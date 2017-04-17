<?php
namespace Model\Business;

include_once __DIR__.'/Common/DBPropertyObject.php';

class Attendance extends DBPropertyObject{
  
  //實體表 :: 單表
  //出缺勤記錄表
  public $table_name = "rv_attendance";
  
  //欄位
  public $tables_column = Array(
    'id',
    'staff_id',
    'date',
    'checkin_hours',
    'checkout_hours',
    'work_hours_total',
    'late',
    'early',
    'nocard',
    'remark',
    'vocation_hours',
    'vocation_from',
    'vocation_to',
    'overtime_hours',
    'overtime_from',
    'overtime_to'
  );
  
}
?>
