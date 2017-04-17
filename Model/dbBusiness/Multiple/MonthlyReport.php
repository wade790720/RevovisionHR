<?php
namespace Model\Business\Multiple;

include_once __DIR__.'/_MultipleSets.php';
include_once __DIR__.'/../MonthlyProcessing.php';
include_once __DIR__.'/../MonthlyReport.php';
include_once __DIR__.'/../MonthlyReportLeader.php';
include_once __DIR__.'/../RecordMonthlyReport.php';
include_once __DIR__.'/../RecordPersonalComment.php';
include_once __DIR__.'/../Staff.php';
include_once __DIR__.'/../Department.php';
include_once __DIR__.'/../ConfigCyclical.php';

use \Exception;

class MonthlyReport extends MultipleSets{
  
  protected $team;
  
  protected $staff;
  
  protected $general;
  
  protected $leader;
  
  protected $process;
  
  protected $year;
  
  protected $month;
  
  protected $pid;
  
  protected $condition;
  
  protected $reportData;
  
  protected $record;
  
  protected $comment;
  
  protected $config_cyc;
  
  protected $config;
  
  public function __construct( $set=array() ){
    
    $this->config_cyc = new \Model\Business\ConfigCyclical();
    $this->process = new \Model\Business\MonthlyProcessing();
    $this->general = new \Model\Business\MonthlyReport();
    $this->leader = new \Model\Business\MonthlyReportLeader();
    $this->staff = new \Model\Business\Staff();
    $this->team = new \Model\Business\Department();
    $this->record = new \Model\Business\RecordMonthlyReport();
    $this->comment = new \Model\Business\RecordPersonalComment();
    
    if(isset($set['year']) && isset($set['year'])){
      $this->year       = $set['year'];
      $this->month      = $set['month'];
      $this->condition = array("year"=>$set['year'],"month"=>$set['month']);
      $this->config = $this->config_cyc->getConfigWithDate( $this->year, $this->month );
    }
    
    return $this;
  }
  
  public function getReport(){
    
    $this->general->read( $this->condition );
    
    $this->leader->read( $this->condition );
    
    $this->data = array_merge($this->general->data , $this->leader->data);
    
    return $this->data;
  }
  
    
  public function getReportWithProcess($id,$staff_id,$department_id){
    $collect = array();
    if( count($id)>0 ){
      $id = ' in ('.join(',',$id).')';
    }else{ return null;}
    $pro_sql = $this->getFantasyProcessSQL("where a.id $id");
    $processData = $this->process->sql($pro_sql)->map();
    $shunt = $this->process->shuntIdWithType();
    if(isset($shunt[1])){
      $leader_id = join(',',$shunt[1]);
      $sql= $this->getReportSQL($this->leader->table_name," processing_id in ($leader_id) ");
      $leader = $this->leader->sql($sql)->map('processing_id',false,true);
    }else{
      $leader = array();
    }
    if(isset($shunt[2])){
      $general_id = join(',',$shunt[2]);
      $sql2= $this->getReportSQL($this->general->table_name," processing_id in ($general_id) ");
      $general = $this->general->sql($sql2)->map('processing_id',false,true);
    }else{
      $general = array();
    }
    
    
    foreach($processData as $pid => &$val){
      // 組合設定區間
      $val['_interval'] = array( 'start'=>$this->config_cyc->getLastDate($val['year'],$val['month'],$val['day_start']), 'end'=>$this->config_cyc->getThisDate($val['year'],$val['month'],$val['day_end']) );
      //組合該單的月報表
      $val['_reports'] = isset($leader[$pid])? $leader[$pid] : $general[$pid];
      foreach($val['_reports'] as &$sub_val){
        // convert to array
        $sub_val['comment_id'] = bomb($sub_val['comment_id']);
        $sub_val['_comment_count'] = count($sub_val['comment_id']);
      }
      // convert to array
      // $val['path_staff_id'] = json_decode($val['path_staff_id']);
      // 組合
      $val['_path_staff'] = $this->getStaffWithPath($val['path_staff_id']);
      
      $val['_authority'] = $this->getAuthority($val,$staff_id,$department_id);
      $collect[$pid] = $val;
      
    }
    
    $this->data = &$collect;
    return $this->data;
  }
  
