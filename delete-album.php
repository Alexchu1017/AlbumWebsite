<!DOCTYPE html>
<?php
session_start();
// Show error message if user direct enter to this page without login.
if(empty($_SESSION['userName']) || empty($_GET['id'])) {
    echo 'Error! Please go to ';
    echo '<a href="home-page.php">Main Page</a>';
    exit();
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Delete Album</title>
    </head>
    <body>
        <?php
        try {
            include 'library/db.php';
            
            $idalbum = $_GET['id'];
            $userName = $_SESSION['userName'];
            $searchP = "SELECT imageurl FROM photo WHERE (idalbum=? AND "
                     . "idcreator=(SELECT idcreator FROM creator WHERE name=?))";
            $pPhoto = $db->prepare($searchP);
            $pPhoto->bind_param("is", $idalbum, $userName);
            $pPhoto->execute();
            
            $photoRes = $pPhoto->get_result();
            $filePath = array();
            if($photoRes->num_rows > 0) {
                $count = 0;
                while($row = $photoRes->fetch_assoc()) {
                    // keep the image url for later
                    $filePath[$count] = $row['imageurl'];
                    $count++;
                }
            }
            
            $delete = "DELETE FROM album WHERE (idalbum=? AND "
                    . "idcreator=(SELECT idcreator FROM creator WHERE name=?))";
            $p = $db->prepare($delete);
            $p->bind_param("is", $idalbum, $userName);
            $p->execute();
            $db->close();
            
            // delete the photos from the filesystem (folder 'images')
            foreach($filePath as $value) {
                unlink($value);
            }
            
            header("location:home-page.php", true, 301);
            exit();
        } catch (Exception $ex) {
            echo "<script>window.alert('Error to detele this album. Please contact your system administrator.');"
               . "window.location.href='home-page.php'</script>";
        }
        ?>
    </body>
</html>
