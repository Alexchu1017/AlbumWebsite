<!DOCTYPE html>
<?php
session_start();
// Show error message if user direct enter to this page without login.
if(empty($_SESSION['userName']) || empty($_GET['id'])) {
    echo 'Error! Please go to ';
    echo '<a href="login.php">Login</a>';
    exit();
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Delete Account</title>
    </head>
    <body>
        <?php
        try {
            include 'library/db.php';
            
            $idcreator = $_GET['id'];
            $userName = $_SESSION['userName'];
            $searchP = "SELECT imageurl FROM photo WHERE idcreator=(SELECT idcreator FROM creator "
                     . "WHERE (idcreator=? AND name=?))";
            $pPhoto = $db->prepare($searchP);
            $pPhoto->bind_param("is", $idcreator, $userName);
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
            
            $delete = "DELETE FROM creator WHERE idcreator=? AND name=?";
            $p = $db->prepare($delete);
            $p->bind_param("is", $idcreator, $userName);
            $p->execute();
            $db->close();
            
            // delete the photos from the filesystem (folder 'images')
            foreach($filePath as $value) {
                unlink($value);
            }
            
            session_destroy();
            echo "<script>window.alert('Okay~ Account deleted successfully.');"
                ."window.location.href='index.html'</script>";
        } catch (Exception) {
            echo "<script>window.alert('Error to detele this account. Please contact your system administrator.');"
                ."window.location.href='index.html'</script>";
        }
        ?>
    </body>
</html>
