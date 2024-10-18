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
$sesnumber = $_SESSION['number'];

$tfnsel_1 = "SELECT * FROM user WHERE userName='$username' AND status='1'";
$data_1 = mysqli_query($con, $tfnsel_1);

if(mysqli_num_rows($data_1) > 0){
  $tstatus = '1';
} else {
  $tstatus = '0';
}



	mysqli_close($con);
ob_flush();
?>
<?php include "headerlink.php" ?>

<body>
    <?php include "header.php" ?>
    <!-- -------------------------------------------------include sidebar--------------------  -->
    <?php
  $page="users";
 include "sidebar.php" ?>



    <!-- -------------------------------------------------include sidebar--------------------- -->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Show Users</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Show Users</li>
                </ol>
            </nav>
        </div>
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-xxl-12 col-xl-12">

                            <div class="col-12">

                                <div class="card recent-sales overflow-auto">

                                    <div class="filter">




                                    </div>



                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12 ticket_but">
                                                <!-- submit data call the CDR data  -->
                                                <h6 class="mb-4 mt-4 text-primary">Show Users </h6>
                                                <div class=""
                                                    style="float:right;  margin-top:-55px; margin-left:-50px;"> <a
                                                        href="create_admin_user.php">
                                                        <button
                                                            class="btn btn-md btn-primary shadow-sm best_font">Create
                                                            User</button></a>
                                                </div>
                                                <!-- show the CDR data  -->
                                                <table class="table table-border datatable">
                                                    <tr>
                                                        <th>Sr.</th>
                                                        <!-- <th>AgentName</th> -->
                                                        <th>Full Name</th>
                                                        <th>User Number</th>
                                                        <th>User Name</th>
                                                        <th>User Type</th>
                                                        <th title="Total use">No. of Campaign</th>
                                                        <th>Action</th>


                                                    </tr>
                                                    <?php 
                        $query2 = "SELECT * from user WHERE status!='1' ORDER BY id DESC";                      
                       
               
                    
                    $result2 = mysqli_query($con, $query2);
                    $sr=1;
                    while ($row2 = mysqli_fetch_array($result2)) {
                        $id = $row2['id'];
                        $fullName = $row2['full_name'];
                        $number = $row2['number'];
                        $user_type = $row2['status'];
                        $email = $row2['email'];
                        $userName = $row2['userName'];
                        $no_agent = $row2['no_agent'];
                    
                        
                    ?>
                                                    <tr>
                                                        <td><?= $sr ?></td>
                                                        <td><?= $fullName ?></td>
                                                        <td><?= $number ?></td>

                                                        <td><?= $userName ?></td>
                                                        <td><?php if($user_type == '0'){ echo "Admin User"; }else{ echo "User";} ?>
                                                        </td>
                                                        <?php
                     $count_data="SELECT count(id) FROM compaignlist WHERE admin='$userName'";
                     $count_query = mysqli_query($con, $count_data);

                     while ($row1 = mysqli_fetch_array($count_query)) 
                        {
                           $total = $row1['count(id)'];
                        }
                     ?>
                                                        <td><?= $total ?></td>
                                                        <td>
                                                            <a href="edit_admin_user.php?id=<?= $id ?> ">
                                                                <span class="badge bg-info">Edit</span>
                                                            </a>
                                                            <img src="assets/img/delete.png" alt="delete_icon"
                                                                style="height:15px; width:15px;" title="Delete Campaign"
                                                                data-id="<?= $id ?>" data-username="<?= $userName ?>"
                                                                id="click_delete">
                                                        </td>

                                                    </tr>
                                                    <?php
                    $sr++; }
                    ?>
                                                </table>
                                                <!-- show the CDR data  -->

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
        </section>
    </main>

    <?php include "footerlink.php" ?>
    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>

    <script>
    $(document).on("click", ".clicktocall", function() {

        var callernumber = $(this).data("id");
        var fornumber = $(this).data("idf");

        // var url = "http://103.113.27.5:8723/mehdi/click2call.php?callerNumber=91"+ fornumber +"&receiverNumber=91" + callernumber + "&user=mehdi&key=jbti89692vc60b2o9nu7647";
        // alert(url);
        // alert(callernumber);
        // alert(fornumber);

        $.ajax({
            url: "http://103.113.27.5:8723/mehdi/click2call.php?callerNumber=91" + fornumber +
                "&receiverNumber=91" + callernumber + "&user=mehdi&key=jbti89692vc60b2o9nu7647",
            type: "GET",
            success: function(data) {
                if (data != " ") {

                    var script = document.createElement('script');
                    script.src =
                        "https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.js";
                    document.head.appendChild(script);

                    var link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href =
                        'https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css';
                    document.head.appendChild(link);

                    script.onload = function() {
                        Swal.fire({
                            position: 'top',
                            icon: 'success',
                            title: data,
                            showConfirmButton: false,
                            timer: 3000
                        })
                    };

                }
            },
            error: function() {

                alert("Call Initiate Error");

            }
        });

    });
    </script>
    <script>
    setInterval(loadlive, 100);

    function loadlive() {
        var live = "live";
        $.ajax({
            url: "ajaxfile.php",
            data: {
                live: live
            },
            type: "POST",
            dataType: "json",
            success: function(res) {
                console.log('res');
                var html = '';
                if (res.length > 0) {
                    for (var i = 0; i < res.length; i++) {
                        html += i;
                    }
                    $('#livecount').css("color", "green");
                    $('#livecount').html(html + 1);
                } else {
                    html += "No Live Calls";
                    $('#livecount').html(html);
                }

            }
        })
    }
    </script>


    <script>
    $(document).on("click", "#click_delete", function() {
        var id = $(this).data("id");
        var userName = $(this).data("username");
        // alert(id);
        // alert(userName);
        Swal.fire({
            title: "Are you sure?",
            text: "This User is delete",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "user_delete.php?id=" + id + "&userName=" +
                userName; // Correct the URL parameter concatenation
            }
        });
    });
    </script>


</body>

</html>