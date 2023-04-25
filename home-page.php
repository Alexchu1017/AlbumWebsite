<!DOCTYPE html>
<?php
session_start();
// Show error message if user direct enter to this page without login.
if(empty($_SESSION['userName'])) {
    echo 'Error! Please go to ';
    echo '<a href="index.html">Login</a>';
    exit();
}
// Get the data from creator table in database
include 'library/db.php';
$userName = $_SESSION['userName'];
$searchCreator = "SELECT idcreator, name FROM creator WHERE name='$userName'";
$SCRes = mysqli_query($db, $searchCreator);

$row = mysqli_fetch_assoc($SCRes);
$idcreator = $row['idcreator'];
?>
<html>
    <head>
        <meta charset="UTF-8">
        <!-- The title of this webpage -->
        <title>Main Page</title>
        <!--To link the CSS file to this webpage-->
        <link href="web_design.css" rel="stylesheet" type="text/css"/>
        <!-- To link the ajax-->
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script>
            $(document).ready(function() {
                var dataLink = "view-album.php?id=" + "<?php echo $idcreator; ?>";
                $("#viewAlbum").load(dataLink, function(response, status, xhr) {
                    if(status === "success") {
                        $("#viewAlbum").html(response);
                    }else if(status === "error") {
                        $("#viewAlbum").html("<p class='errMsg'>Error: Please contact your system administrator.</p>");
                    }
                });
            });
            
            // Delete the account
            function deleteAcc() {
                if(confirm("Do you really want to delete this account? *All the photos will also delete.")) {
                    location.href = "delete-creator.php?id=" + "<?php echo $idcreator; ?>";
                }
            }
        </script>
        <style>
            body{
                background-image: linear-gradient( rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5) ), url("picture/Background_img/background_home-page.jpg");
                background-size: cover;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class="navbar">
            <?php
            if(!empty($_SESSION['creatorUpdated'])) {
                echo "<script>window.alert('Yeah! Profile updated successfully!');"
                     . "window.location.href='home-page.php'</script>";
                $_SESSION['creatorUpdated'] = '';
            }
            echo '<p>&nbsp &nbsp Welcome~ My Dear~ ' . $_SESSION['userName'] . '~</p>';
            ?>
        </div>
        <h1 class="Company_Name">Instagraham Incorporated</h1>
        <div class="album-container">
            <h1 class="title">Photo Gallery</h1>
            <form name="setting" action="home-page.php" method="POST">
                <input type="button" value="Profile" onclick="location.href='edit-creator.php?id=<?php echo $idcreator; ?>';" />
                <input type="button" value="Delete this account" onclick="deleteAcc()" />
                <input type="submit" value="Logout" name="logout" />
            </form>
            
            <form name="albumForm" action="home-page.php" method="POST">
                <br/>
                Album title: &nbsp; <input type="text" name="albumTitle" value="" size="45" />
                <input type="submit" value="Create Album" name="createAlbum" /><br/>
            </form>
        </div>
        <?php
        if(isset($_POST['createAlbum'])) {
            if(empty($_POST['albumTitle'])){
                echo "<p class='errMsg'>Sorry~ The Album title cannot be empty.');</p>";
            }else {
                try {
                    $title = $_POST['albumTitle'];
                    $sql = "SELECT title FROM album WHERE (title=? AND " 
                            . "idcreator= (SELECT idcreator FROM creator WHERE name=?))";
                    $p = $db->prepare($sql);
                    $p->bind_param("ss", $title, $userName);
                    $p->execute();
                    
                    $result = $p->get_result();
                    if($result->num_rows > 0){
                        echo "<p class='errMsg'>The album title ' " . $title . " ' already exist, Please try again.</p>";
                    }else{
                        $imageurl = "picture/album.jpg";
                        $addAlbum = "INSERT INTO album (title, imageurl, idcreator) VALUES (?, ?, ?)";
                        $pAA = $db->prepare($addAlbum);
                        $pAA->bind_param("ssi", $title, $imageurl, $idcreator);
                        $pAA->execute();
                    }
                }catch (Exception $ex) {
                    echo "<p class='errMsg'>Error to create a new album. Please contact your system administrator.</p>";
                }
            }
        }
        $db->close();
        ?>
        
        <div id="viewAlbum"></div>
        
        <?php
        if(isset($_POST['logout'])) {
            $_SESSION['userName'] = '';
            $_SESSION['logout'] = TRUE;
            header("location:index.html", true, 301);
            exit();
        }
        ?>
    </body>
</html>
