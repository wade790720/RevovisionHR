<?php
namespace Model\Business;

include_once __DIR__.'/Common/DBPropertyObject.php';

class RecordStaff extends DBPropertyObject{
  
  //實體表 :: 單表
  public $table_name = "rv_record_staff";
  
  //欄位
  public $tables_column = Array(
    'id',
    'operating_staff_id',
    'staff_id',
    'changed_json',
    'update_date'
  );
  
  public function __construct($db=null){
    parent::__construct($db);
  }
  
}
?>
