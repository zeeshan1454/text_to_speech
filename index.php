<?php
ob_start();
// error_reporting(0);
date_default_timezone_set("Asia/Calcutta");
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}
include ('opendb.php');
$listcamp_number = $_REQUEST['camp_number'] ?? '';

$username = $_SESSION['username'] ?? '';

$agent_number = $_SESSION['number'] ?? '';

$tfnsel_1 = "SELECT * FROM user WHERE userName='$username'";
$data_1 = mysqli_query($con, $tfnsel_1);
if (mysqli_num_rows($data_1) > 0) {
  $user_r = mysqli_fetch_assoc($data_1);
  $no_of_agent = $user_r['no_agent'];
  $user_type = $user_r['status'];
  $status = '1';
} else {
  $status = '0';
}



//==================== new code start 
if (isset($_POST["submite"])) {
    $text = $_POST['listdis'];
    $lang = $_POST['lang'];
    $campaign = $_POST['campaign'];
    $currentDateTime = date("Y-m-d H:i:s");
    // एक ट्वीट मे उन्होने यह भी कहा था कि उन्हे सनी के साथ काम करने मे कोई परेशानी नही है

    $apiUrl = 'https://ivrapi.indiantts.in/tts';
    $params = [
        'type' => 'indiantts',
        'text' => $text,
        'api_key' => '101200b0-2710-11ef-b58f-bd77d76bd7b6', 
        'user_id' => '190495',
        'action' => 'play',
        'numeric' => 'hcurrency',
        'lang' => $lang,
        'samplerate' => '8000',
        'ver' => '3'
    ];
    
    $queryString = http_build_query($params);
    
    $finalUrl = $apiUrl . '?' . $queryString;
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $finalUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $response = curl_exec($ch);
    
    $date = date("Y-m-d_H-i-s");
    if (curl_errno($ch)) {
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
        
        <script>
        // var username = document.getElementById("floatingInput1").value;
        window.onload = function() {
         Swal.fire({
             position: "top-end",
             icon: "error",
             title: "Audio file not saved ! Failed",
             showConfirmButton: false,
             timer: 1500
         }).then(function() {
          window.location = "index.php";
         });
        }
        </script>';
        // echo 'Error:' . curl_error($ch);
    } else {
        if (!file_exists('ivr_file')) {
            mkdir('ivr_file', 0777, true);
        }
        $file_name = $date . '.wav';
        $filePath = 'ivr_file/' . $date . '.wav';
        file_put_contents($filePath, $response);
        $insert = "INSERT INTO txtosp(file_name, date, status) VALUES('$file_name', '$currentDateTime', '1')";

        if(!empty($campaign)){
            $up_ivrfile0 = "UPDATE set_ivr SET status='0' WHERE campaignnumber='$campaign'";
            mysqli_query($con, $up_ivrfile0);
            $ins = "INSERT INTO set_ivr(campaignnumber, ivr_file, status) VALUES('$campaign', '$date', '1')";
            $ins_query = mysqli_query($con, $ins);
        }
        mysqli_query($con, $insert);
       echo '
       <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.js"></script>
       <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
       
       <script>
       // var username = document.getElementById("floatingInput1").value;
       window.onload = function() {
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Audio file has been saved as ' . $file_path .'",
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location = "index.php";
        });
       }
       </script>';
        // echo 'Audio file has been saved as ' . $filePath;
    }
curl_close($ch);
  

}
// ================================== Start coding for ad this



//======================== new code start

mysqli_close($con);
ob_flush();
?>
<?php include "headerlink.php" ?>

