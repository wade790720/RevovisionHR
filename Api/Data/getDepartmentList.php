<?php

include __DIR__."/../ApiCore.php";
include BASE_PATH.'/Model/dbBusiness/ConfigCyclical.php';
include BASE_PATH.'/Model/dbBusiness/Multiple/ProcessReport.php';
include BASE_PATH.'/Model/dbBusiness/Multiple/DepartmentStaff.php';

// $time_start = microtime(true);
$api = new ApiCore($_REQUEST);

use Model\Business\ConfigCyclical;
use Model\Business\Multiple\DepartmentStaff;
use Model\Business\Multiple\ProcessReport;
  
if($api->checkPost(array("year","month")) || $api->checkPost(array("check"))){
  
  $year      = $api->post('year');
  $month     = $api->post('month');
  $check     = $api->post('check');
  
  if($check){
    if(!$year){ $year=(int)date('Y'); }
    if(!$month){ $month=(int)date('m'); }
  }
  
  //取得該月的設定值
  $cyc_config = new ConfigCyclical( $year,$month );
  $config = $cyc_config->data;
  
  if($check && $api->isPast( $config['RangeEnd'] ) ){
    if($month==12){
      $year+=1;
      $month=1;
    }else{
      $month+=1;
    }
    $config = $cyc_config->getConfigWithDate( $year, $month );
  }
  // LG($config);
  
  if( $api->isPast( $config['RangeStart'] ) ){
    
    $ds = new DepartmentStaff();
    
    $pr = new ProcessReport($year,$month);
    
    //過濾還再職的員工
    $ds->staff->read( array('id','staff_no','name','name_en','lv','status_id','first_day','last_day','department_id') ,'')->filterOnDuty( $config['RangeStart'] );
    
    $teamsData = $ds->collect();
    
    $staffMaps = $ds->staff->map();
    
    
    $Staff_key = DepartmentStaff::$Staff;
    
    
    // if( $api->isFuture( $config['RangeEnd'] ) || IS_DEBUG_MODE==1 ){
    if( $api->isFuture( $config['RangeEnd'] ) && $api->SC->isAdmin() ){
      
	  $emptyTeams = array();
	  //檢查 未產生的月績效考評單
      foreach($teamsData as &$loc){
        $manager_id = $loc['manager_staff_id'];
        $super_id = $loc['supervisor_staff_id'];
        $real_manager_id = ($manager_id) ? $manager_id : $super_id;
        $team_id = $loc['id'];
        $super_team_id = $staffMaps[ $super_id ][ 'department_id' ];
        
        $staffCount = $ds->countStaff($team_id);
        //沒主管又沒員工
        if( ($manager_id + $staffCount) == 0 ){$emptyTeams[]=$team_id;continue;}
        
        $leaderCount = $ds->countSubLeader($team_id);
        $superArray = $ds->team->getSuperArrayWithManager($real_manager_id);
        
        //有主管
        if( $manager_id ){
          
          $pr->checkLeaderReport($manager_id, $super_id, $team_id, $super_team_id);
          
        }
        
        //除了主管的員工數量
        if( $staffCount ){
          $staffs = $loc[ $Staff_key ];
          $dev_manager_id = ($manager_id) ? $manager_id : $super_id;
          
          $pr->checkGeneralReport($staffs,$dev_manager_id,$team_id);
          
          //有員工一定是組員對應該單位組長
          
          $stid = ($manager_id) ? $team_id : $super_team_id;
          $pr->checkProcessing($real_manager_id, $real_manager_id, $team_id, $stid ,'2', $superArray);
        }
        //檢察是否有下層主管
        if( $leaderCount && $manager_id){
          
          $pr->checkProcessing($manager_id, $manager_id, $team_id, $team_id ,'1', $superArray);
          
        }
        
      } // for ..count    
      //一次塞入資料
      $times = $pr->releaseAllInsert();
      // LG($times);
      //檢查 績效表的流程單
      if($times>0 || $check){
		  
		  if(count($emptyTeams)>0){
			  $empty_teams_id = join(',',$emptyTeams);
			  $pr->process->delete("where created_department_id in ($empty_teams_id)");
		  }
		  
		  $pr->updateGeneralProcessing();
		  
		}
    }
    
    if($check){
      $api->setArray('done');
    }else{
      // $tree = $ds->team->getTree($teamsData);
      $teamsData = $ds->getForm($teamsData);
      //把 process 組合進去
      $processMap = $pr->process->select(array('year'=>$year,'month'=>$month));
      foreach($processMap as &$val){
        $created_dev = $val['created_department_id'];
        $id = $val['id'];
        $teamsData[$created_dev]['_processing'][$id]= $val;
      }
      $api->setArray($teamsData);
    }
    
  }else{
    //選擇時間還沒到
    $api->denied();
    $api->setMsg("Is Not Yet On Arrival Date.");
    
  }
  
}

print $api->getJSON();

?>