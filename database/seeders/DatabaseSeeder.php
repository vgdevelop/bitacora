<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use App\Models\WorkTeam;
use App\Models\Location;
use App\Models\Asset;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $operations = Department::updateOrCreate(['code'=>'OPE'], ['name'=>'Operaciones de cultivo','description'=>'Operación diaria y sanidad de las áreas productivas.','active'=>true]);
        $maintenance = Department::updateOrCreate(['code'=>'MNT'], ['name'=>'Mantenimiento','description'=>'Mantenimiento preventivo y correctivo de infraestructura.','active'=>true]);
        WorkTeam::updateOrCreate(['code'=>'CULT-A'], ['department_id'=>$operations->id,'name'=>'Cultivo A','shift'=>'Diurno','active'=>true]);
        WorkTeam::updateOrCreate(['code'=>'MANT-1'], ['department_id'=>$maintenance->id,'name'=>'Mantenimiento 1','shift'=>'General','active'=>true]);
        $flower = Location::updateOrCreate(['code'=>'S-FLO-01'], ['name'=>'Sala de floración 01','type'=>'room','zone'=>'Producción','description'=>'Sala de floración con control ambiental independiente.','active'=>true]);
        $container = Location::updateOrCreate(['code'=>'CNT-02'], ['name'=>'Contenedor de cultivo 02','type'=>'container','zone'=>'Módulos exteriores','description'=>'Módulo productivo climatizado.','active'=>true]);
        Asset::updateOrCreate(['code'=>'AA-FLO-01'], ['location_id'=>$flower->id,'name'=>'Aire acondicionado principal','type'=>'air_conditioner','brand'=>'Equipo HVAC','model'=>'Industrial','status'=>'operational']);
        Asset::updateOrCreate(['code'=>'DH-FLO-01'], ['location_id'=>$flower->id,'name'=>'Deshumidificador 01','type'=>'dehumidifier','brand'=>'Equipo ambiental','status'=>'operational']);
        Asset::updateOrCreate(['code'=>'AA-CNT-02'], ['location_id'=>$container->id,'name'=>'Aire acondicionado de contenedor','type'=>'air_conditioner','status'=>'operational']);
        if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            User::updateOrCreate(['email'=>env('ADMIN_EMAIL')], ['name'=>'Supervisor','password'=>env('ADMIN_PASSWORD'),'is_admin'=>true,'role'=>'supervisor','active'=>true]);
        }
    }
}
