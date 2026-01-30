<!DOCTYPE html>
<html>
<head>
    <title>Pemesanan Tiket Online</title>
    <style>
        .border-box { border: 1px solid #7fb3d5; padding: 20px; width: 450px; font-family: sans-serif; }
        .row { margin-bottom: 10px; }
        label { display: inline-block; width: 150px; }
    </style>
</head>
<body>

<div class="border-box">
    <h4 style="text-align:center">TIKET ONLINE JAKARTA - MALAYSIA</h4>
    <form action="proses.php" method="POST">
        <div class="row">
            <label>Nama</label>
            <input type="text" name="nama" required>
        </div>
        <div class="row">
            <label>Pilih Kode Pesawat</label>
            <select name="kode_pesawat">
                <option value="GRD">GRD</option>
                <option value="MPT">MPT</option>
                <option value="BTV">BTV</option>
            </select>
        </div>
        <div class="row">
            <label>Pilih Kelas</label>
            <input type="radio" name="kelas" value="Eksekutif" checked> Eksekutif <br>
            <label></label><input type="radio" name="kelas" value="Bisnis"> Bisnis <br>
            <label></label><input type="radio" name="kelas" value="Ekonomi"> Ekonomi
        </div>
        <div class="row">
            <label>Jumlah Tiket</label>
            <input type="number" name="jumlah" min="1" value="1">
        </div>
        <div class="row">
            <label></label>
            <button type="submit" name="simpan">SIMPAN</button>
            <button type="reset">BATAL</button>
        </div>
    </form>
</div>

</body>
</html>