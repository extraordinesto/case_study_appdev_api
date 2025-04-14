<?php
    include '../connection.php';

    //POST = send/save
    //GET = retrieve/read

    $name = $_POST['fullName'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sqlQuery = "INSERT INTO user SET fullName = '$name',
     username = '$username', `password` = '$password' ";

    $resultOfQuery = $conn->query($sqlQuery);

    if($resultOfQuery){
        echo json_encode(array("success"=>true));
    }
    else{
        echo json_encode(array("success"=>false));
    }
?>