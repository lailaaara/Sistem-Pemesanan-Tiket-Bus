<?php
$methods = [
    [
        "id"       => "bca_va",
        "nama"     => "BCA Virtual Account",
        "tipe"     => "virtual_account",
        "logo"     => "bca.png",
        "aktif"    => true,
    ],
    [
        "id"       => "bni_va",
        "nama"     => "BNI Virtual Account",
        "tipe"     => "virtual_account",
        "logo"     => "bni.png",
        "aktif"    => true,
    ],
    [
        "id"       => "mandiri_va",
        "nama"     => "Mandiri Virtual Account",
        "tipe"     => "virtual_account",
        "logo"     => "mandiri.png",
        "aktif"    => true,
    ],
    [
        "id"       => "gopay",
        "nama"     => "GoPay",
        "tipe"     => "ewallet",
        "logo"     => "gopay.png",
        "aktif"    => true,
    ],
    [
        "id"       => "ovo",
        "nama"     => "OVO",
        "tipe"     => "ewallet",
        "logo"     => "ovo.png",
        "aktif"    => true,
    ],
    [
        "id"       => "dana",
        "nama"     => "DANA",
        "tipe"     => "ewallet",
        "logo"     => "dana.png",
        "aktif"    => true,
    ],
    [
        "id"       => "transfer",
        "nama"     => "Transfer Bank",
        "tipe"     => "transfer",
        "logo"     => "bank.png",
        "aktif"    => true,
    ],
];
response("success", "Metode pembayaran tersedia", $methods);
