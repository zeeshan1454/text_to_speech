<?php
ob_start();
// error_reporting(0);
date_default_timezone_set("Asia/Calcutta");
session_start();
if(!isset($_SESSION['username']))
{
header("Location: login.php");
}
include('opendb.php');
$username = $_SESSION['username'];

$id=$_REQUEST['id'];

if(!empty($id)){
$delete="DELETE FROM txtosp WHERE id = '$id'";
$query=mysqli_query($con, $delete);
if($query)
{
    header("Location:index.php");
}
}


?>