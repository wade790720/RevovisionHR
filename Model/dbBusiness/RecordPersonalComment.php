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
  
}
?>
