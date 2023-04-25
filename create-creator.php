<!DOCTYPE html>
<?php
session_start();
$_SESSION['accCreated'] = '';
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>New Account</title>
        <!--To link the CSS file to this webpage-->
        <link href="web_design.css" rel="stylesheet" type="text/css"/>
        <style>
            body{
                background-image: linear-gradient(90deg, #4B39A6, #BC4949);
            }
       </style>
    </head>
    <body>
        <h1 class="Company_Name">Instagraham Incorporated</h1>
        <div class="create-acc-form">
            <form name="newAccForm" action="create-creator.php" method="POST">
                <h2 class="lg_title">Create New Account</h2>
                <div class="inner_form">
                    <p>User Name: <input type="text" name="userName" value="" size="20" /></p>
                    <p>&nbsp; &nbsp;Password: <input type="password" name="pw" value="" size="20" /></p>
                </div>
                <input type="submit" value="Create" name="create" />
            </form>
            <?php
            if(isset($_POST['create'])) {
                if(empty($_POST['userName']) || empty($_POST['pw'])) {
                    echo "<script>window.alert('Please enter User Name and Password.');"
                        ."window.location.href='create-creator.php'</script>";
                }else {
                    include 'library/db.php';
                    $userName = $_POST['userName'];
                    $pw = $_POST['pw'];
                    $imageurl = 'picture/profile-picture.jpg';
                    
                    try {
                        $search = "SELECT name FROM creator WHERE name=?";
                        $p = $db->prepare($search);
                        $p->bind_param('s', $userName);
                        $p->execute();
                        $result = $p->get_result();
                        
                        if($result->num_rows == 0) {
                            // Encrypt the password and save into database
                            $insert = "INSERT INTO creator (name, password, imageurl) VALUES (?, MD5(?), ?)";
                            $p2 = $db->prepare($insert);
                            $p2->bind_param("sss", $userName, $pw, $imageurl);
                            $p2->execute();
                            $db->close();
                            $_SESSION['accCreated'] = TRUE;
                            header("location:login.php", true, 301);
                            exit();
                        }else {
                            echo "<script>window.alert('The User Name " . $userName . " already exist. Please try again.');"
                                ."window.location.href='create-creator.php'</script>";
                        }
                    }catch(Exception) {
                        echo "<script>window.alert('Error to create a new account. Please contact your system administrator.');"
                            ."window.location.href='create-creator.php'</script>";
                    }
                    $db->close();
                }
            }
            ?>
            <br/><a class="back-link" href="index.html">Back to Login</a>
        </div>
    </body>
</html>
