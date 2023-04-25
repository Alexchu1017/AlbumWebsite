<!DOCTYPE html>
<?php
session_start();
// Show error message if user direct enter to this page without login.
if(empty($_SESSION['userName']) || empty($_GET['id'])) {
    echo 'Error! Please go to ';
    echo '<a href="home-page.php">Main Page</a>';
    exit();
}

include 'library/db.php';
$idalbum = $_GET['id'];
$userName = $_SESSION['userName'];
$searchA = "SELECT title FROM album WHERE (idalbum=? AND idcreator=(SELECT idcreator FROM creator WHERE name=?))";
$pSA = $db->prepare($searchA);
$pSA->bind_param("is", $idalbum, $userName);
$pSA->execute();

$SARes = $pSA->get_result();
if($SARes->num_rows == 0) {
    echo 'Error! Please go to ';
    echo '<a href="home-page.php">Main Page</a>';
    $db->close();
    exit();
}
$row = $SARes->fetch_assoc();
$albumTitle = $row['title'];
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Album</title>
        <!--To link the CSS file to this webpage-->
        <link href="web_design.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <h1>&nbsp; &nbsp; Photo Gallery</h1><hr/>
        <h1 class="Company_Name">Instagraham Incorporated</h1>
        <div class="edit-album-container">
            <form name="EditAlbumForm" action="edit-album.php?id=<?php echo $idalbum; ?>" method="POST">
                <h1>Edit Your Album</h1>
                <p>Album title: &nbsp; <input type="text" name="title" value="<?php echo $albumTitle; ?>" size="45" /></p>
                <p><input type="submit" value="Edit" name="editAlbum" />
                <input type="button" value="Back To Main Page" onclick="location.href='home-page.php'" /></p>
            </form>
        </div>
        
        <?php
        if(isset($_POST['editAlbum'])) {
            if(empty($_POST['title'])) {
                echo "<p class='errMsg'>Album title cannot empty.</p>";
            }else {
                try {
                    $title = $_POST['title'];
                    $sql = "SELECT title FROM album WHERE (title=? AND " 
                            . "idcreator= (SELECT idcreator FROM creator WHERE name=?))";
                    $p = $db->prepare($sql);
                    $p->bind_param("ss", $title, $userName);
                    $p->execute();
                    
                    $result = $p->get_result();
                    if($result->num_rows > 0){
                        echo "<p class='errMsg'>The album title ' " . $title . " ' already exist, Please try again. </p>";
                    }else{
                        $updateA = "UPDATE album SET title=? WHERE idalbum=?";
                        $pUA = $db->prepare($updateA);
                        $pUA->bind_param("si", $title, $idalbum);
                        $pUA->execute();
                        $db->close();
                        
                        header("location:home-page.php", true, 301);
                        exit();
                    }
                }catch (Exception) {
                    echo "<p class='errMsg'>Error to update your data. Please contact your system administrator.</p>";
                }
            }
        }
        ?>
    </body>
</html>
