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
    //成功結果
    $api->setArray("ok");
  }
  
  
  
}else{
  $api->denied();
}

print $api->getJSON();

?>