<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use App\Models\WorkTeam;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TaskExamplesSeeder::class);

        $production = Department::updateOrCreate(
            ['code' => 'PRODUCCION'],
            ['name' => 'Producción', 'description' => 'Departamento de Producción', 'active' => true],
        );
        $maintenance = Department::updateOrCreate(
            ['code' => 'MNT'],
            ['name' => 'Mantenimiento', 'description' => 'Mantenimiento preventivo y correctivo de infraestructura.', 'active' => true],
        );

        foreach ([
            [$production, 'CULTIVO', 'Cultivo'],
            [$maintenance, 'MANT-1', 'Mantenimiento'],
            [$production, 'RIEGO', 'Riego'],
            [$maintenance, 'LIMPIEZA', 'Limpieza'],
        ] as [$department, $code, $name]) {
            WorkTeam::updateOrCreate(
                ['code' => $code],
                ['department_id' => $department->id, 'name' => $name, 'shift' => 'General', 'active' => true],
            );
        }

        $locations = collect();

        foreach (range(1, 10) as $number) {
            $code = sprintf('FLR-%02d', $number);
            $locations[$code] = Location::updateOrCreate(
                ['code' => $code],
                [
                    'name' => "Floración_{$number}",
                    'type' => 'container',
                    'zone' => 'Interior',
                    'description' => "Contenedor de Floración Nº {$number}",
                    'active' => true,
                ],
            );
        }

        foreach ([
            ['MADRES', 'Sala de Madres'],
            ['ESQUEJES', 'Sala de Esquejes'],
            ['FLORA1', 'Sala de Floración 1'],
            ['FLORA3', 'Sala de Floración 3'],
        ] as [$code, $name]) {
            $locations[$code] = Location::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'type' => 'room', 'zone' => $name, 'description' => null, 'active' => true],
            );
        }

        $containerDevices = [
            ['Aire acondicionado B Derecha', 'air_conditioner'],
            ['Deshumidificador A Izquierda', 'dehumidifier'],
            ['Aire acondicionado A Izquierda', 'air_conditioner'],
            ['Deshumidificador B Derecha', 'dehumidifier'],
            ['Ventilador Móvil A', 'fan'],
            ['Ventilador Móvil B', 'fan'],
            ['Ventilador Móvil C', 'fan'],
            ['Ventilador Móvil D', 'fan'],
            ['Turbo A', 'fan'],
            ['Turbo B', 'fan'],
        ];

        foreach (range(1, 10) as $number) {
            $locationCode = sprintf('FLR-%02d', $number);

            foreach ($containerDevices as $index => [$name, $type]) {
                $deviceNumber = $index + 1;
                Asset::updateOrCreate(
                    ['code' => sprintf('%s-D%02d', $locationCode, $deviceNumber)],
                    [
                        'location_id' => $locations[$locationCode]->id,
                        'name' => $name,
                        'type' => $type,
                        'status' => 'operational',
                    ],
                );
            }
        }

        foreach (['MADRES', 'ESQUEJES', 'FLORA1', 'FLORA3'] as $locationCode) {
            foreach ([
                ['AA', 'Aire acondicionado', 'air_conditioner', 3],
                ['DH', 'Deshumidificador', 'dehumidifier', 6],
                ['HM', 'Humidificador', 'humidifier', 1],
            ] as [$prefix, $name, $type, $quantity]) {
                foreach (range(1, $quantity) as $number) {
                    Asset::updateOrCreate(
                        ['code' => sprintf('%s-%s-%02d', $locationCode, $prefix, $number)],
                        [
                            'location_id' => $locations[$locationCode]->id,
                            'name' => "{$name} {$number}",
                            'type' => $type,
                            'status' => 'operational',
                        ],
                    );
                }
            }
        }

        if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            User::updateOrCreate(
                ['email' => env('ADMIN_EMAIL')],
                [
                    'name' => 'Supervisor',
                    'password' => env('ADMIN_PASSWORD'),
                    'is_admin' => true,
                    'role' => 'supervisor',
                    'active' => true,
                ],
            );
        }
    }
}
