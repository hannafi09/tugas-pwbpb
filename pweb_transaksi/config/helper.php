<?php
// Set zona waktu ke Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');

/**
 * Fungsi untuk memformat tanggal ke format Indonesia (Contoh: 31 Agustus 2026)
 */
function tgl_indo($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00') {
        return '-';
    }

    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    
    // Hasil: Tanggal Bulan Tahun
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

/**
 * Fungsi untuk mengambil tanggal & waktu saat ini (Presisi sampai detik)
 */
function waktu_sekarang() {
    return date('Y-m-d H:i:s');
}

/**
 * Fungsi untuk mengambil tanggal hari ini saja (Presisi YYYY-MM-DD)
 */
function tanggal_sekarang() {
    return date('Y-m-d');
}
?>