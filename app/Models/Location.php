<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Location extends Model { public const TYPES=['room'=>'Sala','container'=>'Contenedor','service_area'=>'Área de servicio']; protected $fillable=['name','code','type','zone','description','active']; protected function casts():array{return ['active'=>'boolean'];} public function assets():HasMany{return $this->hasMany(Asset::class);} public function workLogs():HasMany{return $this->hasMany(WorkLog::class);} public function getTypeLabelAttribute():string{return self::TYPES[$this->type]??$this->type;} }
