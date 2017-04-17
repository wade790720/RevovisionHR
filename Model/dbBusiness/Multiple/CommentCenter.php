<?php
namespace Model\Business\Multiple;

include_once __DIR__.'/_MultipleSets.php';
include_once __DIR__.'/../Staff.php';
include_once __DIR__.'/../RecordPersonalComment.php';
include_once __DIR__.'/../RecordPersonalCommentChanged.php';
include_once __DIR__.'/../MonthlyReport.php';
include_once __DIR__.'/../MonthlyReportLeader.php';
include_once __DIR__.'/../ConfigCyclical.php';

use \Exception;

/*
評論控制
*/
class CommentCenter extends MultipleSets{
  
  protected $staff;
  
  protected $comment;
  protected $comment_changed;
  
  protected $general;
  
  protected $leader;
  
  protected $config;
  
  public function __construct(){
    $this->staff = new \Model\Business\Staff();
    $this->comment = new \Model\Business\RecordPersonalComment();
    $this->comment_changed = new \Model\Business\RecordPersonalCommentChanged();
    $this->general = new \Model\Business\MonthlyReport();
    $this->leader = new \Model\Business\MonthlyReportLeader();
    $this->config = new \Model\Business\ConfigCyclical();
  }
  
  public function addComment($ary){
    
    $report = $this->whichReport($ary,true);
    if(!$report){return false;}
    // $date = $this->config->getNowConfig();
    // LG($date);
    
    $cid = $this->comment->create(array(
      'create_staff_id' => $ary['self_id'],
      'target_staff_id' => $ary['target_staff_id'],
      'report_id' => $ary['report_id'],
      'report_type' => $ary['report_type'],
      'content' => $ary['content']
    ));
    
    $report->sql("update {table} set comment_id = concat(comment_id,',$cid') where id=".$ary['report_id']);
    
    return true;
    
  }
  
  public function getComment($ary){
    
    $report = $this->whichReport($ary);
    if(!$report){return false;}
    if(count($report->data)==0){$report->read($ary['report_id']);}
    
    
    // $data = $this->comment->select(array('report_id'=>$ary['report_id']));
    $staff_table = $this->staff->table_name;
    $data = $this->comment->sql("select a.* , 
    b.name as _created_staff_name , b.name_en as _created_staff_name_en , 
    c.name as _target_staff_name , c.name_en as _target_staff_name_en 
    from {table} as a 
    left join $staff_table as b on a.create_staff_id = b.id 
    left join $staff_table as c on a.target_staff_id = c.id 
    where a.report_id = ".$ary['report_id']." and a.status = 1  
    and a.report_type = ".$ary['report_type'])->data;
    // LG($data);
    return $data;
  }
  
  public function deleteComment($id,$operating){
    $res = $this->comment->update(array('status'=>0), array('id'=>$id,'create_staff_id'=>$operating));
    if($res){ 
      $comment = $this->comment->select($id)[0];
      if($comment['report_type']==1){
        $report = $this->leader;
      }else{
        $report = $this->general;
      }
      $report_id = $comment['report_id'];
      $report->sql("update {table} set comment_id = REPLACE(comment_id,'$id','') where id = $report_id");
    }
    return $res;
  }
  
  public function updateComment($id,$operating,$content){
    $upd = $this->comment->update(array('content'=>$content),array('id'=>$id,'status'=>1,'create_staff_id'=>$operating));
    if($upd){
      $comment_table = $this->comment->table_name;
      $this->comment_changed->sql(" insert into {table} (comment_id,create_staff_id,target_staff_id,content) 
      select id,create_staff_id,target_staff_id,content from $comment_table where id = $id");
    }
    return $upd;
  }
  
  private function whichReport(&$w,$release=false){
    $col = array('id','staff_id','comment_id');
    if($w['mode']==1){
      
      $condi = array('staff_id'=>$w['target_staff_id'], 'year'=>$w['year'], 'month'=>$w['month'] );
      
      $report = $this->leader->read( $col, $condi );
      if(count($report->data)==0){
        $w['report_type'] = 2;
        $report = $this->general->read( $col, $condi );
      }else{
        $w['report_type'] = 1;
      }
      if(count($report->data)==0){return false;}
      
      $w['report_id'] = $report->data[0]['id'];
      
    }else{
      if($w['report_type']==1){
        $report = $this->leader;
      }else{
        $report = $this->general;
      }
      $condi = array('id'=>$w['report_id']);
      if($release){$condi['releaseFlag']='N';}
      $report->read( $col ,$condi);
      if(count($report->data)==0){return false;}
      $w['target_staff_id'] = $report->data[0]['staff_id'];
    }
    return $report;
  }
  
  
  protected function get_comment(){
    return $this->comment;
  }
  protected function get_staff(){
    return $this->staff;
  }
  
}
?>
