<?php

$server = $_SERVER["REMOTE_ADDR"];

if ($server == '127.0.0.1' || $server == '::1') {
    // local credentials
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $database   = "sandbox_bingespark";
} else {
    // remote credentials
    $servername = "jfoster13.webhosting6.eeecs.qub.ac.uk";
    $username   = "jfoster13";
    $password   = "5lV0Rls1VtpBlr3C";
    $database   = "jfoster13";
}

// establish connection
$conn = new mysqli($servername, $username, $password, $database);

// check connection
if ($conn->connect_error){
    echo $conn->connect_error;
    exit();
}

?>