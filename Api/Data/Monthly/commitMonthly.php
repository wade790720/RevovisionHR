<?php

include __DIR__."/../../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Multiple/ProcessRouting.php';

$api = new ApiCore($_REQUEST);

use Model\Business\Multiple\ProcessRouting;

$process_id = $api->post('processing_id');
  
if( $process_id ){
  
  $routing = new ProcessRouting( $process_id );
  
  if(!$routing->process->isLaunch()){
    $api->denied("This Processing Not Yet Be Ready.");
  }
  
  $self_id = $api->SC->getId();
  
  
  if($routing->isFinally()){
    $routing->done( $self_id );
    //成功結果
    $api->setArray("Already Done.");
  }else{
    
    $ok = $routing->processToSupervisor( $self_id, $api->SC->isAdmin() );
    if(!$ok){$api->denied('You Are Not Onwer.');}
    $staff_id = $routing->supervisor;
    $team = $routing->team->map('manager_staff_id')[$staff_id];
    //mail
    include BASE_PATH.'/Model/MailCenter.php';
    $mail = new Model\MailCenter;
    $mail->addAddress($staff_id);
    $res = $mail->sendTemplate('monthly_arrive',array(
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
  }
  
  
  
}else{
  $api->denied();
}

print $api->getJSON();

?>