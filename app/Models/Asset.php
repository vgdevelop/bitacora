<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Asset extends Model { public const STATUSES=['operational'=>'Operativo','maintenance'=>'En mantenimiento','warning'=>'Con observaciones','offline'=>'Fuera de servicio']; protected $fillable=['location_id','name','code','type','brand','model','serial_number','status','installed_at','notes']; protected function casts():array{return ['installed_at'=>'date'];} public function location():BelongsTo{return $this->belongsTo(Location::class);} public function workLogs():HasMany{return $this->hasMany(WorkLog::class);} public function getStatusLabelAttribute():string{return self::STATUSES[$this->status]??$this->status;} }
