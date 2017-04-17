<?php

include __DIR__."/../../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Multiple/MonthlyReport.php';

$api = new ApiCore($_REQUEST);

use Model\Business\Multiple\MonthlyReport;
  
if($api->checkPost(array("year","month")) && $api->SC->isLogin() ){
  // LG($api->getPOST());
  $report = new MonthlyReport( $api->getPOST() );
  
  $filter = !!$api->post("release");
  
  $rgt = $report->getTotallyShow($filter);
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