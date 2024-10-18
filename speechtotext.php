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

    function upload_file($api_key, $path) {
        $url = 'https://api.assemblyai.com/v2/upload';
        $data = file_get_contents($path);
    
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/octet-stream\r\nAuthorization: $api_key",
                'content' => $data
            ]
        ];
    
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
    
        if ($http_response_header[0] == 'HTTP/1.1 200 OK') {
            $json = json_decode($response, true);
            return $json['upload_url'];
        } else {
            echo "Error: " . $http_response_header[0] . " - $response";
            return null;
        }
    }
    
    // Function to create a transcript using AssemblyAI API
    function create_transcript($api_key, $audio_url) {
        $url = "https://api.assemblyai.com/v2/transcript";
    
        $headers = array(
            "authorization: " . $api_key,
            "content-type: application/json"
        );
    
        $data = array(
            "audio_url" => $audio_url
        );
    
        $curl = curl_init($url);
    
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
        $response = json_decode(curl_exec($curl), true);
    
        curl_close($curl);
    
        $transcript_id = $response['id'];
    
        $polling_endpoint = "https://api.assemblyai.com/v2/transcript/" . $transcript_id;
    
        while (true) {
            $polling_response = curl_init($polling_endpoint);
    
            curl_setopt($polling_response, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($polling_response, CURLOPT_RETURNTRANSFER, true);
    
            $transcription_result = json_decode(curl_exec($polling_response), true);
    
            curl_close($polling_response);
    
            if ($transcription_result['status'] === "completed") {
                return $transcription_result;
            } else if ($transcription_result['status'] === "error") {
                throw new Exception("Transcription failed: " . $transcription_result['error']);
            } else {
                sleep(3);
            }
        }
    }
    
    try {
        $files = $_FILES['listdis']['name'];
        $tmp_path = $_FILES['listdis']['tmp_name'];
    
        $currentDateTime = date("Y-m-d H:i:s");
        $api_key = "657733716a7343359c655852a7d6815d";
        $path = "http://103.113.27.5:8723/infosys/speechtotext/" . $files;
    
        // Move uploaded file to the desired location
        $destination_path = "speechtotext/" . $files;
        move_uploaded_file($tmp_path, $destination_path);
    
        $upload_url = upload_file($api_key, $destination_path);
    
        $transcript = create_transcript($api_key, $upload_url);
    
        $text = $transcript['text'];
        $insert = "INSERT INTO speechtotext(file_name, text, date, status) VALUES('$files', '$text', '$currentDateTime', '1')";
        mysqli_query($con, $insert);
    
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
        
        <script>
        window.onload = function() {
         Swal.fire({
             position: "top-end",
             icon: "success",
             title: "Text file has been saved as ' . $files .'",
             showConfirmButton: false,
             timer: 1500
         }).then(function() {
             window.location = "speechtotext.php";
         });
        }
        </script>';
    } catch (Exception $e) {
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
        
        <script>
        window.onload = function() {
         Swal.fire({
             position: "top-end",
             icon: "error",
             title: "Text file not saved! Failed",
             showConfirmButton: false,
             timer: 1500
         }).then(function() {
             window.location = "speechtotext.php";
         });
        }
        </script>';
    }
  

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
  $page = "speechtotext";
  include "sidebar.php" ?>
    <!-- -------------------------------------------------include sidebar--------------------- -->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Speech To Text</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Speech To Text</li>
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
                                            <h5 class="card-title">Total Text</h5>
                                            <div class="" style="float:right;  margin-top:-55px; margin-left:-50px;">
                          <button class="btn btn-md btn-primary shadow-sm best_font" data-toggle="modal" data-target="#staticBackdrop">Create Text</button>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table" id="example">
                                        <thead>
                                            <tr>
                                                <th>Sr No.</th>
                                                <th>File Name</th>
                                                <th>Text</th>
                                                <th>Creation Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <?php
                    $sr = 1;
                   
                        $query = "SELECT * FROM speechtotext WHERE status='1' ORDER BY id DESC";
                     
                    $data_query = mysqli_query($con, $query);
                    while ($row = mysqli_fetch_array($data_query)) {
                        $file_nm = $row['file_name'];
                      ?>
                                        <tbody style="font-size:17px;">
                                            <tr>
                                                <td><?= $sr; ?></td>
                                                <td><?= $file_nm; ?></td>
                                                <td><?= $row['text']; ?></td>
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
                        <form action="" method='post' enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Create Text<span
                                        id='contact_des_c'></span>.</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <label for="Upload Audio File">Upload audio file</label>
                                <input type="file" class="form-control" name="listdis" id="uploadAudioFile"
                                    aria-describedby="helpId" required accept="audio/*">
                                <small id="helpId" class="form-text text-muted">Upload audio file and Trancribe
                                    instantly</small>
                            </div>
                            <div class="modal-footer">
                                <button type="submite" name="submite" class="btn btn-primary "> Save </button>
                                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->

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
            text: "This Text is Delete",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "delete_speechtotext.php?id=" + id;
            }
        });
    });
    </script>


</body>

</html>