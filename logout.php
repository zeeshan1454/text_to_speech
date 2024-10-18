<?php

session_start();
$username=$_SESSION['username'];
include 'opendb.php';
$tfnsel_1 = "SELECT * FROM user WHERE userName='$username'";
$data_1 = mysqli_query($con, $tfnsel_1);
$row_1 = mysqli_fetch_array($data_1);
$status = $row_1['status'];

if($status != '1')
{

    date_default_timezone_set("Asia/Kolkata");
$date_time=Date("Y-m-d h:i:s");
$date=Date("Y-m-d");

$u_sel="SELECT * FROM login_log WHERE log_in_time LIKE '%$date%' AND user_name='$username'";
$u_run=mysqli_query($con,$u_sel);
if(mysqli_num_rows($u_run) > 0){
    $u_ro=mysqli_fetch_array($u_run);
    $id = $u_ro['id']; 
    
    $ins_log="UPDATE `login_log` SET log_out_time='$date_time',status='2' WHERE id='$id'";
    if(mysqli_query($con,$ins_log)){
        // echo "hello";
        // session_destroy();
        // header("Location: login.php");
           session_unset();
    session_destroy();
    // header("Location: index.php?status=You have logged out Successfully!!");
    header("Location:login.php?status=You have logged out Successfully!!");
    die();
    }
}  else {
    $ins_log="UPDATE `login_log` SET status='2' WHERE user_name='$username'";
    if(mysqli_query($con,$ins_log)){
        // echo "hello";
        // session_destroy();
        // header("Location: login.php");
           session_unset();
    session_destroy();
    // header("Location: index.php?status=You have logged out Successfully!!");
    header("Location:login.php?status=You have logged out Successfully!!");
    die();
    }
}
    // session_unset();
    // session_destroy();
    // // header("Location: index.php?status=You have logged out Successfully!!");
    // header("Location:login.php?status=You have logged out Successfully!!");
    // die();

}else{

    session_unset();
    session_destroy();
    header("Location:login.php?status=You have logged out Successfully!!");
    die();
}






// session_start();
// $username=$_SESSION['username'];
// include 'opendb.php';
// $tfnsel_1 = "SELECT * FROM user WHERE userName='$username'";
// $data_1 = mysqli_query($con, $tfnsel_1);
// $row_1 = mysqli_fetch_array($data_1);
// $status = $row_1['status'];

// if($status != '1')
// {
//     session_unset();
//     session_destroy();
//     // header("Location: index.php?status=You have logged out Successfully!!");
//     header("Location:login.php?status=You have logged out Successfully!!");
//     die();

// }else{

//     session_unset();
//     session_destroy();
//     header("Location:login.php?status=You have logged out Successfully!!");
//     die();
// }


?>
