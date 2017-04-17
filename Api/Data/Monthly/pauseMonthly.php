<?php

include __DIR__."/../../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/MonthlyProcessing.php';
include BASE_PATH.'/Model/dbBusiness/ConfigCyclical.php';

$api = new ApiCore($_REQUEST);

use Model\Business\MonthlyProcessing;
use Model\Business\ConfigCyclical;

if( $api->checkPost(array('year','month')) && $api->SC->isSuperUser() ){
  
  $year = $api->post('year');
  $month = $api->post('month');
  
  $process = new MonthlyProcessing();
  
  // $self_id = $api->SC->getId();
  $process->update(array('status_code'=>1),array('year'=>$year,'month'=>$month,'status_code'=>'< 5'));
  
  $config = new ConfigCyclical();
  $config->update(array('monthly_launched'=>0), array('year'=>$year,'month'=>$month));
  
  $api->setArray('ok');
  
}else{
  $api->denied();
}

print $api->getJSON();

?>