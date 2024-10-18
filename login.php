<?php
ob_start();
// error_reporting(0);
session_start();
include('opendb.php');
date_default_timezone_set("Asia/Calcutta");

$Status = ""; // Initialize $Status variable

if (isset($_POST['username']) && isset($_POST['password'])) {
    $UserName = $_POST['username'];
    $Password = $_POST['password'];

    if (isset($_POST['login'])) {
        $status = "1";

        // Use prepared statements to avoid SQL injection
        $sql = "SELECT userName, password FROM user WHERE userName = ? AND password = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ss", $UserName, $Password); 
        
        if ($stmt->execute()) {
            $stmt->store_result();  // Store the result to get the number of rows
            if ($stmt->num_rows > 0) {
                // Login successful
                $_SESSION['username'] = $UserName;
                header('Location: index.php');
                exit; // Don't forget to exit after redirection
            } else {
                // Invalid login credentials
                $Status = "<font color='red'>Invalid User Name Or Password.</font>";
            }
        } else {
            // If execution fails
            $Status = "<font color='red'>Something went wrong. Please try again later.</font>";
        }
        
        $stmt->close();
    }
    mysqli_close($con);
}
?>


<?php include "headerlink.php" ?>
<style>
    .card-title{
        margin-top: -2.5rem;
    }
    .create_account{
        margin-left: 5rem;
    }
 
    </style>
<body>
    <main>
        <div class="container">
            <section
                class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4"> <a href="index.php"
                                    class="logo d-flex align-items-center w-auto">
                                     <img class="img_logo" src="assets/img/logo.png"
                                        alt="">
                                     </a>
                                        
                                    </div>
                        

                              <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        
                                        <h5 class="card-title text-center pb-0 fs-4">Login Your TTS Account</h5>
                                        <p class="text-center small">Enter your username & password to login</p>
                                    </div>
                                    <form class="row g-3 needs-validation" novalidate="" method="post">

                                           
                                        <div class="col-12">
                                             <!-- <label for="yourUsername" class="form-label"> -->
                                                <?php 
                                                if(isset($status)){?> 
                                                           <div class="alert alert-danger" role="alert">
                                                           Invalid UserName or Password
                                                           </div>
                                               <?php }
                                                ?>
                                             <!-- </label> -->
                                             <label for="yourUsername" class="form-label">Username</label>
                                            <div class="input-group has-validation"> <span class="input-group-text"
                                                    id="inputGroupPrepend">@</span> <input type="text" name="username"
                                                    class="form-control" id="yourUsername" required="">
                                                <div class="invalid-feedback">Please enter your username.</div>
                                            </div>
                                        </div>
                                        <div class="col-12"> <label for="yourPassword"
                                                class="form-label">Password</label> <input type="password"
                                                name="password" class="form-control" id="yourPassword" required="">
                                            <div class="invalid-feedback">Please enter your password!</div>
                                        </div>
                                        <!-- <div class="col-12">
                                            <div class="form-check"> <input class="form-check-input" type="checkbox"
                                                    name="remember" value="true" id="rememberMe"> 
                                                    <label
                                                    class="form-check-label" for="rememberMe">Remember me</label>
                                                </div>
                                        </div> -->
                                        <div class="col-12"> <button class="btn btn-primary w-100" name="login"
                                                type="submit">Login</button></div>
                                        <div class="col-12">
                                            <!-- <p class="small mb-0">Click here to log in as an agent <a class="btn btn-secondary"
                                                    href="agentlogn.php">Agent Login</a> 
                                                   
                                            </p> -->
                                        </div>  
                                    </form>
                                </div>
                            </div>
                          
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main> 
    <?php include "footerlink.php" ?>
</body>

</html>