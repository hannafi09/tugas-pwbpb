<?php
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $kode = $_POST['kode_pesawat'];
    $kelas = $_POST['kelas'];
    $jumlah = $_POST['jumlah'];

    // Logika Penentuan Nama Pesawat dan Harga
    $nama_pesawat = "";
    $harga_tiket = 0;

    if ($kode == "GRD") {
        $nama_pesawat = "Garuda";
        if ($kelas == "Eksekutif") $harga_tiket = 1500000;
        elseif ($kelas == "Bisnis") $harga_tiket = 900000;
        else $harga_tiket = 500000;
    } 
    elseif ($kode == "MPT") {
        $nama_pesawat = "Merpati";
        if ($kelas == "Eksekutif") $harga_tiket = 1200000;
        elseif ($kelas == "Bisnis") $harga_tiket = 800000;
        else $harga_tiket = 400000;
    } 
    elseif ($kode == "BTV") {
        $nama_pesawat = "Batavia";
        if ($kelas == "Eksekutif") $harga_tiket = 1000000;
        elseif ($kelas == "Bisnis") $harga_tiket = 700000;
        else $harga_tiket = 300000;
    }

    $total_bayar = $harga_tiket * $jumlah;

    // Tampilan Output sesuai gambar kedua
    echo "<div style='border: 1px solid #7fb3d5; padding: 20px; width: 500px; font-family: monospace;'>";
    echo "=========================================================<br>";
    echo "<b>PEMESANAN TIKET ONLINE JAKARTA - MALAYSIA</b><br>";
    echo "=========================================================<br>";
    echo "Nama         : $nama <br>";
    echo "Nama Pesawat : $nama_pesawat <br>";
    echo "Kelas        : $kelas <br>";
    echo "Harga Tiket  : $harga_tiket <br>";
    echo "Jumlah Tiket : $jumlah <br>";
    echo "Total Bayar  : $total_bayar <br>";
    echo "=========================================================<br>";
    echo "<br><center>-oo0oo-</center>";
    echo "</div>";
    echo "<br><a href='index.php'>Kembali</a>";
}
?>