<?php 
include 'config.php'; 
 $search = ''; 
if (isset($_POST['search'])) { 
    $search = $_POST['search']; 
} 
 $sql = "SELECT * FROM data_sekolah WHERE nama LIKE '%$search%' OR sekolah LIKE 
'%$search%' OR jurusan LIKE '%$search%'"; 
$result = $conn->query($sql); 
 
if (isset($_GET['delete'])) { 
    $id = $_GET['delete']; 
    $conn->query("DELETE FROM data_sekolah WHERE id=$id");     header('Location: index.php'); 
} 
?> 
 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Daftar Siswa</title> 
    <style>         body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 
0; padding: 20px; } 
        h2 { text-align: center; color: #333; } 
        table { width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #fff; } 
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; 
} 
        th { background-color: #4CAF50; color: white; }         tr:hover { background-color: #f5f5f5; } 
        button { padding: 6px 12px; border: none; border-radius: 4px; backgroundcolor: #4CAF50; color: white; cursor: pointer; margin: 0 5px; }         button:hover { background-color: #45a049; } 
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; background: #fff; border-radius: 5px; } 
    </style> 
</head> 
<body> 
<div class="container"> 
    <h2>Daftar Siswa</h2> 
    <form method="POST" action=""> 
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari siswa..." required> 
        <input type="submit" value="Cari"> 
    </form>     <table> 
        <tr> 
            <th>ID</th> 
            <th>Nama</th> 
            <th>Sekolah</th> 
            <th>Jurusan</th> 
            <th>No HP</th> 
            <th>Alamat</th> 
            <th>Aksi</th> 
        </tr> 
         <?php 
         $result = $conn->query($sql);
        if ($result -> num_rows > 0 ) { 
            while ($row = $result->fetch_assoc()) { 
                echo "<tr>"; 
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["nama"] . "</td>";
                echo "<td>" . $row["sekolah"] . "</td>";
                echo "<td>" . $row["jurusan"] . "</td>";
                echo "<td>" . $row["no_hp"] . "</td>"; 
                echo "<td>" . $row["alamat"] . "</td>";
                echo "<td> 
                        <a href='update.php?id=" . $row["id"] . 
"'><button>Edit</button></a> 
                        <a href='index.php?delete=" . $row["id"] . "' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'><button>Hapus</button></a>                       </td>";                 echo "</tr>"; 
            }         
        } else { 
            echo "<tr><td colspan='7'>Tidak ada data</td></tr>"; 
        }         
        ?>  
    </table> 
    <a href="create.php"><button>Tambah Data Siswa</button></a> 
</div> 
 
</body> 
</html>  
<?php 
$conn->close();
?>