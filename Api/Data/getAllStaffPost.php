<?php

include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/StaffPost.php';

use Model\Business\StaffPost;

$api = new ApiCore();

if( $api->SC->isAdmin() ){
  
  $staff = new StaffPost();
  
  $result = $staff->read()->data;
  //成功結果
  $api->setArray($result);
  
}else{
  $api->denied();
}

print $api->getJSON();

?>