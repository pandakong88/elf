<?php

namespace App\Modules\Kepengasuhan\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class CensusV3Campaign extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'census_campaigns';

    protected $fillable = [
        'name', 'description', 'template_id',
        'month', 'year',
        'target_scope',
        'workflow_mode', 'allow_excel', 'allow_direct_input',
        'deadline', 'status', 'opened_at', 'closed_at',
        'created_by', 'approved_by',
    ];

    protected $casts = [
        'month'              => 'integer',
        'year'               => 'integer',
        'allow_excel'        => 'boolean',
        'allow_direct_input' => 'boolean',
        'deadline'           => 'date',
        'opened_at'          => 'datetime',
        'closed_at'          => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function template(): BelongsTo
    {
        return $this->belongsTo(CensusTemplate::class, 'template_id');
    }

    public function dormitories(): HasMany
    {
        return $this->hasMany(CensusV3CampaignDormitory::class, 'campaign_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(CensusV3Response::class, 'campaign_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['active', 'collecting']);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'draft'      => 'Draft',
            'active'     => 'Aktif',
            'collecting' => 'Sedang Berjalan',
            'review'     => 'Menunggu Review',
            'closed'     => 'Selesai',
            default      => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'draft'      => 'gray',
            'active'     => 'blue',
            'collecting' => 'yellow',
            'review'     => 'purple',
            'closed'     => 'green',
            default      => 'gray',
        };
    }

    public function getWorkflowLabel(): string
    {
        return match ($this->workflow_mode) {
            'admin_only'  => 'Admin Input Sendiri',
            'distributed' => 'Distribusi ke Ketua Komplek',
            'excel'       => 'Via Excel',
            'hybrid'      => 'Kombinasi',
            default       => $this->workflow_mode,
        };
    }

    public function getMonthName(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',    4 => 'April',
            5 => 'Mei',     6 => 'Juni',     7 => 'Juli',      8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $months[$this->month] ?? $this->month;
    }

    public function getOverallProgress(): array
    {
        $total = $this->dormitories()->count();
        $done  = $this->dormitories()->whereIn('status', ['submitted', 'approved'])->count();
        return [
            'total'      => $total,
            'done'       => $done,
            'percentage' => $total > 0 ? round(($done / $total) * 100) : 0,
        ];
    }
}
