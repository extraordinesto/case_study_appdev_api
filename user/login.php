<?php
include '../connection.php';

//POST = send/save
//GET = retrieve/read

$username = $_POST['username'];
$password = md5($_POST['password']);

$sqlQuery = "SELECT * FROM user WHERE username = '$username' AND `password` = '$password' ";

$resultOfQuery = $conn->query($sqlQuery);

if ($resultOfQuery->num_rows > 0) {
    // allow user to login
    $userRecord = array();
    while ($rowFound = $resultOfQuery->fetch_assoc()) {
        $userRecord[] = $rowFound;
    }

    echo json_encode(
        array(
            "success" => true,
            "userData" => $userRecord[0],
        )
    );
} else {
    // do not allow user
    echo json_encode(array("success" => false));
}
