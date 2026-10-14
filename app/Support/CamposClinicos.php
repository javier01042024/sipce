<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Catálogo de los campos del detalle clínico de cada tipo de paciente.
 * Se usa para construir los reportes (Excel y PDF) mostrando TODA la
 * información del expediente, agrupada por secciones.
 */
class CamposClinicos
{
    /**
     * Devuelve los grupos de campos (seccion => [campo => etiqueta]) para un tipo.
     */
    public static function grupos(string $tipo): array
    {
        return match (self::normalizar($tipo)) {
            'nino' => self::nino(),
            'adolescente' => self::adolescente(),
            'adulto' => self::adulto(),
            default => [],
        };
    }

    /**
     * Etiqueta legible de un campo (buscada en todos los tipos).
     */
    public static function etiqueta(string $campo): string
    {
        return self::todosLosCampos()[$campo] ?? ucwords(str_replace('_', ' ', $campo));
    }

    /**
     * Unión ordenada de todos los campos de todos los tipos: [campo => etiqueta].
     * El orden respeta: primero los campos de niños, luego los exclusivos de
     * adolescentes y adultos (los repetidos no se duplican).
     */
    public static function todosLosCampos(): array
    {
        $union = [];
        foreach ([self::nino(), self::adolescente(), self::adulto()] as $grupos) {
            foreach ($grupos as $campos) {
                foreach ($campos as $campo => $etiqueta) {
                    if (!isset($union[$campo])) {
                        $union[$campo] = $etiqueta;
                    }
                }
            }
        }
        return $union;
    }

    /**
     * Campos (solo las claves) que pertenecen al tipo indicado.
     */
    public static function camposDeTipo(string $tipo): array
    {
        $campos = [];
        foreach (self::grupos($tipo) as $grupo) {
            foreach ($grupo as $campo => $etiqueta) {
                $campos[] = $campo;
            }
        }
        return $campos;
    }

    /**
     * Columnas (campo/etiqueta/grupo) SOLO de un tipo, en orden.
     * Ideal para hojas por tipo sin columnas vacías de otros tipos.
     */
    public static function columnasDeTipo(string $tipo, array $excluir = []): array
    {
        $columnas = [];
        foreach (self::grupos($tipo) as $nombreGrupo => $campos) {
            foreach ($campos as $campo => $etiqueta) {
                if (in_array($campo, $excluir, true)) {
                    continue;
                }
                $columnas[] = ['campo' => $campo, 'etiqueta' => $etiqueta, 'grupo' => $nombreGrupo];
            }
        }
        return $columnas;
    }

    /**
     * Indica si un valor de tipo_paciente corresponde al tipo indicado
     * (ignorando mayúsculas y tildes).
     */
    public static function coincide(string $tipo, string $otro): bool
    {
        return self::normalizar($tipo) === self::normalizar($otro);
    }

    /**
     * Columnas para la tabla "completa": lista ordenada de
     * ['campo' => ..., 'etiqueta' => ..., 'grupo' => ...], sin duplicados.
     * Se pueden excluir campos ya presentes en el resumen superior.
     */
    public static function columnasUnion(array $excluir = []): array
    {
        $columnas = [];
        $vistos = [];
        foreach ([self::nino(), self::adolescente(), self::adulto()] as $grupos) {
            foreach ($grupos as $nombreGrupo => $campos) {
                foreach ($campos as $campo => $etiqueta) {
                    if (isset($vistos[$campo]) || in_array($campo, $excluir, true)) {
                        continue;
                    }
                    $vistos[$campo] = true;
                    $columnas[] = ['campo' => $campo, 'etiqueta' => $etiqueta, 'grupo' => $nombreGrupo];
                }
            }
        }
        return $columnas;
    }

    /**
     * Valor de un campo del detalle listo para mostrar (fechas formateadas, nulos a '').
     */
    public static function valor($detalle, string $campo): string
    {
        if (!$detalle) {
            return '';
        }

        $valor = $detalle->getAttribute($campo);
        if ($valor === null || $valor === '') {
            return '';
        }

        if (in_array($campo, ['fecha_nacimiento', 'fecha_entrevista'], true)) {
            try {
                return Carbon::parse($valor)->format('d/m/Y');
            } catch (\Throwable $e) {
                return (string) $valor;
            }
        }

        return (string) $valor;
    }

