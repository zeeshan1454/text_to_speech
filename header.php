<?php
ob_start();
error_reporting(0);
date_default_timezone_set("Asia/Calcutta");
session_start();
if(!isset($_SESSION['username']))
{
header("Location: login.php");
}
include('opendb.php');
$username = $_SESSION['username'];
$ses_number = $_SESSION['number'];


$tfnsel_1 = "SELECT * FROM user WHERE userName='$username'";
$data_1 = mysqli_query($con, $tfnsel_1);
if(mysqli_num_rows($data_1) > 0){
  $user_row = mysqli_fetch_assoc($data_1);
  $name= $user_row['name'];
  $user_status = $user_row['status'];
  $tstatus = '1';
} else {
  $tstatus = '0';
}

// ===========================update logic for update wallet top pop ========================

ob_flush();
?>
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between"> <a href="index.php"
            class="logo d-flex align-items-center">
            <img src="assets/img/logonext2call.png" alt=""> </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <div class="search-bar">
    </div>
    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item d-block d-lg-none"> <a class="nav-link nav-icon search-bar-toggle " href="#"> <i
                        class="bi bi-search"></i> </a></li>
            </li>
            <li class="nav-item dropdown pe-3"> <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                    data-bs-toggle="dropdown">

                    <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> <span
                        class="d-none d-md-block dropdown-toggle ps-2"><?= $username ?>
                    </span> </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><a href="profile.php"></a></h6>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li> <a class="dropdown-item d-flex align-items-center" href="profile.php"> <i
                                class="bi bi-person"></i> <span>My Profile</span> </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li> <a class="dropdown-item d-flex align-items-center" href="logout.php"> <i
                                class="bi bi-box-arrow-right"></i> <span>Sign Out</span> </a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>