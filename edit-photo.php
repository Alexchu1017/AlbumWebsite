<!DOCTYPE html>
<?php
session_start();
// Open home-page.php if user direct enter to this page without follow the correctly ways.
if(empty($_SESSION['userName']) || empty($_GET['Pid']) || empty($_GET['Aid'])) {
    header("location:home-page.php", true, 301);
    exit();
}

// Get the data form photo table in database
include 'library/db.php';
$userName = $_SESSION['userName'];
$idphoto = $_GET['Pid'];
$idalbum = $_GET['Aid'];
$searchP = "SELECT title, imageurl, comment FROM photo WHERE (idphoto=? AND idalbum=? "
         . "AND idcreator=(SELECT idcreator FROM creator WHERE name=?))";
$pPhoto = $db->prepare($searchP);
$pPhoto->bind_param("iis", $idphoto, $idalbum, $userName);
$pPhoto->execute();

$photoRes = $pPhoto->get_result();
// if not result go to home page
if($photoRes->num_rows == 0) {
    header("location:home-page.php", true, 301);
    exit();
}
$row = $photoRes->fetch_assoc();
$dataLink = "Pid=" . $idphoto . "&Aid=" . $idalbum;
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Photo</title>
        <!--To link the CSS file to this webpage-->
        <link href="web_design.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <h1>&nbsp; &nbsp; Photo Gallery</h1><hr/>
        <div class="edit-photo-container">
            <form name="editPhotoForm" action="edit-photo.php?<?php echo $dataLink; ?>" method="POST">
                <h1>Edit Photo</h1><hr/>
                <p>Name: <input type="text" name="title" value="<?php echo $row['title'] ?>" size="45" /></p>
                <img src="<?php echo $row['imageurl']; ?>" alt="<?php echo $row['title'] ?>"/><br/>
                <p>Comment:<br/>
                    <textarea name="comment" rows="3" cols="25"><?php echo $row['comment'] ?></textarea>
                </p>
                <input type="submit" value="Edit" name="edit" />
                <input type="button" value="Back" onclick="location.href='view-photo.php?id=<?php echo $idalbum; ?>'" /><br/>
            </form>
        </div>
        
        <?php
        if(isset($_POST['edit'])) {
            if(empty($_POST['title'])) {
                echo "<p class='errMsg'>Sorry~ Name cannot empty.</p>";
            }else {
                $title = $_POST['title'];
                $comment = $_POST['comment'];
                
                try {
                    $updateP = "UPDATE photo SET title=?, comment=? WHERE idphoto=?";
                    $pUP = $db->prepare($updateP);
                    $pUP->bind_param("ssi", $title, $comment, $idphoto);
                    $pUP->execute();
                    $db->close();
                    
                    header("location:view-photo.php?id=" . $idalbum, true, 301);
                    exit();
                }catch (Exception) {
                    echo "<p class='errMsg'>Error to edit photo. Please contact your system administrator.</p>";
                }
            }
        }
        ?>
    </body>
</html>
