<?php

include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Staff.php';

use Model\Business\Staff;

$api = new ApiCore();

if( $api->SC->isLogin() ){
  
  $staff = new Staff();
  
  $col = $staff->invertColumn(array('passwd','is_leader','is_admin','rank'));
  $sub_team = $api->SC->getSubDepartmentId(true);
  // LG($sub_team);
  $result = $staff->getOnDutyWithTeam( join(',',$sub_team), $col );
  
  // LG($result);
  //成功結果
  $api->setArray($result);
  
}else{
  $api->denied();
}

print $api->getJSON();

?>