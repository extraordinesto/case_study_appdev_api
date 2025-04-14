<?php
    include '../connection.php';

    $username = $_POST['username'];
    
    $sqlQuery = "SELECT * FROM user WHERE username = '$username'";

    $resultOfQuery = $conn->query($sqlQuery);

    if($resultOfQuery->num_rows > 0){
        // username already use -- Error
        echo json_encode(array("usernameFound"=>true));
    }
    else{
        // num rows length == 0
        // username not use -- SignUp Successfully
        echo json_encode(array("usernameFound"=>false));
    }

?>