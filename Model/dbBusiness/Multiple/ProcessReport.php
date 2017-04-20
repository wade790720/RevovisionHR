<?php
namespace Model\Business\Multiple;

include_once __DIR__.'/_MultipleSets.php';
include_once __DIR__.'/../MonthlyProcessing.php';
include_once __DIR__.'/../MonthlyReport.php';
include_once __DIR__.'/../MonthlyReportLeader.php';
include_once __DIR__.'/../RecordMonthlyProcessing.php';
include_once __DIR__.'/../RecordMonthlyReport.php';

use \Exception;

/*
用月績效考評表為基底 組合 月績效報表 
主要為程式內使用  不適用來組合資料給使用者 效能向

*/
class ProcessReport extends MultipleSets{
  
  protected $leader;
  
  protected $general;
  
  protected $record_report;
  
  protected $record_process;
  
  protected $process;
  
  protected $year;
  
  protected $month;
  
  protected $date_condition;
  
  public function __construct($year=null,$month=null){
    
    $this->leader = new \Model\Business\MonthlyReportLeader();
    $this->general = new \Model\Business\MonthlyReport();
    $this->process = new \Model\Business\MonthlyProcessing();
    $this->record_process = new \Model\Business\RecordMonthlyProcessing();
    $this->record_report = new \Model\Business\RecordMonthlyReport();
    
    if($year && $month){
      $this->year = $year;
      $this->month = $month;
      
      $this->date_condition = array("year"=>$year,"month"=>$month);
      $this->initRead( $this->date_condition );
    }
    return $this;
  }
  
  public function initRead($codi){
    $this->process->read( $codi );
    
    $this->leader->read( array('id','staff_id','year','month'),$codi );
    $this->general->read( array('id','staff_id','year','month'),$codi );
  }
  
  //
  public function checkLeaderReport($id,$super_id,$team_id,$super_team_id){
    
    $a = $this->leader->checkExist( $id, $this->year, $this->month );
    if(!$a){
      //直到最後上頭都跟自己相同的話 只有運維中心的主管  :: 不用建立 report
      if($super_id!=$id){
        $this->leader->addStorage( array(
          "staff_id" => $id,
          "year" => $this->year,
          "month" => $this->month,
          "owner_staff_id" => $super_id,
          "owner_department_id" => $super_team_id
        ) );
      }
    }
    return $this;
  }
  
  public function checkProcessing($create_id, $owner_id, $team_id, $owner_team_id, $type, $sa){
    if( !$this->checkProcessIsExist($team_id,$type)){
      // $id = ($create_id) ? $create_id : $owner_id;
      $this->addProcess($create_id, $owner_id, $team_id, $owner_team_id, $type, $sa);
    }
  }
  
  private function checkProcessIsExist($id,$type){
    $pmap = $this->process->map("created_department_id");
    $bl = isset($pmap[$id]);
    if($bl){
      if( isset($pmap[$id]['type']) ){
        $bl = $pmap[$id]['type']==$type ;
      }else{
        //當他有兩個結果 代表一定存在
        // foreach($pmap[$id] as $v){
          // if($v['type']==$type){break;}
        // }
      }
    }
    return $bl;
  }
  
  private function addProcess($id,$super_id,$team_id,$super_team_id,$map,$sa){
    $record = array(
      "created_staff_id"=>$id,
      "created_department_id"=>$team_id,
      "year" => $this->year,
      "month" => $this->month,
      "owner_staff_id" => $super_id,
      "owner_department_id" => $super_team_id,
      "type" => $map,
      "path_staff_id" => "'".json_encode($sa)."'"
    );
    $this->process->addStorage($record);
    // $record['id'] = $prid;
    $this->process->addData( $record );
  }
  
  public function checkGeneralReport($staff_ary,$manager_id,$team_id){
    
    foreach($staff_ary as $val){
      $id = $val["id"];
      if(!$id){continue;}
      $a = $this->general->checkExist( $id, $this->year, $this->month );
      // var_dump($a);
      if(!$a){
        
        if($id === $manager_id){ continue; }
        
        $this->general->addStorage( array(
          "staff_id" => $id,
          "year" => $this->year,
          "month" => $this->month,
          "owner_staff_id" => $manager_id,
          "owner_department_id" => $team_id,
        ) );
        
      }
    }
    
  }
  
  public function releaseAllInsert(){
    $a = $this->leader->addRelease();
    $b = $this->general->addRelease();
    $c = $this->process->addRelease();
    return $a+$b+$c;
  }
  
  public function updateGeneralProcessing(){
    $process = $this->process->table_name;
    $general = $this->general->table_name;
    $leader = $this->leader->table_name;
    $year = $this->year;
    $month = $this->month;
    
    $sql = "update $general as a 
    LEFT JOIN $process as b on a.owner_department_id = b.created_department_id 
    and a.year = b.year and a.month = b.month and b.type = '2' 
    set processing_id = b.id where a.year = $year and a.month = $month";
    // LG($sql);
    $this->general->DB->doSQL($sql);
    
    
    $sql2 = "update $leader as a LEFT JOIN $process as b 
    on a.owner_department_id = b.created_department_id 
    and a.year = b.year and a.month = b.month and b.type = '1' 
    set processing_id = b.id where a.year = $year and a.month = $month";
    $this->leader->DB->doSQL($sql2);
    
    $this->deleteNoReportProcess();
    
    return $this;
  }
  
  public function updateReport($data,$process,$staff_id){
    $type = $process['type'];
    if($type==1){
      $table = $this->leader;
    }else{
      $table = $this->general;
    }
    $id = $data['id'];
    unset($data['id']);
    $chagne = $table->update($data,$id);
    if($chagne && $process['status_code']>=3 && count($data)>0){
      $pr_id = $process['id'];
      // $rprt_name = $this->record_process->table_name;
      $this->record_report->add(array(
        'operating_staff_id'=>$staff_id,
        'processing_id'=>$pr_id,
        // 'processing_record_id'=>"(select id from $rprt_name where processing_id = $pr_id order by update_date desc limit 1)",
        'processing_record_id'=>0,
        'report_id'=>$id,
        'report_type'=>$type,
        'changed_json'=>json_encode($data)
      ));
    }
    return $this;
  }
  
  public function deleteNoReportProcess(){
    $general = $this->general->table_name;
    $leader = $this->leader->table_name;
    $year = $this->year;
    $month = $this->month;
    
    $this->process->sql("delete from {table} where id not in ( 
    select processing_id from $general where year=$year and month = $month UNION 
    select processing_id from $leader where year=$year and month = $month ) 
    and year = $year and month = $month ");
    
    return $this;
  }
  
  
  
  protected function get_team(){
    return $this->team;
  }
  protected function get_leader(){
    return $this->leader;
  }
  protected function get_general(){
    return $this->general;
  }
  protected function get_process(){
    return $this->process;
  }
  
}
?>
