<?php

return [
    'environments' => [
        'development' => [
            'base_url' => env('QR_BASE_URL', 'http://localhost:8000/qr/'),
            'expiry_days' => env('QR_EXPIRY_DAYS', 7),
            'expiry_hours' => env('QR_EXPIRY_HOURS', 24),
        ],
        'staging' => [
            'base_url' => 'https://staging-barcode.lspgatensi.id/',
            'expiry_days' => 14,
            'expiry_hours' => 48,
        ],
        'production' => [
            'base_url' => 'https://barcode.lspgatensi.id/',
            'expiry_days' => 30,
            'expiry_hours' => 72,
        ],
    ],

    'current_environment' => env('QR_ENVIRONMENT', 'development'),

    // SIMPLE SETUP: Universal expiry for ALL QR types (EASIEST)
    // If QR_EXPIRY_DAYS is set, ALL QR types will use this value
    'universal_expiry_days' => env('QR_EXPIRY_DAYS', null), // null = use individual settings

    // EASY CUSTOMIZATION: Define expiry settings per QR type
    // Format: 'type' => ['value' => number, 'unit' => 'unit_name']
    // Available units: 'minutes', 'hours', 'days', 'weeks', 'months', 'years'
    'expiry_settings' => [
        'verifikator1' => ['value' => env('QR_EXPIRY_VERIFIKATOR1_DAYS', 365), 'unit' => 'days'],
        'verifikator2' => ['value' => env('QR_EXPIRY_VERIFIKATOR2_DAYS', 365), 'unit' => 'days'],
        'ketua_tuk' => ['value' => env('QR_EXPIRY_KETUA_TUK_DAYS', 365), 'unit' => 'days'],
        'direktur' => ['value' => env('QR_EXPIRY_DIREKTUR_DAYS', 365), 'unit' => 'days'],
        'asesi' => ['value' => env('QR_EXPIRY_ASESI_DAYS', 365), 'unit' => 'days'],
        'document' => ['value' => env('QR_EXPIRY_DOCUMENT_HOURS', 24), 'unit' => 'hours'],

        // Advanced examples for quick customization:
        'custom_short' => ['value' => env('QR_EXPIRY_CUSTOM_MINUTES', 60), 'unit' => 'minutes'],
        'custom_medium' => ['value' => env('QR_EXPIRY_CUSTOM_HOURS', 48), 'unit' => 'hours'],
        'custom_long' => ['value' => env('QR_EXPIRY_CUSTOM_WEEKS', 2), 'unit' => 'weeks'],
        'custom_extended' => ['value' => env('QR_EXPIRY_CUSTOM_MONTHS', 3), 'unit' => 'months'],
        'custom_permanent' => ['value' => env('QR_EXPIRY_CUSTOM_YEARS', 1), 'unit' => 'years'],
    ],

    // QUICK SETUP: Predefined expiry templates for easy configuration
    // Just change the template name in your .env file: QR_EXPIRY_TEMPLATE=strict
    'templates' => [
        'development' => [
            'verifikator1' => ['value' => 1, 'unit' => 'hours'],
            'verifikator2' => ['value' => 1, 'unit' => 'hours'],
            'ketua_tuk' => ['value' => 30, 'unit' => 'minutes'],
            'direktur' => ['value' => 2, 'unit' => 'hours'],
            'asesi' => ['value' => 15, 'unit' => 'minutes'],
            'document' => ['value' => 10, 'unit' => 'minutes'],
        ],
        'testing' => [
            'verifikator1' => ['value' => 30, 'unit' => 'minutes'],
            'verifikator2' => ['value' => 30, 'unit' => 'minutes'],
            'ketua_tuk' => ['value' => 15, 'unit' => 'minutes'],
            'direktur' => ['value' => 1, 'unit' => 'hours'],
            'asesi' => ['value' => 10, 'unit' => 'minutes'],
            'document' => ['value' => 5, 'unit' => 'minutes'],
        ],
        'standard' => [
            'verifikator1' => ['value' => 7, 'unit' => 'days'],
            'verifikator2' => ['value' => 7, 'unit' => 'days'],
            'ketua_tuk' => ['value' => 5, 'unit' => 'days'],
            'direktur' => ['value' => 30, 'unit' => 'days'],
            'asesi' => ['value' => 3, 'unit' => 'days'],
            'document' => ['value' => 24, 'unit' => 'hours'],
        ],
        'strict' => [
            'verifikator1' => ['value' => 3, 'unit' => 'days'],
            'verifikator2' => ['value' => 3, 'unit' => 'days'],
            'ketua_tuk' => ['value' => 2, 'unit' => 'days'],
            'direktur' => ['value' => 7, 'unit' => 'days'],
            'asesi' => ['value' => 1, 'unit' => 'days'],
            'document' => ['value' => 4, 'unit' => 'hours'],
        ],
        'relaxed' => [
            'verifikator1' => ['value' => 14, 'unit' => 'days'],
            'verifikator2' => ['value' => 14, 'unit' => 'days'],
            'ketua_tuk' => ['value' => 10, 'unit' => 'days'],
            'direktur' => ['value' => 60, 'unit' => 'days'],
            'asesi' => ['value' => 7, 'unit' => 'days'],
            'document' => ['value' => 72, 'unit' => 'hours'],
        ],
    ],

    // Template selector from .env
    'current_template' => env('QR_EXPIRY_TEMPLATE', null), // null = use individual settings
];