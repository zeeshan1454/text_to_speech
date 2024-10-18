<?php
$localhost="localhost";
$user="root";
$pwd="";
$database="nivesh";
	$con = mysqli_connect($localhost, $user, $pwd) or die(mysqli_error());
	mysqli_select_db($con,$database) or die(mysqli_error());

	// $database1="asterisk";
	// $conn = mysqli_connect($localhost, $user, $pwd) or die(mysqli_error());
	// mysqli_select_db($conn,$database1) or die(mysqli_error());

	// if($conn==true){
	// 	echo "hello";
	// }
?>