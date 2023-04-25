<!DOCTYPE html>
<?php
session_start();
// Show error message if user direct enter to this page without follow the correctly ways.
if(empty($_SESSION['userName']) || empty($_GET['id'])) {
    echo 'Error! Please go to ';
    echo '<a href="home-page.php">Main Page</a>';
    exit();
}

include 'library/db.php';
$idalbum = $_GET['id'];
$userName = $_SESSION['userName'];
$searchA = "SELECT C.idcreator, A.title FROM creator AS C INNER JOIN album AS A"
         . " ON C.idcreator=A.idcreator WHERE (C.name=? AND A.idalbum=?)";
$pSA = $db->prepare($searchA);
$pSA->bind_param("si", $userName, $idalbum);
$pSA->execute();

$SARes = $pSA->get_result();
// Show error message if user enter others id (which is not related to his idcreator) in the link
if($SARes->num_rows == 0) {
    echo 'Error! Please go to ';
    echo '<a href="home-page.php">Main Page</a>';
    $db->close();
    exit();
}
$row = $SARes->fetch_assoc();
$albumTitle = $row['title'];
$idcreator = $row['idcreator'];
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $albumTitle; ?></title>
        <!--To link the CSS file to this webpage-->
        <link href="web_design.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php echo "<h1>&nbsp; &nbsp; " . $albumTitle . "</h1><hr/>"; ?>
        <div class="add-photo-container">
            <form name="addPhoto" action="view-photo.php?id=<?php echo $idalbum; ?>" method="POST" enctype="multipart/form-data">
                <h1>Add Photo</h1>
                <p>&nbsp; &nbsp; &nbsp; &nbsp;Name: <input type="text" name="title" size="45" /></p>
                <p>Select image to upload:&nbsp; &nbsp;<input type="file" name="fileToUpload" /></p>
                <p><input type="submit" value="Upload Image" name="add" />
                <input type="button" value="Back to Main Page" onclick="location.href='home-page.php'" /></p>
            </form>
        </div>
        
        <?php
        if(isset($_POST["add"])) {
            if(empty($_POST["title"]) || $_FILES["fileToUpload"]["size"] == 0) {
                echo "<p class='errMsg'>Please insert a name and select a photo file.</p>";
            }else {
                $title = $_POST["title"];
                $filename = basename($_FILES['fileToUpload']['name']);
                $filePath = 'images/' . $userName . '-' . $filename;  // Create the target file path
                
                $fileOk = TRUE;
                $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                // Check if file is not .jpg, .jpeg or .png
                if($fileType != "jpg" && $fileType != "jpeg" && $fileType != "png") {
                    echo "<p class='errMsg'>Please select .jpg, .jpeg and .png files only.</p>";
                    $fileOk = FALSE;
                }
                
                // Check if file already exists
                if(file_exists($filePath)) {
                    echo "<p class='errMsg'>The image file already exists. Please try again.</p>";
                    $fileOk = FALSE;
                }
                
                // Check if file size (larger than 10MB) and do not allow
                if($_FILES["fileToUpload"]["size"] > 10000000) {
                    echo "<p class='errMsg'>The image file is too large. Please try again.</p>";
                    $fileOk = FALSE;
                }
                
                // Move the image file to system folder if $fileOk=TRUE
                if($fileOk) {
                    // if file is moved, update the data to database
                    if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $filePath)) {
                        $addP = "INSERT INTO photo (title, imageurl, idcreator, idalbum) VALUES (?, ?, ?, ?)";
                        $pAP = $db->prepare($addP);
                        $pAP->bind_param("ssii", $title, $filePath, $idcreator, $idalbum);
                        $pAP->execute();
                        echo "<p style='text-align: center;'>Image uploaded successfully.</p>";
                    }else {
                        echo "<p class='errMsg'>Error! Your file cannot upload.</p>";
                    }
                }
            }
        }
        ?>
        
        <div id="viewPhoto">
            <table>
                <?php
                try {
                    $searchP = "SELECT * FROM photo WHERE idcreator=? AND idalbum=?";
                    $pPhoto = $db->prepare($searchP);
                    $pPhoto->bind_param("ii", $idcreator, $idalbum);
                    $pPhoto->execute();
                    
                    $photoRes = $pPhoto->get_result();
                    if($photoRes->num_rows == 0) {
                        echo '<p style="text-align: center;">No photos found</p>';
                    }else {
                        $count = 1;
                        echo '<tr>';
                        echo '<th>No.</th> <th>Name</th> <th>Photo</th> <th>Comment</th> <th>Operation</th>';
                        echo '</tr>';
                        while($row = $photoRes->fetch_assoc()) {
                            $dataLink = "Pid=" . $row['idphoto'] . "&Aid=" . $idalbum;
                            echo '<tr>';
                            echo    '<td>' . $count . '.</td>';
                            echo    '<td>' . $row['title'] . '</td>';
                            echo    '<td><img src="' . $row['imageurl'] . '" /></td>';
                            echo    '<td>' . $row['comment'] . '</td>';
                            echo    '<td><a href="edit-photo.php?' . $dataLink . '"><button class="edit">Edit</button></a>'
                                    . '<button class="delete" onclick="deletePhoto' . $count . '()">Delete</button></td>';
                            echo    '<script>';
                            echo        'function deletePhoto' . $count . '() {';
                            echo            'if(confirm("Confirm delete photo No. ' . $count . '")) {';
                            echo                'location.href = "delete-photo.php?' . $dataLink . '";';
                            echo            '}';
                            echo        '}';
                            echo    '</script>';
                            echo '<tr>';
                            $count++;
                        }
                    }
                }catch (Exception) {
                    echo "<p class='errMsg'>Error to view photos. Please contact your system administrator.</p>";
                }
                $db->close();
                ?>
            </table>
        </div>
    </body>
</html>
