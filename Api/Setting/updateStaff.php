<?php
include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/Multiple/Staff.php';
include BASE_PATH.'/Model/dbBusiness/Department.php';
include BASE_PATH.'/Model/dbBusiness/MonthlyReport.php';
include BASE_PATH.'/Model/dbBusiness/ConfigCyclical.php';
use Model\Business\Multiple\Staff;
use Model\Business\Department;
use Model\Business\MonthlyReport;
use Model\Business\ConfigCyclical;

$api = new ApiCore($_POST);
  
if( $api->SC->isAdmin() ){
  
  $id = $api->post('id');
  if(!$id){ $api->denied('No Such Staff.'); }
  $data = $api->getPOST();
  
  //不能改變
  unset($data["account"]);
  unset($data["staff_no"]);
  unset($data["id"]);
  
  
  $staff = new Staff();
  $target_staff = $staff->select( $id );
  $self = $api->SC->getId();
  
  if( count($target_staff)==0 ){ $api->denied('This Staff Has Not Defined.'); }
  
  $isLeader = $target_staff[0]['is_leader'];
  $old_department = $target_staff[0]['department_id'];
  
  if( count($data)==0 ){ $api->denied('No Change Data.'); }
  
  //是主管不能換部門
  if( !($isLeader && $old_department != $data['department_id']) ){
    
    //當月評分
    $webconfig = new ConfigCyclical();
    $year = date("Y");
    $month = date("m");
    $config = $webconfig->getConfigWithDate($year,$month);
    if( $api->isPast($config['RangeEnd']) ){
      if($month==12){
        $month = 1;
        $year += 1;
      }else{
        $month += 1;
      }
    }else{
      //
      if($config['monthly_launched']==1){ $api->denied('Can Not Modify Staff When Monthly Launch.'); }
    }
    
    
    
    if($old_department != $data['department_id']){
      
      //換新部門
      $team = new Department();
      $tm = $team->select( $data['department_id'] );
      if(count($tm)==0){ $api->denied('Department Id Is Wrong.'); }
      $newTeam = $tm[0];
      $newManager = ($newTeam['manager_staff_id']==0) ? $newTeam['supervisor_staff_id'] : $newTeam['manager_staff_id'];
      
      $general = new MonthlyReport();
      $general->update(array(
        "owner_staff_id" => $newManager,
        "owner_department_id" => $data['department_id']
      ), array( 'staff_id'=>$id,'year'=>$year,'month'=>$month ) );
      
      //換單位沒給換單位日
      if( empty($data['update_date']) ){
        $data['update_date']=date('Y-m-d');
      }
      
      
    }
    
    //更新
    $result = $staff->updateByAdmin( $data, $id, $self );
    
    $api->setArray($result);
  }else{
    $api->denied("A Leader Can't Change The Department.");
  }
  
  
}else{
  $api->denied();
}

print $api->getJSON();

?>