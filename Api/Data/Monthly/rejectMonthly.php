<?php

include __DIR__."/../../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Multiple/ProcessRouting.php';
include_once BASE_PATH.'/Model/dbBusiness/Department.php';

$api = new ApiCore($_REQUEST);

use Model\Business\Multiple\ProcessRouting;
use Model\Business\Department;

$process_id = $api->post('processing_id');
$staff_id = $api->post('staff_id');
$turnback = $api->post('turnback');
  
if( $process_id && ($staff_id || ($api->SC->isAdmin() && $turnback)) ){
  
  $self_id = $api->SC->getId();
  $reason = $api->post('reason');
  
  if(!$staff_id){
    $team = new Department();
    $staff_id = $team->select(array('manager_staff_id'),array('upper_id'=>0))[0]['manager_staff_id'];
  }
  
  
  $routing = new ProcessRouting( $process_id );
  
  $ok = $routing->rejectToStaff( $staff_id, $reason, $self_id, $api->SC->isAdmin() );
  if(!$ok){$api->denied('Error Staff Id.');}
  $team = $routing->team->map('manager_staff_id')[$staff_id];
  //mail
  include BASE_PATH.'/Model/MailCenter.php';
  $mail = new Model\MailCenter;
  $mail->addAddress($staff_id);
  $res = $mail->sendTemplate('monthly_return',array(
    'unit_name' => $team['name'],
    'unit_id' => $team['unit_id'],
    'year' => date('Y'),
    'month' => date('m')
  ));
  
  if($res===true){
    $api->setArray('ok');
  }else{
    $api->setArray($res);
  }
  
}else{
  $api->denied();
}

print $api->getJSON();

?>