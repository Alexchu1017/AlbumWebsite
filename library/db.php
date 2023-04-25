<?php
try {
    $db = new mysqli("localhost", "root", "", "5114asst1");
} catch (Exception) {
    echo "Error! Cannot connect to database.<br/>";
    echo "Please contact your system adminstrator.";
    exit();
}
