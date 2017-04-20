<?php
namespace Model\Business;

include_once __DIR__.'/Common/DBPropertyObject.php';

class RecordPersonalComment extends DBPropertyObject{
  
  //實體表 :: 單表
  public $table_name = "rv_record_personal_comment";
  
  //欄位
  public $tables_column = Array(
    'id',
    'create_staff_id',
    'target_staff_id',
    'report_id',
    'report_type',
    'content',
    'status',
    'create_time'
  );
  
  public function __construct($db=null){
    parent::__construct($db);
  }
  
  public function refresh(){
    $select ="select a.id , a.report_type from rv_record_personal_comment as a 
    left join (select id,comment_id from rv_monthly_report) as b on a.report_id = b.id and a.report_type = 2 
    left join (select id,comment_id from rv_monthly_report_leader) as c on a.report_id = c.id and a.report_type = 1 
    where b.id is null and c.id is null ";
    $this->sql($select);
    $id_str ='(0';
    foreach($this->data as $val){
      $id_str .= ','.$val['id'];
    }
    $id_str.=')';
    // LG($sdata);
    // LG($id_str);
    $update = "update rv_record_personal_comment as a 
    left join (select id,staff_id from rv_monthly_report) as b on a.target_staff_id = b.staff_id and a.report_type = 2 
    left join (select id,staff_id from rv_monthly_report_leader) as c on a.target_staff_id = c.staff_id and a.report_type = 1 
    set 
    a.report_id = if(b.id>0, b.id, c.id) 
    where a.id in $id_str";
    $this->sql($update);
    
    $new_data = $this->select(array('id','report_id','report_type') , "where id in $id_str ");
    $update_data = array();
    foreach($new_data as $nv){
      $update_data[$nv['report_type']][$nv['report_id']][] = $nv['id'];
    }
    
    // LG($update_data);
   
    if( isset($update_data['1']) ){
      $table = 'rv_monthly_report_leader';
      foreach($update_data['1'] as $id => $lv){
        $comment_id = ','.join(',',$lv);
        $update_2 = "update $table set comment_id = concat(comment_id,'$comment_id') where id = $id ";
        // LG($update_2);
        $this->sql($update_2);
      }
    }
    
    if( isset($update_data['2']) ){
      $table = 'rv_monthly_report';
      foreach($update_data['2'] as $id => $gv){
        $comment_id = ','.join(',',$gv);
        $update_2 = "update $table set comment_id = concat(comment_id,'$comment_id') where id = $id ";
        $this->sql($update_2);
      }
    }
    
    
  }
  
}
?>