    private static function normalizar(string $tipo): string
    {
        return strtolower(str_replace(['í', 'Í', 'ñ', 'Ñ'], ['i', 'i', 'n', 'n'], $tipo));
    }

    private static function nino(): array
    {
        return [
            'Identificación' => [
                'nombre' => 'Nombre',
                'apellido' => 'Apellido',
                'fecha_nacimiento' => 'Fecha de Nacimiento',
                'telefono_contacto' => 'Teléfono de Contacto',
                'grado_instruccion' => 'Grado de Instrucción',
                'maestro' => 'Maestro',
                'direccion' => 'Dirección',
            ],
            'Padres y Convivencia' => [
                'nombre_padre' => 'Nombre del Padre',
                'edad_padre' => 'Edad del Padre',
                'ocupacion_padre' => 'Ocupación del Padre',
                'nombre_madre' => 'Nombre de la Madre',
                'edad_madre' => 'Edad de la Madre',
                'ocupacion_madre' => 'Ocupación de la Madre',
                'hermanos' => 'Hermanos',
                'personas_vive' => 'Personas con quien vive',
            ],
            'Motivo y Desarrollo del Problema' => [
                'desarrollo_problema' => 'Desarrollo del Problema',
                'frecuencia_sintomas' => 'Frecuencia de Síntomas',
                'intensidad_sintomas' => 'Intensidad de Síntomas',
                'duracion_sintomas' => 'Duración de Síntomas',
                'contexto_problema' => 'Contexto del Problema',
                'estrategias_afrontamiento' => 'Estrategias de Afrontamiento',
                'consecuencias' => 'Consecuencias',
                'acontecimientos_estresantes' => 'Acontecimientos Estresantes',
                'expectativas' => 'Expectativas',
            ],
            'Historia Perinatal' => [
                'tipo_parto' => 'Tipo de Parto',
                'causa_parto_inducido' => 'Causa de Parto Inducido',
                'dificultades_parto' => 'Dificultades del Parto',
                'comportamiento_bebe' => 'Comportamiento del Bebé',
                'reacciones_estimulos' => 'Reacciones a Estímulos',
                'situacion_familiar_postnatal' => 'Situación Familiar Postnatal',
                'aceptacion_maternidad' => 'Aceptación de la Maternidad',
            ],
            'Desarrollo Evolutivo' => [
                'desarrollo_areas' => 'Desarrollo por Áreas',
                'habla_lenguaje' => 'Habla y Lenguaje',
                'alimentacion_bebe' => 'Alimentación del Bebé',
                'dificultades_destete' => 'Dificultades de Destete',
                'trastornos_alimentacion' => 'Trastornos de Alimentación',
                'alimentacion_actual' => 'Alimentación Actual',
                'descripcion_dia_anterior' => 'Descripción del Día Anterior',
                'info_sexualidad' => 'Información sobre Sexualidad',
                'desarrollo_sexual' => 'Desarrollo Sexual',
            ],
            'Área Escolar y Conductual' => [
                'tareas_escolares' => 'Tareas Escolares',
                'rendimiento_escolar' => 'Rendimiento Escolar',
                'responsabilidades_domesticas' => 'Responsabilidades Domésticas',
                'recompensas' => 'Recompensas',
                'nivel_actividad' => 'Nivel de Actividad',
                'entretenimiento' => 'Entretenimiento',
                'tipo_actividades' => 'Tipo de Actividades',
                'finalizacion_tareas' => 'Finalización de Tareas',
                'arrebatos' => 'Arrebatos',
                'oposicionismo' => 'Oposicionismo',
                'agresiones' => 'Agresiones',
            ],
            'Relaciones Sociales' => [
                'autoimagen' => 'Autoimagen',
                'percepcion_demas' => 'Percepción de los Demás',
                'relaciones_ninos' => 'Relaciones con Niños',
                'tiene_amigos' => 'Tiene Amigos',
                'actividades_sociales' => 'Actividades Sociales',
                'tipo_ninos_atraen' => 'Tipo de Niños que Atraen',
                'comportamiento_grupo' => 'Comportamiento en Grupo',
                'juegos_preferidos' => 'Juegos Preferidos',
                'relacion_adultos' => 'Relación con Adultos',
                'entretenimiento_casa' => 'Entretenimiento en Casa',
                'actividades_exteriores' => 'Actividades Exteriores',
                'actividades_deportivas' => 'Actividades Deportivas',
                'intereses' => 'Intereses',
            ],
        ];
    }

