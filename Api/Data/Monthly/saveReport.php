<?php

include __DIR__."/../../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Multiple/ProcessReport.php';

$api = new ApiCore($_POST);

use Model\Business\Multiple\ProcessReport;

$report = $api->post('report');
$all = count($report);

if( $report && $all>0 && $api->SC->isLogin()){
  
  $count = 0;
  $pr = new ProcessReport();
  $process_id = array();
  foreach($report as &$loc){
    $process_id[$loc['processing_id']] = $loc['processing_id'];
  }
  //檢查是不是每張表都是自己的
  $staff = $api->SC->getId();
  $pr_process = $pr->process->read( array('id','owner_staff_id','created_staff_id','type'), 'where id in ('.join(',',$process_id).') and owner_staff_id = '.$staff )->map();
  // $pr_process = $pr->process->read( array('id','owner_staff_id','created_staff_id','type'), 'where id in ('.join(',',$process_id).') ' )->map();
  // LG($pr_process);
  if(!(count($pr_process)==count($process_id))){
    $api->denied('You Are Not Have Permission Change It.');
  }
  
  
  foreach($report as &$loc){
    $pid = $loc['processing_id'];
    unset($loc['processing_id']);
    unset($loc['staff_id']);
    
    $pr->updateReport( $loc, $pr_process[$pid], $staff );
    $count++;
  }
  
  //成功結果
  if($count == $all){
    $api->setArray($count);
  }else{
    $api->sqlError('No Complete Update.');
  }
  
}else{
  $api->denied();
}

print $api->getJSON();

?>