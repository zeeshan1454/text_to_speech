<?php
session_start();
// error_reporting(0);
if(!isset($_SESSION['username']))
{
// header("Location: login.php");
header("Location: session_out.php");
}

include 'config.php';


$username = $_SESSION['username'];
$list_id =$_SESSION['cgreat_list_it'];

$tfnsel_data = "SELECT * FROM vicidial_lists WHERE list_id='$list_id'";
$data_get_one = mysqli_query($conn, $tfnsel_data);
$get_cam_id = mysqli_fetch_assoc($data_get_one);
$new_campaign_id = $get_cam_id['campaign_id'];


$to_date = date("Y-m-d");

$date_24 = date("Y-m-d h:i:s");

   $csptime = $_SESSION['csptime'];
   $agentid = $_SESSION['agent_name'];
   $fromdate = $_SESSION['fromdate'];
   $todate = $_SESSION['todate'];
   $sel_cam = $_SESSION['sel_cam'];
   $serch_type = $_SESSION['serch_type'];

   $select_dur = $_SESSION['selecte_call_duration'];

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index

$columnName = 'lead_id';
$columnSortOrder = 'desc';


$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value



$searchQuery = " ";
if($searchValue != ''){
	$searchQuery = " and (status like '%".$searchValue."%' or 
  phone_number like '%".$searchValue."%' or 
  entry_date like'%".$searchValue."%') ";
}

     $sel = mysqli_query($conn,"select count(*) as allcount from vicidial_list WHERE list_id='$list_id'");
    $records = mysqli_fetch_assoc($sel);
    $totalRecords = $records['allcount'];
    
    
    $sel = mysqli_query($conn,"select count(*) as allcount from vicidial_list WHERE list_id='$list_id' AND 1 ".$searchQuery);
    $records = mysqli_fetch_assoc($sel);
    $totalRecordwithFilter = $records['allcount'];
    
        // $empQuery = "SELECT * from anveyacdr where 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
        $empQuery = "SELECT * FROM vicidial_list WHERE list_id='$list_id' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

 



$empRecords = mysqli_query($conn, $empQuery);
$data = array();
$sr = $row + 1;
while ($row = mysqli_fetch_assoc($empRecords)) {
  $status_one = $row['status'];
  
	 $sell_n = "SELECT * FROM `vicidial_lead_recycle` WHERE campaign_id='$new_campaign_id' AND status='$status_one'";
  // die();
  $query_n = mysqli_query($conn, $sell_n);
  if(mysqli_num_rows($query_n) > 0){
    $data_recycle='on';
  }else{
    $data_recycle='off';
  } 

  // $row_n = mysqli_fetch_array($query_n);
  // if(!empty($row_n['status'])){
	// // $client_name = $row_n['name'];
  // $data_recycle='Finish Call';
  // } else {
	//   $data_recycle='Recycle Call';
  // }
    $data[] = array(
    		"sr"=>$sr,
    		"status"=>$status_one,
    		"phone_code"=>$row['phone_code'],
    		"phone_number"=>$row['phone_number'],
    		"modify_date"=>$row['modify_date'],
    		"recycle"=>$data_recycle
    	);
		$sr++;
}

## Response
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);