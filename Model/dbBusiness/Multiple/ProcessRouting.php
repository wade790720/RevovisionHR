<?php
namespace Model\Business\Multiple;

include_once __DIR__.'/_MultipleSets.php';
include_once __DIR__.'/../MonthlyProcessing.php';
include_once __DIR__.'/../Department.php';
include_once __DIR__.'/../MonthlyReport.php';
include_once __DIR__.'/../MonthlyReportLeader.php';
include_once __DIR__.'/../RecordMonthlyProcessing.php';

use \Exception;

class ProcessRouting extends MultipleSets{
  
  protected $team;
  
  protected $process;
  
  protected $general;
  
  protected $leader;
  
  protected $year;
  
  protected $month;
  
  protected $record_porcess;
  
  protected $id;
  protected $owner;
  protected $supervisor;
  
  public function __construct($process_id){
    
    $this->process = new \Model\Business\MonthlyProcessing();
    $this->owner = $this->process->read($process_id)->getOwner();
    $this->id = $process_id;
    
    $this->team = new \Model\Business\Department();
    $this->supervisor = $this->team->getSuperWithManager( $this->owner );
    
    $this->record_porcess = new \Model\Business\RecordMonthlyProcessing();
    $this->leader = new \Model\Business\MonthlyReportLeader();
    $this->general = new \Model\Business\MonthlyReport();
    
    return $this;
  }
  
  public function processToSupervisor($operating_staff=0, $isAdmnin=false ){
    if($operating_staff==0){ $operating_staff = $this->owner; }
    if(!$isAdmnin && $operating_staff!=$this->owner){
      //不是擁有者 右不是 admin
      return false;
    }
    $team = $this->team->map('manager_staff_id',true);
    $super = $this->supervisor;
    $update = array('owner_staff_id'=>$super,'owner_department_id'=>$team[$super]['id'],'commited'=>1,'status_code'=>3);
    $this->process->update( $update ,$this->id);
    $this->record_porcess->add( array('operating_staff_id'=>$operating_staff, 'target_staff_id'=>$super, 'processing_id'=>$this->id, 'action'=>'commit', 'changed_json'=> json_encode($update) ) );
    return $this;
  }
  
  public function rejectToStaff( $staff_id, $reason, $operating_staff=0, $isAdmnin=false ){
    if(!$operating_staff){ $operating_staff = $this->owner; }
    $team = $this->team->map('manager_staff_id',true);
    $owner_team = $team[$staff_id];
    if( empty($owner_team) ){return false;}
    if($owner_team['upper_id']==0){
      //取消核准
      if($isAdmnin){
        $update = array('owner_staff_id'=>$staff_id,'owner_department_id'=>$owner_team['id'],'status_code'=>3);
        $add = array('operating_staff_id'=>$operating_staff, 'target_staff_id'=>$staff_id, 'processing_id'=>$this->id, 'action'=>'cancel', 'changed_json'=> json_encode($update) );
      }else{
        return false;
      }
    }else{
      //退回員工
      $update = array('owner_staff_id'=>$staff_id,'owner_department_id'=>$owner_team['id'],'status_code'=>4);
      $add = array('operating_staff_id'=>$operating_staff, 'target_staff_id'=>$staff_id, 'processing_id'=>$this->id, 'action'=>'return', 'changed_json'=> json_encode($update) );
    }
    
    $this->process->update( $update, $this->id);
    
    if($reason){$add['reason']=$reason;}
    $this->record_porcess->add( $add );
    return $this;
  }
    
  public function isFinally(){
    return $this->process->getOwner() == $this->supervisor;
  }
  
  public function done($operating_staff=0){
    if(!$this->process->isDone()){
      $team = $this->team->map('manager_staff_id',true);
      $super = $this->supervisor;
      $type = $this->process->data[0]['type'];
      $update = array('owner_staff_id'=>$super,'owner_department_id'=>$team[$super]['id'],'commited'=>1,'status_code'=>5);
      $this->process->update( $update ,$this->id);
      if($type==1){
        $this->leader->update( array('releaseFlag'=>'Y'), array('processing_id'=>$this->id) );
      }else{
        $this->general->update( array('releaseFlag'=>'Y'), array('processing_id'=>$this->id) );
      }
      $this->record_porcess->add( array('operating_staff_id'=>$operating_staff, 'target_staff_id'=>$super, 'processing_id'=>$this->id, 'action'=>'done', 'changed_json'=> json_encode($update) ) );
    }
    return $this;
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
