<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\Position;
use App\Modules\Core\Models\WorkflowStep;
use App\Modules\Core\Models\WorkflowTemplate;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KepengasuhanSeeder extends Seeder
{
    public function run(): void
    {
        $putraOrg = Organization::where('slug', 'kepengasuhan-putra')->firstOrFail();
        $putriOrg = Organization::where('slug', 'kepengasuhan-putri')->firstOrFail();

        // 1. Buat Asrama
        $asramaPutra = Dormitory::create([
            'id'              => Str::uuid()->toString(),
            'organization_id' => $putraOrg->id,
            'name'            => 'Gedung Sunan Ampel (Putra)',
            'gender'          => 'L',
            'description'     => 'Asrama santri putra utama',
            'is_active'       => true,
        ]);

        $asramaPutri = Dormitory::create([
            'id'              => Str::uuid()->toString(),
            'organization_id' => $putriOrg->id,
            'name'            => 'Gedung Fatimah (Putri)',
            'gender'          => 'P',
            'description'     => 'Asrama santri putri utama',
            'is_active'       => true,
        ]);

        // 2. Buat Kamar
        $kamarPutra1 = Room::create([
            'id'           => Str::uuid()->toString(),
            'dormitory_id' => $asramaPutra->id,
            'name'         => 'Kamar A-01',
            'capacity'     => 10,
            'description'  => 'Kamar Putra A-01',
            'is_active'    => true,
        ]);

        $kamarPutra2 = Room::create([
            'id'           => Str::uuid()->toString(),
            'dormitory_id' => $asramaPutra->id,
            'name'         => 'Kamar A-02',
            'capacity'     => 10,
            'description'  => 'Kamar Putra A-02',
            'is_active'    => true,
        ]);

        $kamarPutri1 = Room::create([
            'id'           => Str::uuid()->toString(),
            'dormitory_id' => $asramaPutri->id,
            'name'         => 'Kamar F-01',
            'capacity'     => 10,
            'description'  => 'Kamar Putri F-01',
            'is_active'    => true,
        ]);

        // 3. Ambil Santri Dummy & Tempatkan di Kamar
        $santriPutra = Person::where('gender', 'L')
            ->whereIn('id', function ($query) use ($putraOrg) {
                $query->select('person_id')
                    ->from('person_roles')
                    ->where('organization_id', $putraOrg->id)
                    ->where('role_type', 'santri');
            })->get();

        foreach ($santriPutra as $index => $santri) {
            $room = ($index < 5) ? $kamarPutra1 : $kamarPutra2;
            RoomAssignment::create([
                'id'          => Str::uuid()->toString(),
                'room_id'     => $room->id,
                'person_id'   => $santri->id,
                'valid_from'  => now()->startOfYear()->toDateString(),
                'valid_until' => null,
                'is_active'   => true,
            ]);
        }

        $santriPutri = Person::where('gender', 'P')
            ->whereIn('id', function ($query) use ($putriOrg) {
                $query->select('person_id')
                    ->from('person_roles')
                    ->where('organization_id', $putriOrg->id)
                    ->where('role_type', 'santri');
            })->get();

        foreach ($santriPutri as $santri) {
            RoomAssignment::create([
                'id'          => Str::uuid()->toString(),
                'room_id'     => $kamarPutri1->id,
                'person_id'   => $santri->id,
                'valid_from'  => now()->startOfYear()->toDateString(),
                'valid_until' => null,
                'is_active'   => true,
            ]);
        }

        // 4. Buat Workflow Template untuk Perizinan
        $musyrifPosition = Position::where('organization_id', $putraOrg->id)->where('name', 'Musyrif')->first();
        $musyrifahPosition = Position::where('organization_id', $putriOrg->id)->where('name', 'Musyrifah')->first();
        $pengasuhPosition = Position::where('name', 'Pengasuh')->first();

        // Template Perizinan Putra
        $templatePutra = WorkflowTemplate::create([
            'id'              => Str::uuid()->toString(),
            'organization_id' => $putraOrg->id,
            'name'            => 'Workflow Perizinan Putra',
            'entity_type'     => 'perizinan',
            'description'     => 'Alur persetujuan izin keluar/pulang santri putra',
            'is_active'       => true,
        ]);

        if ($musyrifPosition) {
            WorkflowStep::create([
                'id'                   => Str::uuid()->toString(),
                'template_id'          => $templatePutra->id,
                'step_order'           => 1,
                'name'                 => 'Persetujuan Musyrif',
                'approver_position_id' => $musyrifPosition->id,
                'action_type'          => 'approve',
                'sla_hours'            => 24,
            ]);
        }

        if ($pengasuhPosition) {
            WorkflowStep::create([
                'id'                   => Str::uuid()->toString(),
                'template_id'          => $templatePutra->id,
                'step_order'           => 2,
                'name'                 => 'Restu Pengasuh',
                'approver_position_id' => $pengasuhPosition->id,
                'action_type'          => 'approve',
                'sla_hours'            => 48,
            ]);
        }

        // Template Perizinan Putri
        $templatePutri = WorkflowTemplate::create([
            'id'              => Str::uuid()->toString(),
            'organization_id' => $putriOrg->id,
            'name'            => 'Workflow Perizinan Putri',
            'entity_type'     => 'perizinan',
            'description'     => 'Alur persetujuan izin keluar/pulang santri putri',
            'is_active'       => true,
        ]);

        if ($musyrifahPosition) {
            WorkflowStep::create([
                'id'                   => Str::uuid()->toString(),
                'template_id'          => $templatePutri->id,
                'step_order'           => 1,
                'name'                 => 'Persetujuan Musyrifah',
                'approver_position_id' => $musyrifahPosition->id,
                'action_type'          => 'approve',
                'sla_hours'            => 24,
            ]);
        }

        if ($pengasuhPosition) {
            WorkflowStep::create([
                'id'                   => Str::uuid()->toString(),
                'template_id'          => $templatePutri->id,
                'step_order'           => 2,
                'name'                 => 'Restu Pengasuh',
                'approver_position_id' => $pengasuhPosition->id,
                'action_type'          => 'approve',
                'sla_hours'            => 48,
            ]);
        }

        $this->command->info('✅ KepengasuhanSeeder: Dormitories, Rooms, Room Assignments, and Workflow Templates seeded.');
    }
}
