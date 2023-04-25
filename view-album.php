<?php
include 'library/db.php';

// Get the data from album table in database
$idcreator = $_GET['id'];
$searchAlbum = "SELECT idalbum, title, imageurl FROM album WHERE idcreator=?";
$pSA = $db->prepare($searchAlbum);
$pSA->bind_param("i", $idcreator);
$pSA->execute();

$SARes = $pSA->get_result();
if($SARes->num_rows == 0) {
    echo "<p>No Album :(</p>";
}else {
    $count = 1;
    while($row = $SARes->fetch_assoc()) {
        echo '<div class="album">';
        echo    '<h3>' . $row['title'] . '</h3>';
        echo    '<a href="view-photo.php?id=' . $row['idalbum'] . '"><img src="' . $row['imageurl'] . '" alt="Album" /></a>';
        echo    '<br/><a href="view-photo.php?id=' . $row['idalbum'] . '"><button>View Photo</button></a>';
        echo    '<a href="edit-album.php?id=' . $row['idalbum'] . '"><button>Edit</button></a>';
        echo    '<button onclick="deleteAlbum'. $count . '()">Delete</button>';
        // The funtion for delete album button
        echo    '<script>';
        echo        'function deleteAlbum' . $count .'() {';
        echo            'if(confirm("Confirm delete \'' . $row['title'] . '\' album. *The photos inside this album will also delete. ")) {';
        echo                'location.href = "delete-album.php?id=' . $row['idalbum'] . '";';
        echo            '}';
        echo        '}';
        echo    '</script>';
        echo '</div>';
        $count++;
    }
}
$db->close();
