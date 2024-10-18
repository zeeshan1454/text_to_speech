<?php
session_start();
if(!isset($_SESSION['username']))
{
// header("Location: login.php");
header("Location: session_out.php");
}

include 'config.php';
$username = $_SESSION['username'];
$sesnumber = $_SESSION['number'];
$AdminNu =$_SESSION['admin_number'];
$start = 0;
$filter = $_SESSION['filter'];
$ifilter = $_SESSION['ifilter'];
$click_id = '';

if(isset($_SESSION['dashboardType']) && ($_SESSION['dashboardType'] == 'twoWay')){
    $type_way = '2';
   } else {
     $type_way = '1';
   }
$ddatat = "SELECT * FROM campaign_agent WHERE agent_number='$sesnumber'";
$dattta = mysqli_query($con, $ddatat);
$ddataa = mysqli_fetch_assoc($dattta);
$pri_data = $ddataa['campaign_number'];

$tfnsel_1 = "SELECT * FROM user WHERE userName='$username'";
 $data_1 = mysqli_query($con, $tfnsel_1);
 if(mysqli_num_rows($data_1) > 0){
   $user_row = mysqli_fetch_assoc($data_1);
   $status_user = $user_row['status'];
   $tstatus = '1';
 } else {
   $tstatus = '0';
 }

 $to_date = date("Y-m-d");
$date_24 = date("Y-m-d h:i:s");

// echo "<script>alert('$to_date')</script>";

if(isset($_SESSION['ifilter'])){
    $ifilter = $_SESSION['ifilter'];
    if(!empty($ifilter)){ 
      $yes_date = date("Y-m-d", strtotime($to_date . " -1 day"));
    } else {
      $yes_date = date("Y-m-d");
    }
    
   if($ifilter == 'week'){
     $ifilter_date = date("Y-m-d", strtotime($date_24 . " -1 week"));
   } elseif($ifilter == 'month'){
     $ifilter_date = date("Y-m-d", strtotime($date_24 . " -1 month"));
   } elseif($ifilter == 'tmonth'){
     $ifilter_date = date("Y-m-d", strtotime($date_24 . " -3 months"));
   } elseif($ifilter == 'smonth'){
     $ifilter_date = date("Y-m-d", strtotime($date_24 . " -6 months"));
   } elseif($ifilter == 'year'){
     $ifilter_date = date("Y-m-d", strtotime($date_24 . " -1 year"));
   } elseif($ifilter == 'lastthreedays') {
    $ifilter_date = date("Y-m-d", strtotime($to_date . " -4 day")); 
    $to_date = date("Y-m-d", strtotime($to_date . " -1 day")); 
  } elseif($ifilter == 'lasttwodays'){
    $ifilter_date = date("Y-m-d", strtotime($to_date . " -3 day")); 
    $to_date = date("Y-m-d", strtotime($to_date . " -1 day")); 
  }
   }

