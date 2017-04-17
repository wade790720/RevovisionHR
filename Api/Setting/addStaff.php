<?php

include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Multiple/Staff.php';
include BASE_PATH.'/Model/dbBusiness/ConfigCyclical.php';
use Model\Business\Multiple\Staff;
use Model\Business\ConfigCyclical;

$api = new ApiCore($_POST);

if( $api->checkPost(array('department_id','staff_no','title_id','post_id','account','passwd','email','status_id')) && $api->SC->isAdmin() ){
  
  //當月評分
  $webconfig = new ConfigCyclical();
  $year = date("Y");
  $month = date("m");
  $config = $webconfig->getConfigWithDate($year,$month);
  if( $api->isPast($config['RangeEnd']) ){
    
  }else{
    //當月已經起動
    if($config['monthly_launched']==1){ $api->denied('Can Not Modify Staff When Monthly Launch.'); }
  }
  
  
  $data = $api->getPOST();
  
  if( empty($data['department_id']) || empty($data['staff_no']) || empty($data['title_id']) || empty($data['post_id']) || empty($data['account']) || empty($data['passwd']) || empty($data['email']) || empty($data['status_id']) ){
    $api->denied('Wrong Param, Has Empty Param.');
  }
  
  if(empty($data['first_day'])){$data['first_day']=date('Y-m-d');}
  // if(empty($data['update_date'])){$data['update_date']=date('Y-m-d');}
  // LG($data);
  $staff = new Staff();
  
  //檢查員編
  if(!preg_match('/^[a-zA-Z]{1}[\d]{2,4}$/',$data['staff_no'])){ $api->denied('Staff_no No Match Format.'); }
  //檢查mail
  if(!preg_match('/^[\w\d\_\.]+\@.+$/',$data['email'])){ $api->denied('Email No Match Format.'); }
  
  $count = $staff->select( array('staff_no'=>$data['staff_no']) );
  //員編不能重複
  if(count($count)==0){
    
    $new_staff = $staff->admission( $data );
    
    $api->setArray($new_staff);
    
  }else{
    $api->denied('Double Staff_no');
  }
  
  
}else{
  // var_dump($_POST);
}

print $api->getJSON();

?>