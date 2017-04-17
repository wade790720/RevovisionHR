<?php

include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Department.php';
// include BASE_PATH.'/Model/dbBusiness/ConfigCyclical.php';
// include BASE_PATH.'/Model/dbBusiness/MonthlyProcessing.php';

$api = new ApiCore($_POST);

use Model\Department;
// use Model\ConfigCyclical;
// use Model\MonthlyProcessing;
$id = $api->post('id');
  
if( $id ){
  
  // LG($id);
  
  $team = new Department();
  $count = $team->select( array('upper_id'=>$id) );
  //有子單位不能移除
  if(count($count)==0){
    
    $qq = $team->delete($id);
    // LG($qq);
    $api->setArray($qq);
  }else{
    $api->denied();
  }
  
}else{
  // var_dump($_POST);
}

print $api->getJSON();

?>