<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
class WorkTeam extends Model { protected $fillable=['department_id','name','code','shift','active']; protected function casts():array{return ['active'=>'boolean'];} public function department():BelongsTo{return $this->belongsTo(Department::class);} public function users():BelongsToMany{return $this->belongsToMany(User::class,'team_user')->withPivot('is_leader')->withTimestamps();} public function workLogs():HasMany{return $this->hasMany(WorkLog::class);} }
