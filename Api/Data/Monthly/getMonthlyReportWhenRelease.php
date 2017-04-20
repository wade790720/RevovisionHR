<?php

include __DIR__."/../../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Multiple/MonthlyReport.php';

$api = new ApiCore($_REQUEST);

use Model\Business\Multiple\MonthlyReport;
  
if($api->checkPost(array("year","month")) && ($api->SC->isLeader() || $api->SC->isAdmin() ) ){
  // LG($api->getPOST());
  $report = new MonthlyReport( $api->getPOST() );
  
  $filter = !!$api->post("release");
  if( $api->SC->isAdmin() || $api->SC->isCEO() ){
    $team_id = false;
  }else{
    $team_id = $api->SC->getDepartmentId();
  }
  
  
  $rgt = $report->getTotallyShow($filter,$team_id);
  // $rgt = $report->getTotallyShow();
  
  $result = array(
    "staff" => $rgt['general'],
    "leader" => $rgt['leader']
  );
  
  $api->setArray($result);
  
}else{
  // var_dump($_POST);
}

print $api->getJSON();

?>