    private static function adolescente(): array
    {
        return [
            'Identificación' => [
                'nombre' => 'Nombre',
                'apellido' => 'Apellido',
                'cedula' => 'Cédula',
                'fecha_nacimiento' => 'Fecha de Nacimiento',
                'genero' => 'Género',
                'telefono_personal' => 'Teléfono Personal',
                'email_personal' => 'Email Personal',
                'direccion' => 'Dirección',
                'institucion_educativa' => 'Institución Educativa',
                'nivel_educativo' => 'Nivel Educativo',
                'grado_instruccion' => 'Grado de Instrucción',
                'fecha_entrevista' => 'Fecha de Entrevista',
            ],
            'Representante' => [
                'nombre_representante' => 'Nombre del Representante',
                'cedula_representante' => 'Cédula del Representante',
                'telefono_representante' => 'Teléfono del Representante',
                'email_representante' => 'Email del Representante',
                'parentesco' => 'Parentesco',
            ],
            'Padres y Convivencia' => [
                'padre_nombre' => 'Nombre del Padre',
                'padre_edad' => 'Edad del Padre',
                'padre_ocupacion' => 'Ocupación del Padre',
                'madre_nombre' => 'Nombre de la Madre',
                'madre_edad' => 'Edad de la Madre',
                'madre_ocupacion' => 'Ocupación de la Madre',
                'hermanos' => 'Hermanos',
                'personas_vive' => 'Personas con quien vive',
            ],
            'Historia Clínica' => [
                'historia_problema' => 'Historia del Problema',
                'cuando_empezo' => 'Cuándo Empezó',
                'cambios_rendimiento' => 'Cambios en el Rendimiento',
                'frecuencia_sintomas' => 'Frecuencia de Síntomas',
                'intensidad_sintomas' => 'Intensidad de Síntomas',
                'factores_desencadenantes' => 'Factores Desencadenantes',
                'pads_observaciones' => 'Observaciones Adicionales',
            ],
            'Hábitos y Relaciones' => [
                'habitos' => 'Hábitos',
                'relaciones_sociales' => 'Relaciones Sociales',
                'rendimiento_academico' => 'Rendimiento Académico',
                'tiene_amigos' => 'Tiene Amigos',
                'actividades_grupales' => 'Actividades Grupales',
                'autoimagen' => 'Autoimagen',
                'relacion_padres' => 'Relación con los Padres',
                'manejo_emociones' => 'Manejo de Emociones',
                'rechazo_bullying' => 'Rechazo / Bullying',
                'pareja' => 'Pareja',
                'redes_sociales' => 'Redes Sociales',
                'mensajes_dano' => 'Mensajes de Daño',
            ],
            'Salud y Consumo' => [
                'salud_fisica' => 'Salud Física',
                'terapia_anterior' => 'Terapia Anterior',
                'sustancias_frecuencia' => 'Frecuencia de Sustancias',
                'reducir_consumo' => 'Reducir Consumo',
                'molesta_criticas' => 'Le Molestan las Críticas',
                'usar_para_olvidar' => 'Usar para Olvidar',
                'olvidar_lo_que_paso' => 'Olvidar lo que Pasó',
                'orientacion_sexual' => 'Orientación Sexual',
            ],
            'Evaluación Emocional' => [
                'depresion' => 'Depresión',
                'ansiedad_panico' => 'Ansiedad / Pánico',
                'irritabilidad_impulso' => 'Irritabilidad / Impulsividad',
                'conductas_riesgo' => 'Conductas de Riesgo',
            ],
            'Recursos y Fortalezas' => [
                'que_haces_bien' => 'Qué Hace Bien',
                'que_te_da_orgullo' => 'Qué le da Orgullo',
                'quien_te_respalda' => 'Quién le Respalda',
                'cuanto_quieres_cambio' => 'Cuánto Quiere el Cambio',
                'mini_ejercicio' => 'Mini Ejercicio',
            ],
            'Antecedentes y Observaciones' => [
                'antecedentes_familiares' => 'Antecedentes Familiares',
                'observaciones' => 'Observaciones',
            ],
        ];
    }

