<?php
$result = pg_query($conn, "
    SELECT DISTINCT kelas FROM bus WHERE kelas IS NOT NULL AND kelas != ''
");
$categories = [];
while ($row = pg_fetch_assoc($result)) {
    $categories[] = [
        "id" => strtolower($row['kelas']),
        "nama" => $row['kelas']
    ];
}
if (empty($categories)) {
    $categories = [
        ["id" => "ekonomi", "nama" => "Ekonomi"],
        ["id" => "eksekutif", "nama" => "Eksekutif"],
        ["id" => "vip", "nama" => "VIP"],
    ];
}
response("success", "Kategori layanan bus", $categories);
