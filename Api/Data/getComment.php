<?php

include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Multiple/CommentCenter.php';

use Model\Business\Multiple\CommentCenter;

$api = new ApiCore($_REQUEST);

if( $api->SC->isLogin() && ( $api->checkPost(array('report_id','report_type')) || $api->checkPost(array('staff_id','year','month')) ) ){
  
  $target_staff_id = $api->post('staff_id');
  $param = array(
    'mode' => ($target_staff_id) ? 1 : 2,
    'self_id' => $api->SC->getId(),
    'target_staff_id' => $target_staff_id,
    'year' => $api->post('year'),
    'month' => $api->post('month'),
    'report_id' => $api->post('report_id'),
    'report_type' => $api->post('report_type')
  );
  
  $comment = new CommentCenter();
  
  $result = $comment->getComment( $param );
  
  if($result){
    //成功結果
    $api->setArray($result);
  }else{
    $api->denied('Not Found Report.');
  }
  
}else{
  // $api->denied();
}

print $api->getJSON();

?>