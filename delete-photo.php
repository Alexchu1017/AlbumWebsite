<!DOCTYPE html>
<?php
session_start();
// Open home-page.php if user direct enter to this page without follow the correctly ways.
if(empty($_SESSION['userName']) || empty($_GET['Pid']) || empty($_GET['Aid'])) {
    header("location:home-page.php", true, 301);
    exit();
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Delete Photo</title>
        <link href="web_design.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php
        try {
            include 'library/db.php';
            
            $userName = $_SESSION['userName'];
            $idphoto = $_GET['Pid'];
            $idalbum = $_GET['Aid'];
            $searchP = "SELECT imageurl FROM photo WHERE (idphoto=? AND idalbum=? "
                     . "AND idcreator=(SELECT idcreator FROM creator WHERE name=?))";
            $pPhoto = $db->prepare($searchP);
            $pPhoto->bind_param("iis", $idphoto, $idalbum, $userName);
            $pPhoto->execute();
            
            $photoRes = $pPhoto->get_result();
            $filePath = "";
            if($photoRes->num_rows == 0) {
                // if not result open home page
                header("location:home-page.php", true, 301);
                exit();
            }else {
                // keep the image url for later
                $row = $photoRes->fetch_assoc();
                $filePath = $row['imageurl'];
            }
            
            $deleteP = "DELETE FROM photo WHERE idphoto=$idphoto";
            $db->query($deleteP);
            $db->close();
            
            // delete the photo from the filesystem (folder 'images')
            if(unlink($filePath)) {
                header("location:view-photo.php?id=" . $idalbum, true, 301);
                exit();
            }else {
                echo "<p class='errMsg'>Error to delete photo. Please contact your system administrator.</p>";
            }
        }catch (Exception) {
            echo "<p class='errMsg'>Error to delete photo. Please contact your system administrator.</p>";
        }
        ?>
    </body>
</html>
