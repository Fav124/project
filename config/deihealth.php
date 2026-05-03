<?php

return [
    'obat' => [
        // Batas hari sebelum obat dianggap "hampir kadaluarsa" (default 90 hari / 3 bulan)
        'batas_hampir_kadaluarsa_hari' => env('OBAT_BATAS_HAMPIR_KADALUARSA', 90),
    ],
];
