<!DOCTYPE html>
<?php
session_start();
// Show error message if user direct enter to this page without login.
if(empty($_SESSION['userName'])) {
    echo 'Error! Please go to ';
    echo '<a href="Login_Page.html">Login</a>';
    exit();
}

// Get the data from creator table in database
include 'library/db.php';
$userName = $_SESSION['userName'];
$search = "SELECT name, imageurl FROM creator WHERE name='$userName'";
$result = mysqli_query($db, $search);

$row = mysqli_fetch_assoc($result);
$imageurl = $row['imageurl'];
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Profile</title>
         <!--To link the CSS file to this webpage-->
        <link href="web_design.css" rel="stylesheet" type="text/css"/>
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script>
            // Change profile picture size to fix in <div class="pic">
            function resizePic() {
                var profilePic = document.getElementById("profilePic");
                
                if(profilePic.width >= profilePic.height) {
                    profilePic.style.width = "auto";
                    profilePic.style.height = "100%";
                }else if(profilePic.width < profilePic.height) {
                    profilePic.style.width = "100%";
                    profilePic.style.height = "auto";
                }
            }
            
            // Change the Profile Picture(#profilePic) when user selected a picture from input[file]
            function changePic(picture) {
                if(picture.files.length === 0) {
                    $("#profilePic").attr('src', <?php echo "'" . $imageurl . "'"; ?>);
                }else {
                    var pictureURL = URL.createObjectURL(picture.files[0]);
                    $("#profilePic").attr('src', pictureURL);
                }
            }
        </script>
    </head>
    <body>
        <div class="profile-container">
            <h2>Your Profile</h2>
            <form name="profile" action="edit-creator.php" method="POST" enctype="multipart/form-data">
                <div class="pic">
                    <img id="profilePic" src="<?php echo $imageurl; ?>" alt="Profile Picture" onload="resizePic()" /><br/>
                </div>
               <input type="file" name="selectedPic" accept="image/jpeg, image/png" onchange="changePic(this)" /><br/><br/>
                <p>User Name: &nbsp;<input type="text" name="userName" value="<?php echo $userName; ?>" size="30" /></p>
                <input type="submit" value="Edit" name="edit" />
                <input type="submit" value="Back" name="back" /><br/>
            </form>
            
            <?php
            if(isset($_POST['edit'])) {
                $filePath = "";
                $editOK = TRUE;
                
                // Check if input[name="userName"] empty
                if(empty($_POST['userName'])) {
                    echo "<p class='errMsg'>Sorry~ The User Name cannot be empty.</p>";
                    $editOK = FALSE;
                }else if($_POST['userName'] != $userName) {
                    $sql = "SELECT name FROM creator WHERE name=?";
                    $p = $db->prepare($sql);
                    $p->bind_param("s", $_POST['userName']);
                    $p->execute();
                    
                    $res = $p->get_result();
                    if($res->num_rows > 0) {
                        echo '<p class="errMsg">The User Name "' . $_POST['userName'] . '" already exist. Please try again.</p>';
                        $editOK = FALSE;
                    }
                }
                
                // Check have file selected in input[name="selectedPic"] or not
                if($_FILES["selectedPic"]["size"] == 0) {
                    $filePath = $imageurl;
                }else {
                    $filename = basename($_FILES['selectedPic']['name']);
                    $filePath = "images/" . $filename;
                    
                    $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    if($fileType != "jpg" && $fileType != "jpeg" && $fileType != "png") {
                        echo "<p class='errMsg'>Please select .jpeg and .png files only.</p>";
                        $editOK = FALSE;
                    }
                }
                
                if($editOK) {
                    if(move_uploaded_file($_FILES['selectedPic']['tmp_name'], $filePath) || $imageurl == $filePath) {
                        $update = "UPDATE creator set name=?, imageurl=? WHERE name=?";
                        $pUpdate = $db->prepare($update);
                        $pUpdate->bind_param("sss", $_POST['userName'], $filePath, $userName);
                        $pUpdate->execute();
                        $db->close();
                        
                        $_SESSION['userName'] = $_POST['userName'];
                        $_SESSION['creatorUpdated'] = TRUE;
                        header("location:home-page.php", true, 301);
                        exit();
                    }else {
                        echo "<p class='errMsg'>Error! Your profile picture cannot upload.</p>";
                    }
                }
            }
            $db->close();
            
            if(isset($_POST['back'])) {
                header("location:home-page.php");
                exit();
            }
            ?>
        </div>
    </body>
</html>
