<?php
namespace Model\Business;

include_once __DIR__.'/Common/DBPropertyObject.php';

class MonthlyProcessing extends DBPropertyObject{
  
  //實體表 :: 單表
  public $table_name = "rv_monthly_processing";
  
  //欄位
  public $tables_column = Array(
    'id',
    'status_code',
    'lv',
    'type',
    'commited',
    'created_staff_id',
    'year',
    'month',
    'owner_staff_id',
    'owner_department_id',
    'path_staff_id'
  );
  //status_code
  public static $statusCode = array(
    0 => '錯誤',
    1 => '準備',
    2 => '初步考評',
    3 => '審核階段',
    4 => '退回',
    5 => '核准'
  );
  //type
  public static $type = array(
    1 => '組長',
    2 => '組員'
  );
  
    
  public $year;
  
  public $month;
  
  public function __construct($db=null){
    parent::__construct($db);
  }
  //override
  public function select($a=null,$b=0,$c=null){
    parent::select($a,$b,$c);
    foreach($this->data as &$val){
      if(isset($val['path_staff_id']))$val['path_staff_id'] = json_decode($val['path_staff_id']);
    }
    return $this->data;
  }
  //override
  public function read($a=null,$b=0,$c=null){
    parent::read($a,$b,$c);
    foreach($this->data as &$val){
      if(isset($val['path_staff_id']))$val['path_staff_id'] = json_decode($val['path_staff_id']);
    }
    return $this;
  }
  
  public function isOnCreator($manager_id,$data=false){
    $data = ($data)?$data:$this->data[0];
    return $data['created_staff_id']==$manager_id;
  }
  
  public function isOwner($manager_id,$data=false){
    $data = ($data)?$data:$this->data[0];
    return $data['owner_staff_id']==$manager_id;
  }
  
  public function isRelation($manager_id,$data=false){
    $data = ($data)?$data:$this->data[0];
    $ary = (is_array($data['path_staff_id']))?$data['path_staff_id']:json_decode($data['path_staff_id']);
    return in_array( $manager_id, $ary );
  }
  
  public function isLaunch($data=false){
    $data = ($data)?$data:$this->data[0];
    return (int)$data['status_code'] > 1;
  }
  
  public function isDone($data=false){
    $data = ($data)?$data:$this->data[0];
    return $data['status_code'] == 5;
  }
  
  
  
  public function getCreator(){
    if( empty($this->data[0]) ){return null;}
    return $this->data[0]['created_staff_id'];
  }
  public function getOwner(){
    if( empty($this->data[0]) ){return null;}
    return $this->data[0]['owner_staff_id'];
  }
  
  
  public function shuntIdWithType(){
    $loc = array();
    foreach($this->data as $val){
      $loc[$val['type']][] = $val['id'];
    }
    return $loc;
  }
  
}
?>
