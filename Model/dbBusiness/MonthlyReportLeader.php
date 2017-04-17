<?php
namespace Model\Business;

include_once __DIR__.'/Common/DBPropertyObject.php';
include_once __DIR__.'/Common/BaseReport.php';

class MonthlyReportLeader extends DBPropertyObject{
  
  //實體表 :: 單表
  public $table_name = "rv_monthly_report_leader";
  
  //欄位
  public $tables_column = Array(
    'id',
    'staff_id',
    'year',
    'month',
    'target',
    'quality',
    'method',
    'error',
    'backtrack',
    'planning',
    'execute',
    'decision',
    'resilience',
    'attendance',
    'attendance_members',
    'addedValue',
    'mistake',
    'total',
    'comment_count',
    'status',
    'releaseFlag',
    'bonus',
    'processing_id',
    'owner_staff_id',
    'owner_department_id'
  );
  
  use BaseReport;
  
}
?>
