<!DOCTYPE html>
<?php
session_start();
$_SESSION['userName'] = '';
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
    </head>
    <body>
        <?php
        if(!empty($_SESSION['accCreated'])) {
            echo "<script>window.alert('New account created successfully. You can login now !');"
            . "window.location.href='Login_Page.html'</script>";
            $_SESSION['accCreated'] = '';
        }
        
        if(isset($_POST['login'])) {
            if(empty($_POST['userName']) || empty($_POST['pw'])) {
                echo "<script>window.alert('Please enter User Name and Password.');"
                     ."window.location.href='Login_Page.html'</script>";
            }else {
                include 'library/db.php';
                $userName = $_POST['userName'];
                $pw = $_POST['pw'];
                
                try {
                    $search = "SELECT name, password FROM creator WHERE (name=? AND password=MD5(?))";
                    $p = $db->prepare($search);
                    $p->bind_param('ss', $userName, $pw);
                    $p->execute();
                    $result = $p->get_result();
                    
                    if($result->num_rows == 1) {
                        $db->close();
                        $_SESSION['userName'] = $userName;
                        header("location:home-page.php", true, 301);
                        exit();
                    }else {
                        echo "<script>window.alert('Wrong User Name or Password. Please try again !');"
                             . "window.location.href='Login_Page.html.'</script>";
                    }
                }catch(Exception) {
                    echo "<script>window.alert('Error to login. Please contact your system administrator.');"
                         . "window.location.href='Login_Page.html.'</script>";
                }
                $db->close();
            }
        }
        ?>
    </body>
</html>
