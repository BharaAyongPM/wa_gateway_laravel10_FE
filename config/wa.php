<?php

return [
    'base_url' => env('WA_API_BASE', 'http://127.0.0.1:5000'),
    'timeout'  => (int) env('WA_TIMEOUT', 15),
    'retry'    => [
        'times' => (int) env('WA_RETRY_TIMES', 2),
        'sleep' => (int) env('WA_RETRY_SLEEP', 200), // ms
    ],

    // Opsi: daftarkan path endpoint agar terstandar
    'endpoints' => [
        'send_message' => '/send-message',
        'send_media'   => '/send-media',
        'send_media_buffer'  => '/device/{session}/send-media-buffer',
        'groups'       => '/groups',
        'create'   => '/device',
        // Tambahan:
        'send_market' => '/send-market',
        'qr'           => '/device/{session}/qr',
        'qr_live'      => '/device/{session}/qrcode-live',
        'status'   => '/device/{session}/info',
        'delete'       => '/device/{session}',
        'send_domain_check' => '/send-domain-check',
        'send_muslim_ai'    => '/send-muslim-ai',
        'send_pinterest'    => '/send-pinterest',
        'send_renungan'     => '/send-renungan',
        'send_tiktok'       => '/send-tiktok',
        'send_animebrat'    => '/send-animebrat',
        'send_twitter'      => '/send-twitter-video',
        'send_pddikti'      => '/send-pddikti',
        'list_groups'      => '/groups',                 // GET  ⬅️ WAJIB ADA
    ],
];
