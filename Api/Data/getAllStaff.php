<?php

include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Staff.php';

use Model\Business\Staff;

$api = new ApiCore();

if( $api->SC->isLogin() && $api->SC->isAdmin() ){
  
  $staff = new Staff();
  
  // $col = $staff->invertColumn(array('passwd','is_leader'));
  $col = null;
  // LG($col);
  $result = $staff->select( $col, '' );
  //成功結果
  $api->setArray($result);
  
}else{
  $api->denied();
}

print $api->getJSON();

?>