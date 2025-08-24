<?php
include 'config.php';

if(isset($_POST['query'])){
    $keyword = mysqli_real_escape_string($conn, $_POST['query']);
    $sql = "SELECT nama_pos, alamat, area 
            FROM data_ichiban 
            WHERE nama_pos LIKE '%$keyword%' 
            LIMIT 10";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            echo '<a href="#" class="list-group-item list-group-item-action outlet-item" 
                     data-nama="'.$row['nama_pos'].'" 
                     data-alamat="'.$row['alamat'].'"
                     data-area="'.$row['area'].'">'
                     .$row['nama_pos'].'</a>';
        }
    } else {
        echo '<a href="#" class="list-group-item list-group-item-action disabled">Tidak ditemukan</a>';
    }
}
?>