// echo "<script>alert($username)</script>";

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
// $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
// $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$columnName = 'duration'; // Column name
$columnSortOrder = 'desc'; // asc or desc
$searchValue = mysqli_real_escape_string($con,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";
if($searchValue != ''){
	$searchQuery = " and (number like '%".$searchValue."%' or 
    forward like '%".$searchValue."%' or 
    agentName like'%".$searchValue."%' or starttime like'%".$searchValue."%' ) ";
}

  if($status_user == '1'){

	if(isset($ifilter) && !empty($ifilter) && $ifilter != 'all' && $ifilter != 'yesterday'){
		if($filter == 'total'){
	$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE compaignlist.ivr='$type_way' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
	$records = mysqli_fetch_assoc($sel);
	$totalRecords = $records['allcount'];
	
	$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE compaignlist.ivr='$type_way' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
	$records = mysqli_fetch_assoc($sel);
	$totalRecordwithFilter = $records['allcount'];
	
		  $query = "SELECT anveyacdr.*
		  FROM compaignlist
		  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		  WHERE compaignlist.ivr='$type_way' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		  $_SESSION['filter_session'] = 'total';
	  } elseif($filter == 'answer'){
	
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
		  $query = "SELECT anveyacdr.*
		  FROM compaignlist
		  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		  WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' and anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		  $_SESSION['filter_session'] = 'ANSWER';

	  } elseif($filter == 'nokey'){
	
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
		  $query = "SELECT anveyacdr.*
		  FROM compaignlist
		  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		  WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' and anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		  $_SESSION['filter_session'] = 'nokey';

	  } elseif($filter == 'onekey'){
	
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
		  $query = "SELECT anveyacdr.*
		  FROM compaignlist
		  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		  WHERE compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' and anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		  $_SESSION['filter_session'] = 'onekey';

	  } elseif($filter == 'congestion'){
	
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' and anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND '$to_date'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' and anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
		  $query = "SELECT anveyacdr.*
		  FROM compaignlist
		  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		  WHERE compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' and anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		  $_SESSION['filter_session'] = 'CONGESTION';
	  } elseif($filter == 'cancel'){
	
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
		  $query = "SELECT anveyacdr.*
		  FROM compaignlist
		  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		  WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' and anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		  $_SESSION['filter_session'] = 'CANCEL';
	  } elseif($filter == 'MISSED_CALL'){
	
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
	  $query = "SELECT anveyacdr.*
	  FROM compaignlist
	  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	  WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' and anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	  $_SESSION['filter_session'] = 'NOANSWER';
	} elseif($filter == 'BUSY'){
	
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
	  $query = "SELECT anveyacdr.*
	  FROM compaignlist
	  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	  WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' and anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	  $_SESSION['filter_session'] = 'BUSY';
	}
	
		} elseif(isset($ifilter) && !empty($ifilter) && $ifilter == 'all' && $ifilter != 'yesterday'){
	
		  if($filter == 'total'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'total';
		} elseif($filter == 'answer'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'ANSWER';
		} elseif($filter == 'nokey'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'nokey';
		} elseif($filter == 'onekey'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.press_key='1'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'onekey';
		} elseif($filter == 'congestion'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY'");
			$records = mysqli_fetch_assoc($sel);
			$totalRecords = $records['allcount'];
			
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND 1 ".$searchQuery);
			$records = mysqli_fetch_assoc($sel);
			$totalRecordwithFilter = $records['allcount'];
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'CONGESTION';
		} elseif($filter == 'cancel'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY'");
			$records = mysqli_fetch_assoc($sel);
			$totalRecords = $records['allcount'];
			
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND 1 ".$searchQuery);
			$records = mysqli_fetch_assoc($sel);
			$totalRecordwithFilter = $records['allcount'];
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'CANCEL';
		} elseif($filter == 'MISSED_CALL'){
	
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER'");
			$records = mysqli_fetch_assoc($sel);
			$totalRecords = $records['allcount'];
			
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND 1 ".$searchQuery);
			$records = mysqli_fetch_assoc($sel);
			$totalRecordwithFilter = $records['allcount'];
	
		$query = "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'NOANSWER';
	  } elseif($filter == 'BUSY'){
	
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'BUSY';
	  }
		   
		} elseif((isset($ifilter) && !empty($ifilter) && $ifilter == 'yesterday' ) || ( isset($ifilter) && empty($ifilter) )) {
	
		  if($filter == 'total'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.starttime LIKE '%$yes_date%'");
			$records = mysqli_fetch_assoc($sel);
			$totalRecords = $records['allcount'];
			
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
			$records = mysqli_fetch_assoc($sel);
			$totalRecordwithFilter = $records['allcount'];
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'total';
		} elseif($filter == 'answer'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime LIKE '%$yes_date%'");
			$records = mysqli_fetch_assoc($sel);
			$totalRecords = $records['allcount'];
			
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
			$records = mysqli_fetch_assoc($sel);
			$totalRecordwithFilter = $records['allcount'];
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' and anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'ANSWER';
		} elseif($filter == 'nokey'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime LIKE '%$yes_date%'");
			$records = mysqli_fetch_assoc($sel);
			$totalRecords = $records['allcount'];
			
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
			$records = mysqli_fetch_assoc($sel);
			$totalRecordwithFilter = $records['allcount'];
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' and anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'nokey';
		} elseif($filter == 'onekey'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND anveyacdr.starttime LIKE '%$yes_date%'");
			$records = mysqli_fetch_assoc($sel);
			$totalRecords = $records['allcount'];
			
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
			$records = mysqli_fetch_assoc($sel);
			$totalRecordwithFilter = $records['allcount'];
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' and anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'onekey';
		} elseif($filter == 'congestion'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%'");
			$records = mysqli_fetch_assoc($sel);
			$totalRecords = $records['allcount'];
			
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
			$records = mysqli_fetch_assoc($sel);
			$totalRecordwithFilter = $records['allcount'];
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' and anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'CONGESTION';
		} elseif($filter == 'cancel'){
	
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%'");
			$records = mysqli_fetch_assoc($sel);
			$totalRecords = $records['allcount'];
			
			$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
			$records = mysqli_fetch_assoc($sel);
			$totalRecordwithFilter = $records['allcount'];
	
			$query = "SELECT anveyacdr.*
			FROM compaignlist
			JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' and anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
			$_SESSION['filter_session'] = 'CANCEL';
		} elseif($filter == 'MISSED_CALL'){
	
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND anveyacdr.starttime LIKE '%$yes_date%'");
		$records = mysqli_fetch_assoc($sel);
		$totalRecords = $records['allcount'];
		
		$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
		$records = mysqli_fetch_assoc($sel);
		$totalRecordwithFilter = $records['allcount'];
	
		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' and anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'NOANSWER';
	  } 
	
		}


  } else {

	if(isset($ifilter) && !empty($ifilter) && $ifilter != 'all' && $ifilter != 'yesterday'){
	  if($filter == 'total'){

        $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];


$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
FROM compaignlist
JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'total';
	} elseif($filter == 'answer'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'ANSWER';
	} elseif($filter == 'nokey'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'nokey';
	} elseif($filter == 'onekey'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'onekey';
	} elseif($filter == 'congestion'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.* 
		FROM compaignlist 
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name 
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	  
		$_SESSION['filter_session'] = 'CONGESTION';
	} elseif($filter == 'cancel'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'CANCEL';
	}
	elseif($filter == 'NOAGENT'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOAGENT' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOAGENT' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

	  $query = "SELECT anveyacdr.*
	  FROM compaignlist
	  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	  WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOAGENT' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'
	  AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	  $_SESSION['filter_session'] = 'NOAGENT';
  }
  elseif($filter == 'MISSED_CALL'){

	$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

	$query = "SELECT anveyacdr.*
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'
	AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$_SESSION['filter_session'] = 'NOANSWER';
  }
  elseif($filter == 'AFTRHR'){

	$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='AFTRHR' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='AFTRHR' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

	$query = "SELECT anveyacdr.*
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='AFTRHR' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'
	AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$_SESSION['filter_session'] = 'AFTRHR';
  }
  elseif($filter == 'BUSY'){

	$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

	$query = "SELECT anveyacdr.*
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime BETWEEN '$ifilter_date' AND '$to_date'
	AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$_SESSION['filter_session'] = 'BUSY';
  }

	elseif($filter == 'missed' && $click_id == 'data'){
	  $query = "SELECT anveyacdr.*
	  FROM compaignlist
	  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	  WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.starttime LIKE '%$to_date%' AND anveyacdr.status='NOANSWER' "; 
	  $_SESSION['filter_session'] = 'NOANSWER';
	   
	   $update_cmd="UPDATE anveyacdr
  JOIN compaignlist ON compaignlist.compaignnumber = anveyacdr.pri_number
  SET anveyacdr.notification = '1'
  WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way'
		AND anveyacdr.notification = ' '
		AND anveyacdr.starttime LIKE '%$to_date%'
		AND anveyacdr.status = 'NOANSWER';
  ";
	  mysqli_query($con, $update_cmd);
  
  }
	
	 } elseif(isset($ifilter) && !empty($ifilter) && $ifilter == 'all' && $ifilter != 'yesterday') {

	  if($filter == 'total'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'total';
	} elseif($filter == 'answer'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'ANSWER';
	} elseif($filter == 'nokey'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'nokey';
	} elseif($filter == 'onekey'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.press_key='1'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.press_key='1'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'onekey';
	} elseif($filter == 'congestion'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.* 
		FROM compaignlist 
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name 
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	  
		$_SESSION['filter_session'] = 'CONGESTION';
	} elseif($filter == 'cancel'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'CANCEL';
	}
	elseif($filter == 'NOAGENT'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOAGENT'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOAGENT' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

	  $query = "SELECT anveyacdr.*
	  FROM compaignlist
	  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	  WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOAGENT'
	  AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	  $_SESSION['filter_session'] = 'NOAGENT';
  }
  elseif($filter == 'MISSED_CALL'){

	$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

	$query = "SELECT anveyacdr.*
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER'
	AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$_SESSION['filter_session'] = 'NOANSWER';
  }
  elseif($filter == 'AFTRHR'){

	$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='AFTRHR'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='AFTRHR' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

	$query = "SELECT anveyacdr.*
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='AFTRHR'
	AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$_SESSION['filter_session'] = 'AFTRHR';
  }
  elseif($filter == 'BUSY'){

	$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY'");
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecords = $records['allcount'];
	
	   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
	   FROM compaignlist
	   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
			   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND 1 ".$searchQuery);
	   $records = mysqli_fetch_assoc($sel);
	   $totalRecordwithFilter = $records['allcount'];

	$query = "SELECT anveyacdr.*
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY'
	AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$_SESSION['filter_session'] = 'BUSY';
  }

	elseif($filter == 'missed' && $click_id == 'data'){
	  $query = "SELECT anveyacdr.*
	  FROM compaignlist
	  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	  WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.starttime LIKE '%$to_date%' AND anveyacdr.status='NOANSWER' "; 
	  $_SESSION['filter_session'] = 'NOANSWER';
	   
	   $update_cmd="UPDATE anveyacdr
  JOIN compaignlist ON compaignlist.compaignnumber = anveyacdr.pri_number
  SET anveyacdr.notification = '1'
  WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way'
		AND anveyacdr.notification = ' '
		AND anveyacdr.starttime LIKE '%$to_date%'
		AND anveyacdr.status = 'NOANSWER';
  ";
	  mysqli_query($con, $update_cmd);
  
  }

	 } elseif((isset($ifilter) && !empty($ifilter) && $ifilter == 'yesterday' ) || ( isset($ifilter) && empty($ifilter) )){
	  if($filter == 'total'){
		
		 ## Total number of records without filtering
 $sel = mysqli_query($con, "SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.starttime LIKE '%$yes_date%'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];


## Total number of records with filtering
$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
FROM compaignlist
JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.starttime LIKE '%$yes_date%'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'total';
	} elseif($filter == 'answer'){
		
		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime LIKE '%$yes_date%'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
FROM compaignlist
JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='MESSAGEPLAYED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime LIKE '%$yes_date%'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'ANSWER';
	} elseif($filter == 'nokey'){
		
		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime LIKE '%$yes_date%'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
FROM compaignlist
JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOKEYPRESSED' AND anveyacdr.press_key!='1' AND anveyacdr.starttime LIKE '%$yes_date%'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'nokey';
	} elseif($filter == 'onekey'){
		
		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND anveyacdr.starttime LIKE '%$yes_date%'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
FROM compaignlist
JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.press_key='1' AND anveyacdr.starttime LIKE '%$yes_date%'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'onekey';
	} elseif($filter == 'congestion'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];


## Total number of records with filtering
$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
FROM compaignlist
JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.* 
		FROM compaignlist 
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name 
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status!='MESSAGEPLAYED' AND anveyacdr.status!='NOKEYPRESSED' AND anveyacdr.status!='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	  
		$_SESSION['filter_session'] = 'CONGESTION';
	} elseif($filter == 'cancel'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];


## Total number of records with filtering
$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
FROM compaignlist
JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

		$query = "SELECT anveyacdr.*
		FROM compaignlist
		JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%'
		AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
		$_SESSION['filter_session'] = 'CANCEL';
	}
	elseif($filter == 'NOAGENT'){

		$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
 FROM compaignlist
 JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOAGENT' AND anveyacdr.starttime LIKE '%$yes_date%'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];


## Total number of records with filtering
$sel = mysqli_query($con,"SELECT COUNT(*) as allcount
FROM compaignlist
JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOAGENT' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

	  $query = "SELECT anveyacdr.*
	  FROM compaignlist
	  JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	  WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOAGENT' AND anveyacdr.starttime LIKE '%$yes_date%'
	  AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	  $_SESSION['filter_session'] = 'NOAGENT';
  }
  elseif($filter == 'MISSED_CALL'){

	$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND anveyacdr.starttime LIKE '%$yes_date%'");
   $records = mysqli_fetch_assoc($sel);
   $totalRecords = $records['allcount'];
   
   
   ## Total number of records with filtering
   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
   FROM compaignlist
   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
   $records = mysqli_fetch_assoc($sel);
   $totalRecordwithFilter = $records['allcount'];

	$query = "SELECT anveyacdr.*
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='NOANSWER' AND anveyacdr.starttime LIKE '%$yes_date%'
	AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$_SESSION['filter_session'] = 'NOANSWER';
  }
  elseif($filter == 'AFTRHR'){

	$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='AFTRHR' AND anveyacdr.starttime LIKE '%$yes_date%'");
   $records = mysqli_fetch_assoc($sel);
   $totalRecords = $records['allcount'];
   
   
   ## Total number of records with filtering
   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
   FROM compaignlist
   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='AFTRHR' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
   $records = mysqli_fetch_assoc($sel);
   $totalRecordwithFilter = $records['allcount'];

	$query = "SELECT anveyacdr.*
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='AFTRHR' AND anveyacdr.starttime LIKE '%$yes_date%'
	AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$_SESSION['filter_session'] = 'AFTRHR';
  } 
  elseif($filter == 'BUSY'){

	$sel = mysqli_query($con, "SELECT COUNT(*) as allcount
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%'");
   $records = mysqli_fetch_assoc($sel);
   $totalRecords = $records['allcount'];
   
   
   ## Total number of records with filtering
   $sel = mysqli_query($con,"SELECT COUNT(*) as allcount
   FROM compaignlist
   JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
		   WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%' AND 1 ".$searchQuery);
   $records = mysqli_fetch_assoc($sel);
   $totalRecordwithFilter = $records['allcount'];

	$query = "SELECT anveyacdr.*
	FROM compaignlist
	JOIN anveyacdr ON compaignlist.compaignname = anveyacdr.campaign_name
	WHERE  compaignlist.admin = '$username' AND compaignlist.ivr='$type_way' AND anveyacdr.status='BUSY' AND anveyacdr.starttime LIKE '%$yes_date%'
	AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
	$_SESSION['filter_session'] = 'BUSY';
  }
	
	 }

  }

$empRecords = mysqli_query($con, $query);
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
    $data[] = array(
    		"sr"=>$sr,
    		"id"=>$row['id'],
    		"agentName"=>$row['agentName'],
    		"client_name"=>$client_name,
    		"number"=>$row['number'],
    		"status"=>$row['status'],
    		"starttime"=>$row['starttime'],
    		"endtime"=>$row['endtime'],
    		"forward"=>$row['forward'],
    		"call_location"=>$row['call_location'],
    		"recording"=>$row['recording'],
    		"direction"=>$row['direction'],
    		"pri_number"=>$row['pri_number'],
    		"press_key"=>$row['press_key']
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