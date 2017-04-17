<?php
include __DIR__.'/../ApiCore.php';
include BASE_PATH.'/Model/dbBusiness/Staff.php';
include BASE_PATH.'/Model/dbBusiness/Department.php';

$api = new ApiCore($_REQUEST);

if($api->checkPost(array("username","passwd"))){
  //Logging
  $user_name = $api->post("username");
  $pass_word = $api->post("passwd");
  
  $staff = new Model\Business\Staff();
  
  $ary = $staff->select( 
    array('id','account','department_id','first_day','is_leader','is_admin','lv','name','name_en','post','rank','staff_no','status','status_id','title','update_date'),
    array('staff_no'=>$user_name,'passwd'=>$pass_word)
  );
  
  // var_dump($ary);exit;
  // $ary = $api->select("rv_staff",null,"(staff_no='$user_name') AND (passwd='$pass_word')")->getArray();

  if(is_array($ary) && count($ary)>0){
    $member = $ary[0];
    //離職
    if($member["status_id"]==4){
      $api->denied();
    }else{
      
      $team = new Model\Business\Department();
      
      $member['_department_sub'] = $team->read(array('enable'=>1))->getLowerIdArray( $member['department_id'] );
      $member['_department_upper_path'] = $team->getUpperIdArray( $member['department_id'] );
      
      $member['_login_time'] = @date('Y-m-d:H:m:s');
      
      $api->SC->setMember($member);
      $session = $member;
    }
  }else{
    
    $api->inputWrong();
  }
  
}else{
  
  $session = $api->SC->getMember();
  
}

if(isset($session)){
  
  $session['server_time'] = @date('Y-m-d:H:m:s');
  $api->setArray($session);
  $api->setMsg("Already Logged.");
  
}

print $api->getJSON();

?>
