<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pisos Permitidos
    |--------------------------------------------------------------------------
    |
    | Lista de todos los pisos disponibles para asignación en el sistema
    | de rotación de personal de recepción.
    |
    */
    'pisos_permitidos' => [
        "PB", "MZ1", "MZ2", "MZ3", 
        "PB-JC", "MZ1-JC", "MZ2-JC", "MZ3-JC",
        "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", 
        "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", 
        "21", "22", "23"
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles de Recepción
    |--------------------------------------------------------------------------
    |
    | Configuración de los roles relacionados con recepción
    |
    */
    'roles' => [
        'recepcion_pb' => 3,
        'recepcion_pisos' => 4,
        'rotacion' => 5
    ],

    /*
    |--------------------------------------------------------------------------
    | Reglas de Rotación
    |--------------------------------------------------------------------------
    |
    | Configuración de las reglas automáticas de cambio de rol
    |
    */
    'reglas' => [
        // Si tiene rol 3 (recepcion_pb) y se asigna a cualquier piso != PB → cambiar a rol 4
        'pb_to_pisos' => [
            'rol_origen' => 3,
            'piso_trigger' => '!PB',
            'rol_destino' => 4
        ],
        // Si tiene rol 4 (recepcion_pisos) y se asigna a PB → cambiar a rol 3
        'pisos_to_pb' => [
            'rol_origen' => 4,
            'piso_trigger' => 'PB',
            'rol_destino' => 3
        ]
    ]
];