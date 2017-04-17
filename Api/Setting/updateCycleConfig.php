<?php

include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/ConfigCyclical.php';

$api = new ApiCore($_POST);

use Model\Business\ConfigCyclical;
  
if( $api->checkPost(array('year', 'month', 'day_start', 'day_end', 'day_cut_addition')) ){
  
  $year = $api->post('year');
  $month = $api->post('month');
  $day_start = $api->post('day_start');
  $day_end = $api->post('day_end');
  $day_cut_addition = $api->post('day_cut_addition');
  $monthly_launched = $api->post('monthly_launched');
  
  $condition = array('year'=>$year,'month'=>$month);
  
  $endDate = "$year-$month-$day_end";
  // LG($endDate);
  if($api->isFuture($endDate) && $monthly_launched==1){
    $api->denied('End Days At Future. Can Not Be Launch.');
  }
  
  
  $config = new ConfigCyclical();
  
  $cutOffDate = date( 'Y-m-d' , strtotime("$year-$month-$day_end + $day_cut_addition days"));
  
  
  $change = array('day_start'=>$day_start,'day_end'=>$day_end,'day_cut_addition'=>$day_cut_addition,'cut_off_date'=>$cutOffDate,'monthly_launched'=>$monthly_launched);
  
  $hasChanged = $config->update($change,$condition);
  if($hasChanged){ $change['hasChanged']=1; }
  
  $api->setArray($change);
  
}else{
  // var_dump($_POST);
}

print $api->getJSON();

?>