    private static function adulto(): array
    {
        return [
            'Identificación' => [
                'nombre' => 'Nombre',
                'apellido' => 'Apellido',
                'cedula' => 'Cédula',
                'fecha_nacimiento' => 'Fecha de Nacimiento',
                'genero' => 'Género',
                'estado_civil' => 'Estado Civil',
                'telefono' => 'Teléfono',
                'email' => 'Email',
                'direccion' => 'Dirección',
                'nivel_instruccion' => 'Nivel de Instrucción',
                'fecha_entrevista' => 'Fecha de Entrevista',
            ],
            'Datos Laborales' => [
                'ocupacion' => 'Ocupación',
                'lugar_trabajo' => 'Lugar de Trabajo',
                'telefono_trabajo' => 'Teléfono de Trabajo',
            ],
            'Contacto de Emergencia' => [
                'nombre_emergencia' => 'Nombre de Emergencia',
                'telefono_emergencia' => 'Teléfono de Emergencia',
                'parentesco_emergencia' => 'Parentesco de Emergencia',
            ],
            'Historia Clínica' => [
                'historia_problema' => 'Historia del Problema',
                'frecuencia_sintomas' => 'Frecuencia de Síntomas',
                'intensidad_sintomas' => 'Intensidad de Síntomas',
                'factores_desencadenantes' => 'Factores Desencadenantes',
                'estrategias_afrontamiento' => 'Estrategias de Afrontamiento',
                'sensaciones_corporales' => 'Sensaciones Corporales',
                'autolesiones' => 'Autolesiones',
                'ideacion_suicida' => 'Ideación Suicida',
                'consumo_sustancias' => 'Consumo de Sustancias',
                'estrategias_previas' => 'Estrategias Previas',
                'objetivos_terapia' => 'Objetivos de Terapia',
            ],
            'Antecedentes Psicológicos' => [
                'antecedentes_psiquiatricos' => 'Antecedentes Psiquiátricos',
                'medicamentos_actuales' => 'Medicamentos Actuales',
                'antecedentes_familiares' => 'Antecedentes Familiares',
            ],
            'Historia Familiar y Social' => [
                'convivencia' => 'Convivencia',
                'relacion_familiar' => 'Relación Familiar',
                'conflictos_familiares' => 'Conflictos Familiares',
                'vida_social' => 'Vida Social',
                'pareja' => 'Pareja',
                'redes_sociales' => 'Redes Sociales',
            ],
            'Vida Sexual' => [
                'estado_sexual' => 'Estado Sexual',
                'problemas_sexuales' => 'Problemas Sexuales',
                'actividad_sexual' => 'Actividad Sexual',
                'historial_problemas_sexuales' => 'Historial de Problemas Sexuales',
            ],
            'Hábitos' => [
                'consume_alcohol' => 'Consume Alcohol',
                'consume_tabaco' => 'Consume Tabaco',
                'otras_sustancias' => 'Otras Sustancias',
                'habitos_sueno' => 'Hábitos de Sueño',
            ],
            'Evaluación Emocional' => [
                'estado_animo' => 'Estado de Ánimo',
                'sintomas_depresivos' => 'Síntomas Depresivos',
                'sintomas_ansiedad' => 'Síntomas de Ansiedad',
                'irritabilidad' => 'Irritabilidad',
                'conductas_riesgo' => 'Conductas de Riesgo',
                'sintomas_fisicos' => 'Síntomas Físicos',
                'calidad_vida' => 'Calidad de Vida',
            ],
            'Recursos y Fortalezas' => [
                'fortalezas' => 'Fortalezas',
                'logros' => 'Logros',
                'red_apoyo' => 'Red de Apoyo',
                'motivacion_cambio' => 'Motivación al Cambio',
                'valores_accion' => 'Valores en Acción',
            ],
            'Observaciones' => [
                'observaciones' => 'Observaciones',
            ],
        ];
    }
}
