<?php 
//error_reporting(0);
//ini_set('display_errors', 0);
include 'config.php';
$data = new getData();
$row = $data->getOvstHosXp();
$query = new Query();
//print_r($row);
$data->clearQueue(); // Remove Queue OlD 
if(count($row) > 0){
	for ($i=0; $i <count($row) ; $i++) { 
		$fetch = $data->prepareDataQ4u($row[$i]->vn); //getQ from HOSxP
		if(count($fetch) > 0){
			$hn =  $fetch[0]->hn;
			$vn =  $fetch[0]->vn;
			$service_point_id =  $fetch[0]->service_point_id;
				//$service_point_id =  $data->getQ4uServicePoint($fetch[0]->depcode);
			$priority_id =  $fetch[0]->priority_id;
			$date_serv =  $fetch[0]->date_serv;
			$time_serv =  $fetch[0]->time_serv;
			$queue_number = $fetch[0]->queue_number;
			$queue_running =  0;
			$his_queue =  $fetch[0]->queue_number;
			$date_create = date('Y-m-d H:i:s');
			$date_update = date('Y-m-d H:i:s');
			$queue_interview = $data->getQueueInterView(); // getQ Q4U
			$countHnPersonQ4u = $data->getQ4uPersonCount($hn); //get Person Q4U
			if($countHnPersonQ4u == 0 ){
				$fetch = $data->prepareDataPersonQ4u($hn); //get Person Data from Hosxp
				$hnp = $fetch[0]->hn;  
				$titlep = $fetch[0]->title; 
				$first_namep = $fetch[0]->first_name; 
				$last_namep  = $fetch[0]->last_name;
				$birthdatep = $fetch[0]->birthdate; 
				$sexp  = $fetch[0]->sex;
				$update_datep = date('Y-m-d H:i:s');
				$sql = "INSERT INTO q4u_person(hn,title,first_name,last_name,birthdate,sex,update_date) VALUES('$hnp','$titlep','$first_namep','$last_namep','$birthdatep','$sexp','$update_datep')";
				$saveQPerson = $query->queryDbSlave($sql,'queue'); // create Q
					//$saveQPerson = true;
			if($saveQPerson == true){$saveQPerson = 1;	}else{$saveQPerson = 0;	}
			}else{$saveQPerson = 0;	}
			$rowServicePoint = $data->getServicepoint();
			for ($j=0; $j <count($rowServicePoint) ; $j++) { 
				$servicepoint_idTwo = $rowServicePoint[$j]->service_point_id;
				$sql = "INSERT INTO q4u_queue(hn,vn,service_point_id,priority_id,date_serv,time_serv,queue_number,queue_running,his_queue,date_create,date_update,queue_interview) VALUES('$hn','$vn','$servicepoint_idTwo','$priority_id','$date_serv','$time_serv','$queue_number','$queue_running','$his_queue','$date_create','$date_update','$queue_interview')";
				$saveQ = $query->queryDbSlave($sql,'queue');
				//$saveQ = true;
				if($saveQ == true){
					$arr[$i]['vn'] = $row[$i]->vn;
					$arr[$i]['hn'] = $hn;
					$arr[$i]['feedback'] = 'success';
					$arr[$i]['person'] = $saveQPerson;
					$arr[$i]['date'] = $date_serv;
					$arr[$i]['time'] = $time_serv;
					$arr[$i]['queue_number'] = $queue_number;
				}else{
					$arr[$i]['vn'] = $row[$i]->vn;
					$arr[$i]['hn'] = $hn;
					$arr[$i]['feedback'] = 'fail';
					$arr[$i]['person'] = $saveQPerson;
					$arr[$i]['date'] = $date_serv;
					$arr[$i]['time'] = $time_serv;
					$arr[$i]['queue_number'] = $queue_number;
				}

			}
		}
	}
}
		$result['result']= @$arr;
		if(count($result) > 0 ){
			echo json_encode($result);
		}
		exit();

	/*update current queue from hosxp
	$rowQ4u = $data->getQueueQ4u();
	for ($i=0; $i <count($rowQ4u); $i++) { 
		 $curQ = $data->checkCurrentHosxPQ($rowQ4u[$i]->vn);
		 $vn = $rowQ4u[$i]->vn;
		 $servicepoint_id = $data->getServicePointHosxp($curQ[0]->cur_dep);
		 if($curQ[0]->main_dep != $curQ[0]->cur_dep){
		 	$sql = "UPDATE q4u_queue SET service_point_id = '$servicepoint_id' , is_completed = 'N' WHERE vn='$vn' ";
		 	$query->queryDbSlave($sql,'queue');
		 }
		} */
