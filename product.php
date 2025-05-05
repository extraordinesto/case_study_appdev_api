<?php
include 'connection.php';

$sqlQuery = "SELECT *, `imageURL` FROM item";
$sql = mysqli_query($conn,$sqlQuery);

$arr=[];

while($row=mysqli_fetch_array($sql)){
    $arr[]=$row;
}

echo json_encode($arr);


?>