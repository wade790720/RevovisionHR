<?php

include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Staff.php';

use Model\Business\Staff;

$api = new ApiCore();

if( $api->SC->isLogin() ){
  
  $staff = new Staff();
  
  $col = $staff->invertColumn(array('passwd','is_leader','is_admin','rank'));
  $sub_team = $api->SC->getSubDepartmentId(true);
  $self_id = $api->SC->getId();
  // LG($sub_team);
  $result = $staff->getOnDutyWithTeam( join(',',$sub_team), $col );
  //去掉自己
  foreach($result as $i => &$val){
    if($val['id']==$self_id){
      array_splice($result,$i,1);break;
    }
  }
  
  // LG($result);
  //成功結果
  $api->setArray($result);
  
}else{
  $api->denied();
}

print $api->getJSON();

?>