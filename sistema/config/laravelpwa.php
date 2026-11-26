<?php

return [
    'name' => 'LaravelPWA',
    'manifest' => [
        // Nombre que se ve al instalar la aplicación
        'name' => 'FARM SJB - Sistema de Venta de Medicamentos',
        'short_name' => 'FARM SJB',
        'start_url' => '/',
        // Colores de la plantilla: fondo blanco y barra oscura como el menú lateral
        'background_color' => '#ffffff',
        'theme_color' => '#212529',
        'display' => 'standalone',
        'orientation'=> 'any',
        'status_bar'=> 'black',
        'icons' => [
            '72x72' => [
                'path' => '/images/icons/icon-72x72.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/images/icons/icon-96x96.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/images/icons/icon-128x128.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/images/icons/icon-144x144.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/images/icons/icon-152x152.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/images/icons/icon-192x192.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/images/icons/icon-384x384.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/images/icons/icon-512x512.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/images/icons/splash-640x1136.png',
            '750x1334' => '/images/icons/splash-750x1334.png',
            '828x1792' => '/images/icons/splash-828x1792.png',
            '1125x2436' => '/images/icons/splash-1125x2436.png',
            '1242x2208' => '/images/icons/splash-1242x2208.png',
            '1242x2688' => '/images/icons/splash-1242x2688.png',
            '1536x2048' => '/images/icons/splash-1536x2048.png',
            '1668x2224' => '/images/icons/splash-1668x2224.png',
            '1668x2388' => '/images/icons/splash-1668x2388.png',
            '2048x2732' => '/images/icons/splash-2048x2732.png',
        ],
        // Accesos directos al mantener presionado el ícono de la aplicación
        'shortcuts' => [
            [
                'name' => 'Nueva venta',
                'description' => 'Registrar una venta',
                'url' => '/sales/create',
                'icons' => [
                    'src' => '/images/icons/icon-96x96.png',
                    'purpose' => 'any'
                ]
            ],
            [
                'name' => 'Medicamentos',
                'description' => 'Ver el stock de medicamentos',
                'url' => '/medicines',
                'icons' => [
                    'src' => '/images/icons/icon-96x96.png',
                    'purpose' => 'any'
                ]
            ],
            [
                'name' => 'Reporte de ventas',
                'description' => 'Ver y descargar el reporte de ventas',
                'url' => '/reports/sales',
                'icons' => [
                    'src' => '/images/icons/icon-96x96.png',
                    'purpose' => 'any'
                ]
            ]
        ],
        'custom' => []
    ]
];
