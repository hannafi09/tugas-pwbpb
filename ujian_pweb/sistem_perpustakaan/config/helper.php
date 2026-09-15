<?php
function tanggal_sekarang() {
    return date('Y-m-d');
}

function tgl_indo($tanggal) {
    if (empty($tanggal)) return '-';
    return date('d-m-Y', strtotime($tanggal));
}
?>