<body>
    <?php include "header.php" ?>
    <!-- -------------------------------------------------include sidebar--------------------  -->
    <?php
  $page = "texttospeech";
  include "sidebar.php" ?>
    <!-- -------------------------------------------------include sidebar--------------------- -->
    <main id="main" class="main">
        <style>
        .audio-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .audio-player {
            display: inline-block;
        }

        .audio-download {
            margin-left: 10px;
            font-size: 1.2em;
            color: #6c757d;
           position:relative;
           margin-left : -47px;
           font-size: 22px;

        }

        .text-secondary {
            color: #6c757d;
            /* Bootstrap secondary color */
        }
        </style>
        <div class="pagetitle">
            <h1>Text To Speech</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Text To Speech</li>
                </ol>
            </nav>
        </div>
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">

                    <div class="row">
                        <div class="col-12">

                            <div class="card recent-sales overflow-auto">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 ticket_but">
                                            <h5 class="card-title">Total Speech</h5>
                                            <div class="" style="float:right;  margin-top:-55px; margin-left:-50px;">
                                                <?php
                        if ($user_type != '5') {
                          echo '<button class="btn btn-md btn-primary shadow-sm best_font" data-toggle="modal" data-target="#staticBackdrop">Create Speech</button>';
                        }
                        ?>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table" id="example">
                                        <thead>
                                            <tr>
                                                <th>Sr No.</th>
                                                <th>File Name</th>
                                                <th>File</th>
                                                <!-- <th>Download</th> -->
                                                <th>Creation Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <?php
                    $sr = 1;
                   
                        $query = "SELECT * FROM txtosp WHERE status='1' ORDER BY id DESC";
                     
                    $data_query = mysqli_query($con, $query);
                    while ($row = mysqli_fetch_array($data_query)) {
                        $file_nm = $row['file_name'];
                      ?>
                                        <tbody>
                                            <tr>
                                                <td><?= $sr; ?></td>
                                                <td><?= $file_nm; ?></td>
                                                <td class="d-flex mx-auto align-items-center">
                                                    <div class="audio-controls mx-auto ml-2">
                                                        <audio class="audio-player" src="ivr_file/<?= $file_nm ?>"
                                                            type="audio/wav" controls>
                                                            Your browser does not support the audio element.
                                                        </audio>
                                                        <a href="ivr_file/<?= $file_nm ?>" download
                                                            class="text-dark audio-download" title="Download Audio File">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </td>

                                                <td><?= $row['date']; ?></td>
                                                <td>
                                                    <a id="click_delete" data-id="<?= $row['id'] ?>"
                                                        data-list_id="<?= $row['id'] ?>"><span
                                                            class="badge bg-danger cursor-p"><i
                                                                class="bi bi-exclamation-octagon me-1"></i>Delete</span></a>

                                                </td>
                                            </tr>
                                        </tbody>
                                        <?php $sr++; } ?>

                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <!-- add disposition open form  -->
            <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="" method='post'>
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Create Speech<span
                                        id='contact_des_c'></span>.</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <textarea class="form-control" rows="5" name='listdis'
                                    placeholder="Welcome to Winet Infratel. We prioritize customer needs with 24/7 support."
                                    id="con_name-d" required></textarea>
                            </div>
                            <div class="modal-body">
                                <label for="lang">Select Language</label>
                                <select class="form-control" id="lang" name="lang">
                                    <option value="hi_male_v1" selected>Hindi (Male 1)</option>
                                    <option value="hi_female_v1">Hindi (Female)</option>
                                    <option value="hi_male_v2">Hindi (Male 2)</option>
                                    <option value="en_male_v1">English (Male)</option>
                                    <option value="en_female_v1">English (Female 1)</option>
                                    <option value="en_female_v4">English (Female 2)</option>
                                    <option value="en_female_v6">English (Female 3)</option>
                                    <option value="en_female_v7">English (Female 4)</option>
                                    <option value="gu_female_v2">Gujarati (Female 1)</option>
                                    <option value="gu_female_v1">Gujarati (Female 2)</option>
                                    <option value="mr_female_v1">Marathi (Female)</option>
                                    <option value="ta_female_v1">Tamil (Female)</option>
                                    <option value="kn_female_v1">Kannada (Female)</option>
                                    <option value="te_female_v1">Telugu (Female)</option>
                                    <option value="or_female_v1">Oriya (Female)</option>
                                    <option value="or_male_v1">Oriya (Male)</option>
                                    <option value="pn_female_v1">Panjabi (Female)</option>
                                    <option value="as_female_v1">Assamese (Female)</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="submite" name="submite" class="btn btn-primary "> Save </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include "footerlink.php" ?>
    <script>
    $(document).on("click", "#click_delete", function() {
        var id = $(this).data("id");
        Swal.fire({
            title: "Are you sure?",
            text: "This File is Delete",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "delete_texttospeech.php?id=" + id;
            }
        });
    });
    </script>


</body>

</html>