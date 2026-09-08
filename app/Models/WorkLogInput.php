<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkLogInput extends Model
{
    protected $fillable = ['work_log_id', 'task_input_id', 'input_key', 'input_label', 'field_type', 'phase', 'unit', 'value', 'recorded_by', 'recorded_at'];

    protected function casts(): array
    {
        return ['recorded_at' => 'datetime'];
    }

    public function workLog(): BelongsTo
    {
        return $this->belongsTo(WorkLog::class);
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(TaskInput::class, 'task_input_id');
    }
}
