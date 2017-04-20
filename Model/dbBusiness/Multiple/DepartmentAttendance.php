<?php
namespace Model\Business\Multiple;

include_once __DIR__.'/_MultipleSets.php';
include_once __DIR__.'/../Attendance.php';
include_once __DIR__.'/../Staff.php';
include_once __DIR__.'/../Department.php';

use \Exception;

class DepartmentAttendance extends MultipleSets{
  
  protected $attendance;
  
  protected $team;
  
  protected $staff;
  
  public function __construct($db=null){
    $this->attendance = new \Model\Business\Attendance();
    $this->team = new \Model\Business\Department();
    $this->staff = new \Model\Business\Staff();
  }
  
  public function getWithDate($start,$end=null,$team_id=null,$staff_ids=null){
    $start = $this->DateTime($start);
    if(empty($end)){$end = $this->DateTime($start)->modify("+30 days");}else{ $end = $this->DateTime($end); }
    
    $start_time = $start->format("y-m-d");
    $end_time = $end->format("y-m-d");
    
    $table = $this->attendance->table_name;
    $team_table = $this->team->table_name;
    $staff_table = $this->staff->table_name;
    
    $sql = "SELECT atd.*, 
    TIME_FORMAT(atd.checkin_hours,'%H:%i') AS checkin_hours,
    TIME_FORMAT(atd.checkout_hours,'%H:%i') AS checkout_hours,
    st.id,
    st.staff_no,
    st.name,
    st.name_en,
    st.title,
    st.lv,
    dep.name as unit_name,
    dep.unit_id
    FROM $table as atd 
    LEFT JOIN $staff_table as st ON  atd.staff_id = st.id
    LEFT JOIN $team_table as dep  ON  st.department_id = dep.id
    WHERE atd.date >= date('$start_time') AND atd.date <= date('$end_time')";
    
    if(isset($team_id)){ $sql.= "AND st.department_id in ($team_id)"; }
    if(isset($staff_ids)){ $sql.= "AND st.id IN( $staff_ids )"; }
    $sql.= " ORDER BY dep.unit_id ASC, st.rank DESC, st.staff_no, MONTH(atd.date) ASC ,DAY(atd.date) ASC";
    // LG($staff_ids);
    // LG($this->attendance);
    $this->data =  $this->attendance->DB->doSQL($sql);
    foreach($this->data as &$v){
      $v['checkin_hours'] = $this->coverTime($v['checkin_hours']);
      $v['checkout_hours'] = $this->coverTime($v['checkout_hours']);
    }
    return $this->data;
  }
  
  public function bTree(){
    $res = array();
    $staff_ = '_staff';
    foreach($this->data as &$val){
      
      $res[$val['unit_id']]['unit_name'] = $val['unit_name'];
      $res[$val['unit_id']]['unit_id'] = $val['unit_id'];
      
      
      $res[$val['unit_id']][$staff_][$val['staff_no']]['name'] = $val['name'];
      $res[$val['unit_id']][$staff_][$val['staff_no']]['name_en'] = $val['name_en'];
      $res[$val['unit_id']][$staff_][$val['staff_no']]['title'] = $val['title'];
      
      unset($val['name']);
      unset($val['name_en']);
      unset($val['title']);
      unset($val['unit_name']);
      unset($val['id']);
      
      $res[$val['unit_id']][$staff_][$val['staff_no']]['_attendance'][] = $val;
      
    }
    return $res;
  }
  
  public function staffList(){
    $res = array();
    $absence = '_absence';
    foreach($this->data as &$val){
      
      
      $res[$val['staff_no']]['unit_id'] = $val['unit_id'];
      $res[$val['staff_no']]['unit_name'] = $val['unit_name'];
      $res[$val['staff_no']]['name'] = $val['name'];
      $res[$val['staff_no']]['name_en'] = $val['name_en'];
      $res[$val['staff_no']][$absence][$val['date']] = $val;
      
    }
    return $res;
  }
  
  private function coverTime($time){
     return (preg_match('/[1-9]+/',$time))?$time:''; 
  }
  
  protected function get_team(){
    return $this->team;
  }
  protected function get_attendance(){
    return $this->attendance;
  }
  
}
?>
