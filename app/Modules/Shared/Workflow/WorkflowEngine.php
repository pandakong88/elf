<?php

namespace App\Modules\Shared\Workflow;

use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\WorkflowInstance;
use App\Modules\Core\Models\WorkflowInstanceLog;
use App\Modules\Core\Models\WorkflowStep;
use App\Modules\Core\Models\WorkflowTemplate;
use DomainException;
use Illuminate\Support\Facades\DB;

class WorkflowEngine
{
    /**
     * Mulai workflow baru untuk sebuah entity.
     *
     * @param  string  $templateId  UUID dari workflow template yang dipakai
     * @param  string  $entityType  Tipe entity ('perizinan', 'pengajuan', dll)
     * @param  string  $entityId    UUID dari entity terkait
     * @param  Person  $initiator   Orang yang memulai workflow
     * @return WorkflowInstance
     *
     * @throws DomainException
     */
    public function initiate(
        string $templateId,
        string $entityType,
        string $entityId,
        Person $initiator
    ): WorkflowInstance {
        $template = WorkflowTemplate::active()->findOrFail($templateId);

        // Pastikan template punya minimal 1 step
        if ($template->steps()->count() === 0) {
            throw new DomainException(
                "Template '{$template->name}' belum memiliki langkah workflow."
            );
        }

        // Pastikan entity ini belum punya workflow aktif
        $existingActive = WorkflowInstance::byEntity($entityType, $entityId)
                                          ->pending()
                                          ->exists();
        if ($existingActive) {
            throw new DomainException(
                "Entity {$entityType} #{$entityId} sudah memiliki workflow yang sedang berjalan."
            );
        }

        return DB::transaction(function () use ($template, $entityType, $entityId, $initiator) {
            $instance = WorkflowInstance::create([
                'template_id'  => $template->id,
                'entity_type'  => $entityType,
                'entity_id'    => $entityId,
                'current_step' => 1,
                'status'       => 'in_progress',
                'initiated_by' => $initiator->id,
            ]);

            WorkflowInstanceLog::create([
                'instance_id' => $instance->id,
                'step_order'  => 1,
                'action'      => 'submitted',
                'actor_id'    => $initiator->id,
                'notes'       => null,
                'created_at'  => now(),
            ]);

            return $instance;
        });
    }

    /**
     * Advance workflow ke langkah berikutnya (approve step saat ini).
     *
     * @throws DomainException
     */
    public function advance(
        WorkflowInstance $instance,
        Person $actor,
        ?string $notes = null
    ): WorkflowInstance {
        $this->guardFinished($instance);

        $currentStep = $this->getCurrentStep($instance);

        return DB::transaction(function () use ($instance, $actor, $notes, $currentStep) {
            // Catat log
            WorkflowInstanceLog::create([
                'instance_id' => $instance->id,
                'step_order'  => $currentStep->step_order,
                'action'      => 'approved',
                'actor_id'    => $actor->id,
                'notes'       => $notes,
                'created_at'  => now(),
            ]);

            // Cek apakah ini step terakhir
            if ($currentStep->isLastStep()) {
                $instance->update(['status' => 'approved']);
            } else {
                $nextStep = $currentStep->nextStep();
                $instance->update(['current_step' => $nextStep->step_order]);
            }

            return $instance->fresh();
        });
    }

    /**
     * Tolak workflow (reject) dari step mana saja.
     *
     * @throws DomainException
     */
    public function reject(
        WorkflowInstance $instance,
        Person $actor,
        string $notes
    ): WorkflowInstance {
        $this->guardFinished($instance);

        if (empty($notes)) {
            throw new DomainException('Alasan penolakan wajib diisi.');
        }

        $currentStep = $this->getCurrentStep($instance);

        return DB::transaction(function () use ($instance, $actor, $notes, $currentStep) {
            WorkflowInstanceLog::create([
                'instance_id' => $instance->id,
                'step_order'  => $currentStep->step_order,
                'action'      => 'rejected',
                'actor_id'    => $actor->id,
                'notes'       => $notes,
                'created_at'  => now(),
            ]);

            $instance->update(['status' => 'rejected']);

            return $instance->fresh();
        });
    }

    /**
     * Batalkan workflow (cancel) oleh initiator atau admin.
     *
     * @throws DomainException
     */
    public function cancel(
        WorkflowInstance $instance,
        Person $actor,
        ?string $notes = null
    ): WorkflowInstance {
        $this->guardFinished($instance);

        return DB::transaction(function () use ($instance, $actor, $notes) {
            WorkflowInstanceLog::create([
                'instance_id' => $instance->id,
                'step_order'  => $instance->current_step,
                'action'      => 'noted',
                'actor_id'    => $actor->id,
                'notes'       => $notes ?? 'Workflow dibatalkan.',
                'created_at'  => now(),
            ]);

            $instance->update(['status' => 'cancelled']);

            return $instance->fresh();
        });
    }

    /**
     * Ambil step yang sedang aktif.
     *
     * @throws DomainException
     */
    public function getCurrentStep(WorkflowInstance $instance): WorkflowStep
    {
        $step = WorkflowStep::where('template_id', $instance->template_id)
                            ->where('step_order', $instance->current_step)
                            ->first();

        if (! $step) {
            throw new DomainException(
                "Step #{$instance->current_step} tidak ditemukan di template workflow."
            );
        }

        return $step;
    }

    /**
     * Ambil seluruh history log dari sebuah instance.
     */
    public function getHistory(WorkflowInstance $instance): \Illuminate\Database\Eloquent\Collection
    {
        return $instance->logs()->with('actor')->get();
    }

    /**
     * Guard: pastikan workflow belum selesai sebelum diproses.
     *
     * @throws DomainException
     */
    private function guardFinished(WorkflowInstance $instance): void
    {
        if ($instance->isFinished()) {
            throw new DomainException(
                "Workflow ini sudah berstatus '{$instance->status}' dan tidak bisa diproses lagi."
            );
        }
    }
}
