<?php
return [
    // endpoint API Ryzumi (punya kamu)
    'api_base'   => env('CEKRESI_API_BASE', 'https://api.ryzumi.vip/api/tool/cek-resi'),

    // lama sesi interaktif (menit)
    'ttl_minutes' => (int) env('CEKRESI_TTL', 15),

    // jumlah item per halaman list menu
    'page_size'   => (int) env('CEKRESI_PAGE_SIZE', 8),

    // daftar ekspedisi (urut = nomor menu)
    'options' => [
        ['key' => 'shopee-express',           'label' => 'Shopee Express (SPX)',          'aliases' => ['spx','shopee','shopeexpres','spxid']],
        ['key' => 'anter-aja',                'label' => 'AnterAja',                       'aliases' => ['anteraja','anter aja','aa']],
        ['key' => 'tiki',                     'label' => 'TIKI',                           'aliases' => []],
        ['key' => 'pos-indonesia',            'label' => 'Pos Indonesia',                  'aliases' => ['pos','kantor pos']],
        ['key' => 'lion-parcel',              'label' => 'Lion Parcel',                    'aliases' => ['lion','lp']],
        ['key' => 'ninja',                    'label' => 'Ninja Xpress',                   'aliases' => ['ninja xpress','ninjavan','ninja van']],
        ['key' => 'paxel',                    'label' => 'Paxel',                          'aliases' => []],
        ['key' => 'pcp-express',              'label' => 'PCP Express',                    'aliases' => ['pcp']],
        ['key' => 'indah-logistik-cargo',     'label' => 'Indah Logistik Cargo',           'aliases' => ['indah','indah cargo']],
        ['key' => 'sap-express',              'label' => 'SAP Express',                    'aliases' => ['sap']],
        ['key' => 'acommerce',                'label' => 'aCommerce',                      'aliases' => []],
        ['key' => 'grab-express',             'label' => 'Grab Express',                   'aliases' => ['grab']],
        ['key' => 'gtl-goto-logistics',       'label' => 'GoTo Logistics (GTL)',           'aliases' => ['goto','gtl']],
        ['key' => 'janio-asia',               'label' => 'Janio Asia',                      'aliases' => ['janio','janio asia']],
        ['key' => 'lazada-express-lex',       'label' => 'Lazada Express (LEX)',           'aliases' => ['lex','lazada']],
        ['key' => 'lazada-logistics',         'label' => 'Lazada Logistics',               'aliases' => []],
        ['key' => 'nss-express',              'label' => 'NSS Express',                    'aliases' => []],
        ['key' => 'luar-negeri-bea-cukai',    'label' => 'Bea Cukai (Intl)',               'aliases' => ['bea cukai','customs']],
        ['key' => 'jet-express',              'label' => 'J&T Express (JET)',              'aliases' => ['j&t','jet','jnt','j&t express']],
        ['key' => 'rcl-red-carpet-logistics', 'label' => 'RCL (Red Carpet Logistics)',     'aliases' => ['rcl']],
        ['key' => 'standard-express-lwe',     'label' => 'Standard Express (LWE)',         'aliases' => ['lwe','standard']],
        ['key' => 'pt-ncs',                   'label' => 'NCS',                             'aliases' => ['ncs']],
        ['key' => 'qrim-express',             'label' => 'QRIM Express',                    'aliases' => ['qrim']],
        // Tambah/kurangi sesuai preferensi
    ],
];
