<?php

include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/ConfigCyclical.php';

$api = new ApiCore($_POST);

use Model\Business\ConfigCyclical;
  
if( $api->checkPost(array('year', 'month')) ){
  
  $year = $api->post('year');
  $month = $api->post('month');
  
  $condition = array('year'=>$year,'month'=>$month);
  
  
  $config = new ConfigCyclical($year,$month);
  
  
  $api->setArray($config->data);
  
}else{
  // var_dump($_POST);
}

print $api->getJSON();

?>