  public function getStaffWithPath(&$ary){
    if(!is_array($ary)){$ary = json_decode($ary);}
    $join = join(',',$ary);
    if(count($ary)>0){
      $map = $this->staff->read(array('department_id','name','name_en','id'),"where id in ($join)")->map();
    }else{
      $map = array();
    }
    return $map;
  }
  
  public function getDepartmentWithPath(&$ary){
    if(!is_array($ary)){$ary = json_decode($ary);}
    $join = join(',',$ary);
    if(count($ary)>0){
      $map = $this->team->read(array('lv','name','unit_id','id'),"where id in ($join)")->map();
    }else{
      $map = array();
    }
    return $map;
  }
  
  public function getTotallyShow($release=false){
    
    $leader = $this->leader->table_name;
    $general = $this->general->table_name;
    $config = $this->config;
   
    $collect = array();
    if($release){$release=" and releaseFlag='Y' ";}else{$release=' ';}
    $year = $this->year;
    $month = $this->month;
    $where = "year = $year and month = $month $release";
    
    $sql= $this->getReportSQL($leader,$where);
    
    $collect['leader'] = $this->leader->DB->doSQL($sql);
    
    $sql2= $this->getReportSQL($general,$where);
    
    $collect['general'] = $this->general->DB->doSQL($sql2);
    
    $report_type_id_array = array();
    
    $comment_id_array = array();
    $pointNeedle = array('1'=>array(),'2'=>array());
    // stamp();
    foreach($collect['leader'] as $key=>&$val){
      $val['_total_score'] = $this->mathLeaderScore($val);
      $val['_work_day'] = $this->getOnWorkDays( $config['RangeStart'] , $config['RangeEnd'] , $val['first_day'] );
      $report_type_id_array[] = '"1-'.$val['id'].'"';
      $val['comment_id'] = bomb($val['comment_id']);
      $val['_comment_count'] = count($val['comment_id']);
      if($val['_comment_count']>0){ $comment_id_array = array_merge($comment_id_array,$val['comment_id']); }
      $pointNeedle[1][$val['id']] = &$val;
    }
    
    foreach($collect['general'] as $key=>&$val){
      if( $val['duty_shift'] > 0 ){
        $val['_total_score'] = $this->mathCSITScore($val);
      }else{
        $val['_total_score'] = $this->mathGeneralScore($val);
      }
      $val['_work_day'] = $this->getOnWorkDays( $config['RangeStart'] , $config['RangeEnd'] , $val['first_day'] );
      $report_type_id_array[] = '"2-'.$val['id'].'"';
      $val['comment_id'] = bomb($val['comment_id']);
      $val['_comment_count'] = count($val['comment_id']);
      if($val['_comment_count']>0){ $comment_id_array = array_merge($comment_id_array,$val['comment_id']); }
      $pointNeedle[2][$val['id']] = &$val;
    }
    // LG($comment_id_array);
    if(count($report_type_id_array)>0){
      $record = $this->record->select(
        array('operating_staff_id','report_id','report_type','changed_json','update_date'),
        'where CONCAT(report_type,"-",report_id) in('.join(',',$report_type_id_array).')'
      );
      foreach($record as &$val){
        $pointNeedle[ $val['report_type'] ][ $val['report_id'] ][ '_changed_record' ][] = $val;
      }
    }
    
    if(count($comment_id_array)>0){
      $staff_table = $this->staff->table_name;
      // $comments = $this->comment->select( 'where id in('.join(',',$comment_id_array).')' );
      $comments = $this->comment->sql( "select a.content, a.create_time, a.report_type, a.report_id, b.name as _create_staff_name, b.name_en as _create_staff_name_en
      from {table} as a 
      left join $staff_table as b on a.create_staff_id = b.id
      where a.id in(".join(',',$comment_id_array).") and a.status = 1" )->data;
      // LG($comments);
      foreach($comments as &$val){
        $pointNeedle[ $val['report_type'] ][ $val['report_id'] ][ '_comments' ][] = $val;
      }
    }
    
    
    
    // LG($collect);
    
    $this->data = $collect;
    return $this->data;
  }
  
  private function getReportSQL($table,$where=' '){
    $staff = $this->staff->table_name;
    $team = $this->team->table_name;
    return " select a.*, b.name, b.name_en, b.first_day, b.staff_no , b.post, b.title, b.last_day, c.name as unit_name, c.unit_id, c.duty_shift 
    from $table as a 
    left join $staff as b on a.staff_id = b.id 
    left join $team as c on b.department_id = c.id 
    where $where
    order by c.unit_id asc, b.rank desc, b.staff_no";
  }
  
  private function getFantasyProcessSQL($process_where){
    return "select a.*, b.name as created_department_name , b.unit_id as created_department_code, 
    c.name as created_staff_name, c.name_en as created_staff_name_en, c.staff_no as created_staff_no, c.post as created_staff_post, 
    d.day_start, d.day_end 
    from {table} as a 
    left join ".$this->team->table_name." as b on a.created_department_id = b.id 
    left join ".$this->staff->table_name." as c on a.created_staff_id = c.id 
    left join ".$this->config_cyc->table_name." as d on a.year = d.year and a.month = d.month 
    $process_where";
  }
  
  public function getAuthority($process_data,$manager_id,$department_id){
    $creator = $this->process->isOnCreator($manager_id,$process_data);
    $owner = $this->process->isOwner($manager_id,$process_data);
    $relative = $this->process->isRelation($manager_id,$process_data);
    $done = $this->process->isDone($process_data);
    $launch = $this->process->isLaunch($process_data);
    return array(
      'is_creator' => $creator,
      'commit' => $launch && $owner && !$done,
      'editor' => $owner && !$done,
      'return' => $owner && !$creator && !$done,
      'comment' => $relative && !$done
    );
  }
  
  private function mathLeaderScore($loc){
    $score = 0;
    
    $score += $loc['target']*2;
    $score += $loc['quality']*2;
    $score += $loc['method']*2;
    $score += $loc['error']*2;
    $score += $loc['backtrack']*2;
    $score += $loc['planning']*2;
    
    $score += $loc['execute']*7/5;
    $score += $loc['decision']*7/5;
    $score += $loc['resilience']*6/5;
    
    $score += $loc['attendance']*2;
    $score += $loc['attendance_members']*2;
    $score += $loc['addedValue'];
    $score -= $loc['mistake'];
    return (int)$score;
  }
  
  private function mathCSITScore($loc){
    $score = 0;
    
    $score += $loc['quality']*5;
    $score += $loc['completeness']*5;
    $score += $loc['responsibility']*3;
    $score += $loc['cooperation']*3;
    $score += $loc['attendance']*4;
    
    $score += $loc['addedValue'];
    $score -= $loc['mistake'];
    return $score;
  }
  
  private function mathGeneralScore($loc){
    $score = 0;
    
    $score += $loc['quality']*5;
    $score += $loc['completeness']*5;
    $score += $loc['responsibility']*5;
    $score += $loc['cooperation']*3;
    $score += $loc['attendance']*2;
    
    $score += $loc['addedValue'];
    $score -= $loc['mistake'];
    return $score;
  }
  
  private function getOnWorkDays($start,$end,$update){
    $ed = $this->DateTime($end,true);
    $st = $this->DateTime($start,true);
    $up = $this->DateTime($update,true);
    if( $st > $up ){
      $fin = $st;
    }else{
      $fin = $up;
    }
    $gap = $ed - $fin;
    return $gap/86400;
  }
  
  protected function get_team(){
    return $this->team;
  }
  protected function get_staff(){
    return $this->staff;
  }
  protected function get_process(){
    return $this->process;
  }
  protected function get_creator(){
    return $this->creator;
  }
  protected function get_supervisor(){
    return $this->supervisor;
  }
  
}
?>
