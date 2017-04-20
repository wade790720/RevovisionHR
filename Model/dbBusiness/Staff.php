<?php
namespace Model\Business;

include_once __DIR__.'/Common/DBPropertyObject.php';

class Staff extends DBPropertyObject{
  
  //實體表 :: 單表
  public $table_name = "rv_staff";
  
  //狀態
  public static $status = Array(
    1 => '正式',
    2 => '約聘',
    3 => '試用',
    4 => '離職'
  );
  
  //欄位
  public $tables_column = Array(
    'id',
    'staff_no',
    'title',
    'title_id',
    'post',
    'post_id',
    'name',
    'name_en',
    'account',
    'passwd',
    'email',
    'lv',
    'first_day',
    'last_day',
    'update_date',
    'status',
    'status_id',
    'department_id',
    'is_leader',
    'is_admin',
    'rank'
  );
  //override
  public function read($a=null,$b=0,$c=null){
    $order = isset($c)?$c:' order by rank desc , staff_no asc ';
    return parent::read($a,$b,$order);
  }
  //override
  public function select($a=null,$b=0,$c=null){
    $order = isset($c)?$c:' order by rank desc , staff_no asc ';
    return parent::select($a,$b,$order);
  }
  
  public function filterOnDuty($date1,$date2=''){
    $date = $this->DateTime($date1,true);
    $date2 = $this->DateTime($date2,true);
    $tmp=array();
    foreach($this->data as $i => $val){
      // if($val["status_id"]==4){continue;}
      $first = $this->DateTime($val["first_day"],true);
      $last = $this->DateTime($val["last_day"],true);
      if($last){
        if($last < $date){ continue; }
      }else if($val["status_id"]==4){
        continue;
      }else if($date2 && $first && $first >= $date2){
        continue;
      }
      array_push($tmp,$val);
    }
    $this->data = $tmp;
    return $this;
  }
  
  public function getOnDutyWithTeam($team_id,$col=null){
    return $this->select( $col, array("status_id"=>"<>4","department_id"=>"in($team_id)") );
  }
  
}
?>
