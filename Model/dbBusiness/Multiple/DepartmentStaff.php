<?php
namespace Model\Business\Multiple;

include_once __DIR__.'/_MultipleSets.php';
include_once __DIR__.'/../Staff.php';
include_once __DIR__.'/../Department.php';

use \Exception;

/*
用部門為基底 組合 員工進每一個單位
*/
class DepartmentStaff extends MultipleSets{
  
  protected $staff;
  
  protected $team;
  
  public static $Staff = "_staff";
  
  public static $Manager = "_manager";
  
  public function __construct(){
    $this->staff = new \Model\Business\Staff();
    $this->team = new \Model\Business\Department();
    $this->data = $this->team->select();
  }
  
  //組合員工到該單位
  public function collect(){
    $team_map = $this->team->read(array('enable'=>1))->map();
    $staff_map = $this->staff->map();
    
    $newStaffKey = self::$Staff;
    $newManagerKey = self::$Manager;
    
    foreach($staff_map as $id => $val){
      $team_id = $val['department_id'];
      if( isset($team_map[$team_id]) ){
        
        $teams = &$team_map[$team_id];
        
        if( isset( $teams[ $newStaffKey ] ) ){
          $teams[ $newStaffKey ][ $id ] = $val;
        }else{
          $teams[ $newStaffKey ] = array( $id => $val );
        }
        
      }
    }
    
    // $team_new = array();
    $o_key = $this->team->origin;
    foreach($team_map as $id => &$val){
      $manager_id = $val['manager_staff_id'];
      
      if( isset($staff_map[ $manager_id ]) ){
        $val[ $newManagerKey ] = $staff_map[ $manager_id ];
      }
      $position = $val[$o_key];
      // $team_new[$position] = $val;
    }
    
    // $this->data = $team_new;
    $this->data = $team_map;
    return $this->data;
  }
  
  //算出部門員工數
  public function countStaff($id, $no_leader=true){
    $maps = $this->map();
    $team = $maps[$id];
    $staffKey = self::$Staff;
    // var_dump($team);
    if(!isset($team[$staffKey])){return 0;}
    
    $manager_id = $team['manager_staff_id'];
    $counts = count($team[$staffKey]);
    
    if(isset($team[$staffKey][$manager_id]) && $no_leader){
      $counts--;
    }
    return $counts; 
  }
  
  //算出部門下層主管數
  public function countSubLeader($id){
    $ary = $this->team->getLowerIdArray($id);
    $map = $this->team->map();
    $i = 0;
    
    foreach($ary as $id){
      $is = $map[$id]['manager_staff_id']==0;
      if(!$is){$i++;}
    }
    return $i;
  }
  //組層陣列組織
  public function getForm($in=null){
    $data = ($in)?$in:$this->data;
    foreach($data as &$val){
      $id = $val['id']?:1;
      $val['id_path'] = $this->team->getUpperIdArray( $id );
    }
    $this->data = $data;
    return $this->data;
  }
  
  
  protected function get_team(){
    return $this->team;
  }
  protected function get_staff(){
    return $this->staff;
  }
  
}
?>
