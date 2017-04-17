<?php
namespace Model\Business;

include_once __DIR__.'/Common/DBPropertyObject.php';
include_once __DIR__.'/Common/BaseReport.php';

class MonthlyReport extends DBPropertyObject{
  
  //實體表 :: 單表
  public $table_name = "rv_monthly_report";
  
  //欄位
  public $tables_column = Array(
    'id',
    'staff_id',
    'year',
    'month',
    'quality',
    'completeness',
    'responsibility',
    'cooperation',
    'attendance',
    'addedValue',
    'mistake',
    'total',
    'comment_count',
    'status',
    'releaseFlag',
    'bonus',
    'owner_staff_id',
    'processing_id',
    'owner_department_id'
  );
  
  use BaseReport;
  
}
?>
