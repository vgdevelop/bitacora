<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkLog extends Model
{
    public const STATUSES = ['planned' => 'Planificado', 'in_progress' => 'En curso', 'completed' => 'Completado', 'requires_follow_up' => 'Requiere seguimiento'];

    public const TYPES = ['inspection' => 'Inspección', 'preventive' => 'Mantenimiento preventivo', 'corrective' => 'Mantenimiento correctivo', 'cleaning' => 'Limpieza y sanitización', 'cultivation' => 'Operación de cultivo', 'calibration' => 'Calibración', 'other' => 'Otro'];

    public const PRIORITIES = ['low' => 'Baja', 'normal' => 'Normal', 'high' => 'Alta', 'critical' => 'Crítica'];

    protected $fillable = ['number', 'created_by', 'work_team_id', 'location_id', 'asset_id', 'title', 'task_type_id', 'task_type_name', 'peo_reference', 'work_type', 'priority', 'status', 'description', 'result', 'observations', 'started_at', 'finished_at', 'next_action_at', 'closed_by'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'finished_at' => 'datetime', 'next_action_at' => 'datetime'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(WorkTeam::class, 'work_team_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class);
    }

    public function inputValues(): HasMany
    {
        return $this->hasMany(WorkLogInput::class)->orderBy('id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->task_type_name ?: (self::TYPES[$this->work_type] ?? $this->work_type);
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }
}
