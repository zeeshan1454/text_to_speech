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
$AdminNu =$_SESSION['admin_number'];


$tfnsel_1 = "SELECT * FROM user WHERE userName='$username'";
$data_1 = mysqli_query($con, $tfnsel_1);
  $user_row = mysqli_fetch_assoc($data_1);
  $status_user = $user_row['status'];

// $to_date = date("Y-m-d");
$to_date = date('Y-m-d', strtotime('1 day'));

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

if(isset($select_dur) && $select_dur == 'Haighest'){
  $columnName = 'duration';
  $columnSortOrder = 'desc';
}else if(isset($select_dur) && $select_dur == 'Lowyest'){
  $columnName = 'duration';
  $columnSortOrder = 'asc';
}else{
  $columnName = 'id';
  $columnSortOrder = 'desc';
}



$searchValue = mysqli_real_escape_string($con,$_POST['search']['value']); // Search value



$searchQuery = " ";
if($searchValue != ''){
	$searchQuery = " and (number like '%".$searchValue."%' or 
    forward like '%".$searchValue."%' or 
    agentName like'%".$searchValue."%' or starttime like'%".$searchValue."%' ) ";
}

if($status_user == '0' || $status_user == '2'){

if($serch_type == 'time'){
  $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
  FROM compaignlist
  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date'");
 $records = mysqli_fetch_assoc($sel);
 $totalRecords = $records['allcount'];
 
 
 $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date' AND 1 ".$searchQuery);
 $records = mysqli_fetch_assoc($sel);
 $totalRecordwithFilter = $records['allcount'];
 
     $empQuery = "SELECT anveyacdr.*
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date'
     AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;


} elseif ($serch_type == 'date'){

  $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
  FROM compaignlist
  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate'");
 $records = mysqli_fetch_assoc($sel);
 $totalRecords = $records['allcount'];
 
 
 $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate' AND 1 ".$searchQuery);
 $records = mysqli_fetch_assoc($sel);
 $totalRecordwithFilter = $records['allcount'];
 
     $empQuery = "SELECT anveyacdr.*
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate'
     AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

   
} elseif ($serch_type == 'agent'){

  $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
  FROM compaignlist
  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.forward = '$agentid'");
 $records = mysqli_fetch_assoc($sel);
 $totalRecords = $records['allcount'];
 
 
 $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.forward = '$agentid' AND 1 ".$searchQuery);
 $records = mysqli_fetch_assoc($sel);
 $totalRecordwithFilter = $records['allcount'];
 
      $empQuery = "SELECT anveyacdr.*
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.forward = '$agentid'
     AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
   
} elseif ($serch_type == 'campaign'){

  $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
  FROM compaignlist
  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.compaignname = '$sel_cam'");
 $records = mysqli_fetch_assoc($sel);
 $totalRecords = $records['allcount'];
 
 
 $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.compaignname = '$sel_cam' AND 1 ".$searchQuery);
 $records = mysqli_fetch_assoc($sel);
 $totalRecordwithFilter = $records['allcount'];
 
      $empQuery = "SELECT anveyacdr.*
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.compaignname = '$sel_cam'
     AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
   
} elseif ($serch_type == 'time-campaign'){

  $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
  FROM compaignlist
  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date'");
 $records = mysqli_fetch_assoc($sel);
 $totalRecords = $records['allcount'];
 
 
 $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date' AND 1 ".$searchQuery);
 $records = mysqli_fetch_assoc($sel);
 $totalRecordwithFilter = $records['allcount'];
 
      $empQuery = "SELECT anveyacdr.*
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date'
     AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
   
} elseif ($serch_type == 'date-campaign'){

  $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
  FROM compaignlist
  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate'");
 $records = mysqli_fetch_assoc($sel);
 $totalRecords = $records['allcount'];
 
 
 $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate' AND 1 ".$searchQuery);
 $records = mysqli_fetch_assoc($sel);
 $totalRecordwithFilter = $records['allcount'];
 
      $empQuery = "SELECT anveyacdr.*
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate'
     AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
   
} elseif ($serch_type == 'time-agent'){

  $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
  FROM compaignlist
  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.forward = '$agentid' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date'");
 $records = mysqli_fetch_assoc($sel);
 $totalRecords = $records['allcount'];
 
 
 $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.forward = '$agentid' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date' AND 1 ".$searchQuery);
 $records = mysqli_fetch_assoc($sel);
 $totalRecordwithFilter = $records['allcount'];
 
      $empQuery = "SELECT anveyacdr.*
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.forward = '$agentid' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date'
     AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
   
} elseif ($serch_type == 'date-agent'){

  $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
  FROM compaignlist
  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.forward = '$agentid' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate'");
 $records = mysqli_fetch_assoc($sel);
 $totalRecords = $records['allcount'];
 
 
 $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.forward = '$agentid' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate' AND 1 ".$searchQuery);
 $records = mysqli_fetch_assoc($sel);
 $totalRecordwithFilter = $records['allcount'];
 
      $empQuery = "SELECT anveyacdr.*
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND anveyacdr.forward = '$agentid' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate'
     AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
   
} else {

  $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
  FROM compaignlist
  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username'");
 $records = mysqli_fetch_assoc($sel);
 $totalRecords = $records['allcount'];
 
 
 $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username' AND 1 ".$searchQuery);
 $records = mysqli_fetch_assoc($sel);
 $totalRecordwithFilter = $records['allcount'];
 
     $empQuery = "SELECT anveyacdr.*
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
     WHERE compaignlist.admin = '$username'
     AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

}


} else {

  if($serch_type == 'time'){
    $sel = mysqli_query($con,"select count(*) as allcount from anveyacdr WHERE starttime BETWEEN '$csptime' AND '$to_date'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];


$sel = mysqli_query($con,"select count(*) as allcount from anveyacdr WHERE starttime BETWEEN '$csptime' AND '$to_date' AND 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

	  $empQuery = "SELECT * from anveyacdr where starttime BETWEEN '$csptime' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
  } elseif($serch_type == 'date'){
    $sel = mysqli_query($con,"select count(*) as allcount from anveyacdr WHERE starttime BETWEEN '$fromdate' AND '$todate'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];


$sel = mysqli_query($con,"select count(*) as allcount from anveyacdr WHERE starttime BETWEEN '$fromdate' AND '$todate' AND 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

	  $empQuery = "SELECT * from anveyacdr where starttime BETWEEN '$fromdate' AND '$todate' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

  } elseif($serch_type == 'agent'){
    
$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
FROM compaignlist
JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
WHERE compaignlist.admin = '$agentid'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

$selc = mysqli_query($con, "SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
 WHERE compaignlist.admin = '$agentid' AND 1 " . $searchQuery);
$records = mysqli_fetch_assoc($selc);
$totalRecordwithFilter = $records['allcount'];

$empQuery = "SELECT anveyacdr.*
FROM compaignlist
JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
WHERE compaignlist.admin = '$agentid' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
  
  } elseif($serch_type == 'campaign'){
    
    $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
    FROM compaignlist
    JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
    WHERE compaignlist.compaignname = '$sel_cam'");
    $records = mysqli_fetch_assoc($sel);
    $totalRecords = $records['allcount'];
    
    $selc = mysqli_query($con, "SELECT COUNT(*) as allcount
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
     WHERE compaignlist.compaignname = '$sel_cam' AND 1 " . $searchQuery);
    $records = mysqli_fetch_assoc($selc);
    $totalRecordwithFilter = $records['allcount'];
    
    $empQuery = "SELECT anveyacdr.*
    FROM compaignlist
    JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
    WHERE compaignlist.compaignname = '$sel_cam' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
      
      } elseif($serch_type == 'time-campaign'){
    
        $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
        FROM compaignlist
        JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
        WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date'");
        $records = mysqli_fetch_assoc($sel);
        $totalRecords = $records['allcount'];
        
        $selc = mysqli_query($con, "SELECT COUNT(*) as allcount
         FROM compaignlist
         JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
         WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date' AND 1 " . $searchQuery);
        $records = mysqli_fetch_assoc($selc);
        $totalRecordwithFilter = $records['allcount'];
        
        $empQuery = "SELECT anveyacdr.*
        FROM compaignlist
        JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
        WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
          
          } elseif($serch_type == 'date-campaign'){
    
            $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
            FROM compaignlist
            JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
            WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate'");
            $records = mysqli_fetch_assoc($sel);
            $totalRecords = $records['allcount'];
            
            $selc = mysqli_query($con, "SELECT COUNT(*) as allcount
             FROM compaignlist
             JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
             WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate' AND 1 " . $searchQuery);
            $records = mysqli_fetch_assoc($selc);
            $totalRecordwithFilter = $records['allcount'];
            
            $empQuery = "SELECT anveyacdr.*
            FROM compaignlist
            JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
            WHERE compaignlist.compaignname = '$sel_cam' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
              
              } elseif($serch_type == 'time-agent'){
    
    $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
    FROM compaignlist
    JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
    WHERE compaignlist.admin = '$agentid' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date'");
    $records = mysqli_fetch_assoc($sel);
    $totalRecords = $records['allcount'];
    
    $selc = mysqli_query($con, "SELECT COUNT(*) as allcount
     FROM compaignlist
     JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
     WHERE compaignlist.admin = '$agentid' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date' AND 1 " . $searchQuery);
    $records = mysqli_fetch_assoc($selc);
    $totalRecordwithFilter = $records['allcount'];
    
    $empQuery = "SELECT anveyacdr.*
    FROM compaignlist
    JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
    WHERE compaignlist.admin = '$agentid' AND anveyacdr.starttime BETWEEN '$csptime' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
      
      } elseif($serch_type == 'date-agent'){
    
        $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
        FROM compaignlist
        JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
        WHERE compaignlist.admin = '$agentid' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate'");
        $records = mysqli_fetch_assoc($sel);
        $totalRecords = $records['allcount'];
        
        $selc = mysqli_query($con, "SELECT COUNT(*) as allcount
         FROM compaignlist
         JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
         WHERE compaignlist.admin = '$agentid' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate' AND 1 " . $searchQuery);
        $records = mysqli_fetch_assoc($selc);
        $totalRecordwithFilter = $records['allcount'];
        
        $empQuery = "SELECT anveyacdr.*
        FROM compaignlist
        JOIN anveyacdr ON compaignlist.id = anveyacdr.campaign_id
        WHERE compaignlist.admin = '$agentid' AND anveyacdr.starttime BETWEEN '$fromdate' AND '$todate' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
          
          } else {

    $sel = mysqli_query($con,"select count(*) as allcount from anveyacdr");
    $records = mysqli_fetch_assoc($sel);
    $totalRecords = $records['allcount'];
    
    
    $sel = mysqli_query($con,"select count(*) as allcount from anveyacdr WHERE 1 ".$searchQuery);
    $records = mysqli_fetch_assoc($sel);
    $totalRecordwithFilter = $records['allcount'];
    
        $empQuery = "SELECT * from anveyacdr where 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

  }

}



$empRecords = mysqli_query($con, $empQuery);
$data = array();
$sr = $row + 1;
while ($row = mysqli_fetch_assoc($empRecords)) {
  $cli_num = $row['number'];
	$sell_n = "SELECT name FROM add_name WHERE number='$cli_num'";
  $query_n = mysqli_query($con, $sell_n);
  $row_n = mysqli_fetch_array($query_n);
  if(!empty($row_n['name'])){
	$client_name = $row_n['name'];
  } else {
	$client_name = $row['number'];
  }

  $campaign_id = $row['campaign_name'];
	$sell_n = "SELECT compaignnumber FROM compaignlist WHERE compaignname='$campaign_id'";
  $query_n = mysqli_query($con, $sell_n);
  $row_n = mysqli_fetch_array($query_n);
  if(!empty($row_n['compaignnumber'])){
	$campaign_name = $row_n['compaignnumber'];
  } else {
	$campaign_name = $campaign_id;
  }
    $data[] = array(
    		"sr"=>$sr,
    		"id"=>$row['id'],
    		"agentName"=>$row['agentName'],
    		"number"=>$row['number'],
    		"client_name"=>$client_name,
    		"status"=>$row['status'],
    		"starttime"=>$row['starttime'],
    		"endtime"=>$row['endtime'],
    		"forward"=>$row['forward'],
    		"call_location"=>$row['call_location'],
    		"recording"=>$row['recording'],
    		"direction"=>$row['direction'],
    		"pri_number"=>$row['pri_number'],
    		"press_key"=>$row['press_key'],
    		"campaign_name"=>$campaign_name
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