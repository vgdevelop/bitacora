<?php

namespace Database\Seeders;

use App\Models\TaskType;
use Illuminate\Database\Seeder;

class TaskExamplesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'inspection' => ['Inspección general', 'PEO-OPS-001', 'Inspección visual de salas y equipos', [
                ['condicion_inicial', 'Condición inicial del equipo', 'select', 'start', null, ['Operativo', 'Con observaciones', 'Fuera de servicio']],
                ['temperatura', 'Temperatura observada', 'number', 'finish', '°C'], ['humedad_relativa', 'Humedad relativa', 'number', 'finish', '% HR'],
                ['ruidos_vibraciones', '¿Presenta ruidos o vibraciones anormales?', 'boolean', 'finish'], ['hallazgos', 'Hallazgos de la inspección', 'textarea', 'finish'], ['accion_recomendada', 'Acción recomendada', 'textarea', 'finish'],
            ]],
            'preventive' => ['Mantenimiento preventivo', 'PEO-MNT-001', 'Mantenimiento preventivo de equipos', [
                ['horometro_inicial', 'Lectura de horómetro', 'number', 'start', 'h'], ['estado_previo', 'Estado previo', 'select', 'start', null, ['Operativo', 'Con observaciones', 'Detenido']],
                ['componentes_intervenidos', 'Componentes intervenidos', 'textarea', 'finish'], ['limpieza_realizada', '¿Se realizó limpieza?', 'boolean', 'finish'], ['ajustes_realizados', 'Ajustes realizados', 'textarea', 'finish'], ['repuestos_consumibles', 'Repuestos o consumibles utilizados', 'textarea', 'finish'], ['prueba_funcional', 'Resultado de prueba funcional', 'select', 'finish', null, ['Conforme', 'Con observaciones', 'No conforme']],
            ]],
            'corrective' => ['Mantenimiento correctivo', 'PEO-MNT-002', 'Diagnóstico y reparación de averías', [
                ['falla_reportada', 'Falla reportada', 'textarea', 'start'], ['equipo_detenido', '¿El equipo está detenido?', 'boolean', 'start'], ['alarma_codigo', 'Código o mensaje de alarma', 'text', 'start'],
                ['diagnostico', 'Diagnóstico / causa probable', 'textarea', 'finish'], ['accion_correctiva', 'Acción correctiva realizada', 'textarea', 'finish'], ['repuestos_utilizados', 'Repuestos utilizados', 'textarea', 'finish'], ['prueba_final', 'Prueba final', 'select', 'finish', null, ['Conforme', 'Con observaciones', 'No conforme']], ['requiere_seguimiento', '¿Requiere seguimiento?', 'boolean', 'finish'],
            ]],
            'cleaning' => ['Limpieza y sanitización', 'PEO-LIM-001', 'Limpieza y sanitización de equipos', [
                ['estado_suciedad', 'Estado inicial', 'select', 'start', null, ['Limpio', 'Suciedad leve', 'Suciedad visible', 'Contaminación sospechada']], ['producto', 'Producto utilizado', 'text', 'finish'], ['lote_producto', 'Lote del producto', 'text', 'finish'], ['concentracion', 'Concentración preparada', 'number', 'finish', '%'], ['tiempo_contacto', 'Tiempo de contacto', 'number', 'finish', 'min'], ['resultado_visual', 'Inspección visual final', 'select', 'finish', null, ['Conforme', 'Con observaciones', 'Repetir limpieza']],
            ]],
            'cultivation' => ['Operación de cultivo', 'PEO-PRO-001', 'Operaciones rutinarias de cultivo', [
                ['actividad', 'Actividad realizada', 'select', 'start', null, ['Poda', 'Defoliación', 'Tutorado', 'Trasplante', 'Riego manual', 'Revisión general', 'Otra']], ['cultivar_lote', 'Cultivar o lote', 'text', 'start'], ['cantidad_plantas', 'Cantidad de plantas intervenidas', 'number', 'finish', 'plantas'], ['material_retirado', 'Material vegetal retirado', 'textarea', 'finish'], ['incidencias_cultivo', 'Incidencias observadas', 'textarea', 'finish'],
            ]],
            'calibration' => ['Calibración', 'PEO-CAL-001', 'Verificación y calibración de instrumentos', [
                ['patron_referencia', 'Patrón o instrumento de referencia', 'text', 'start'], ['identificacion_patron', 'Identificación/certificado del patrón', 'text', 'start'], ['lectura_antes', 'Lectura antes del ajuste', 'number', 'finish'], ['lectura_patron', 'Valor de referencia', 'number', 'finish'], ['lectura_despues', 'Lectura después del ajuste', 'number', 'finish'], ['resultado_calibracion', 'Resultado', 'select', 'finish', null, ['Conforme', 'Ajustado y conforme', 'No conforme', 'Fuera de servicio']],
            ]],
            'other' => ['Otra tarea', null, null, [['motivo', 'Motivo de la intervención', 'text', 'start'], ['detalle_adicional', 'Detalle adicional', 'textarea', 'finish'], ['estado_final', 'Estado final', 'select', 'finish', null, ['Conforme', 'Con observaciones', 'Pendiente']]]],
            'environmental_check' => ['Control ambiental', 'PEO-AMB-001', 'Control de condiciones ambientales', [
                ['temperatura', 'Temperatura', 'number', 'finish', '°C'], ['humedad_relativa', 'Humedad relativa', 'number', 'finish', '% HR'], ['co2', 'Concentración de CO₂', 'number', 'finish', 'ppm'], ['presion_diferencial', 'Presión diferencial', 'number', 'finish', 'Pa'], ['consigna_temperatura', 'Consigna de temperatura', 'number', 'finish', '°C'], ['desviacion', '¿Se detectó una desviación?', 'boolean', 'finish'], ['accion_inmediata', 'Acción inmediata tomada', 'textarea', 'finish'],
            ]],
            'irrigation_check' => ['Control de riego y fertirriego', 'PEO-RIE-001', 'Preparación y verificación de solución de riego', [
                ['tanque_linea', 'Tanque o línea utilizada', 'text', 'start'], ['volumen_preparado', 'Volumen preparado', 'number', 'finish', 'L'], ['ph_inicial', 'pH inicial', 'number', 'finish', 'pH'], ['ph_final', 'pH final', 'number', 'finish', 'pH'], ['ec_final', 'Conductividad final', 'number', 'finish', 'mS/cm'], ['temperatura_solucion', 'Temperatura de solución', 'number', 'finish', '°C'], ['incidencias', 'Incidencias o correcciones', 'textarea', 'finish'],
            ]],
            'pest_monitoring' => ['Monitoreo de plagas', 'PEO-MIP-001', 'Monitoreo integrado de plagas', [
                ['sector_revisado', 'Sector o punto revisado', 'text', 'start'], ['tipo_evidencia', 'Tipo de evidencia', 'select', 'finish', null, ['Sin evidencia', 'Insecto adulto', 'Larva', 'Huevo', 'Daño vegetal', 'Hongo', 'Otro']], ['organismo', 'Organismo identificado o sospechado', 'text', 'finish'], ['cantidad', 'Cantidad observada', 'number', 'finish'], ['severidad', 'Severidad', 'select', 'finish', null, ['Nula', 'Baja', 'Media', 'Alta']], ['medida_tomada', 'Medida inmediata tomada', 'textarea', 'finish'],
            ]],
            'filter_replacement' => ['Cambio o limpieza de filtros', 'PEO-MNT-003', 'Gestión de filtros HVAC y ambientales', [
                ['tipo_filtro', 'Tipo de filtro', 'select', 'start', null, ['Prefiltro', 'Filtro de bolsa', 'HEPA', 'Carbón activado', 'Otro']], ['estado_retirado', 'Estado del filtro retirado', 'select', 'finish', null, ['Normal', 'Saturado', 'Dañado', 'Húmedo', 'Contaminado']], ['accion_filtro', 'Acción realizada', 'select', 'finish', null, ['Limpieza', 'Cambio', 'Recolocación']], ['referencia_nuevo', 'Referencia/lote del filtro nuevo', 'text', 'finish'], ['diferencial_final', 'Presión diferencial final', 'number', 'finish', 'Pa'], ['prueba_operativa', 'Prueba operativa', 'select', 'finish', null, ['Conforme', 'Con observaciones', 'No conforme']],
            ]],
            'alarm_response' => ['Atención de alarma', 'PEO-MNT-004', 'Respuesta ante alarmas de equipos e instalaciones', [
                ['codigo_alarma', 'Código o mensaje de alarma', 'text', 'start'], ['condicion_encontrada', 'Condición encontrada', 'textarea', 'start'], ['alarma_critica', '¿Alarma crítica?', 'boolean', 'start'], ['causa', 'Causa identificada', 'textarea', 'finish'], ['accion_tomada', 'Acción tomada', 'textarea', 'finish'], ['alarma_restablecida', '¿Alarma restablecida?', 'boolean', 'finish'], ['escalado_a', 'Escalado o comunicado a', 'text', 'finish'],
            ]],
        ];

        foreach ($types as $code => [$name,$peoCode,$peoTitle,$inputs]) {
            $type = TaskType::firstOrCreate(['code' => $code], ['name' => $name, 'active' => true]);
            if (! $type->peo_code && $peoCode) {
                $type->update(['name' => $name, 'peo_code' => $peoCode, 'peo_title' => $peoTitle, 'peo_version' => 'Ejemplo', 'instructions' => 'Consultar y seguir el PEO físico vigente antes de realizar la tarea.']);
            }
            foreach ($inputs as $order => $definition) {
                [$key,$label,$fieldType,$phase,$unit,$options] = array_pad($definition, 6, null);
                $type->inputs()->firstOrCreate(['key' => $key], ['label' => $label, 'field_type' => $fieldType, 'phase' => $phase, 'unit' => $unit, 'options' => $options, 'required' => false, 'active' => true, 'sort_order' => ($order + 1) * 10]);
            }
        }
    }
}
