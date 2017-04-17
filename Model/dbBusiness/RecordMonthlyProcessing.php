<?php
namespace Model\Business;

include_once __DIR__.'/Common/DBPropertyObject.php';

class RecordMonthlyProcessing extends DBPropertyObject{
  
  //實體表 :: 單表
  public $table_name = "rv_record_monthly_processing";
  
  //欄位
  public $tables_column = Array(
    'id',
    'operating_staff_id',
    'process_id',
    'action',
    'reason',
    'changed_json',
    'update_date'
  );
  
  public function __construct($db=null){
    parent::__construct($db);
  }
  //override
  public function select($a=null,$b=0,$c=null){
    parent::select($a,$b,$c);
    foreach($this->data as &$val){
      if(isset($val['changed_json']))$val['changed_json'] = json_decode($val['changed_json'],true);
    }
    return $this->data;
  }
  //override
  public function read($a=null,$b=0,$c=null){
    parent::read($a,$b,$c);
    foreach($this->data as &$val){
      if(isset($val['changed_json']))$val['changed_json'] = json_decode($val['changed_json'],true);
    }
    return $this;
  }
  
}
?>
