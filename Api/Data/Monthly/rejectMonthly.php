<?php

include __DIR__."/../../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Multiple/ProcessRouting.php';

$api = new ApiCore($_REQUEST);

use Model\Business\Multiple\ProcessRouting;

$process_id = $api->post('processing_id');
$staff_id = $api->post('staff_id');
  
if( $process_id && $staff_id){
  
  $self_id = $api->SC->getId();
  $reason = $api->post('reason');
  
  
  $routing = new ProcessRouting( $process_id );
  $ok = $routing->rejectToStaff( $staff_id, $reason, $self_id, $api->SC->isAdmin() );
  if(!$ok){$api->denied('Error Staff Id.');}
  
  //成功結果
  $api->setArray("ok");
  
}else{
  $api->denied();
}

print $api->getJSON();

?>