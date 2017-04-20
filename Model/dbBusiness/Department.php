<?php
namespace Model\Business;

include_once __DIR__.'/Common/DBPropertyObject.php';

class Department extends DBPropertyObject{
  
  //實體表 :: 單表
  public $table_name = "rv_department";
  
  public static $SubTeam = "subTeam";
  
  protected static $CeoStaffId;
  
  //欄位
  public $tables_column = Array(
    "id",
    "lv",//部門的等級 1.運維 2.部門 3.處 4.組
    "unit_id",//部門代號 不可重覆
    "name",//部門名稱
    "supervisor_staff_id",//隸屬上層 
    "manager_staff_id",//部門主管
    'duty_shift',
    'upper_id',//部門結構上層
    'enable',//是否開啟
    'update_date'
  );
  
  //override
  public function read($a=null,$b=0,$c=null){
    $order = isset($c)?$c:' order by unit_id asc';
    return parent::read($a,$b,$order);
  }
  //override
  public function select($a=null,$b=0,$c=null){
    $order = isset($c)?$c:' order by unit_id asc';
    return parent::select($a,$b,$order);
  }
  //取得下層單位
  public function getLower($id){
    $lower = array();
    $data = $this->data;
    foreach($data as $v){
      if($v['upper_id']==$id){ $lower[$v['id']] = $v; }
    }
    return $lower;
  }
  //取得所有下層單位的ID
  public function getLowerIdArray($id){
    $lower = array();
    $inner_id = $id;
    $pos = 0;
    do{
      foreach($this->getLower($inner_id) as $key => $val){
        array_push($lower, $key);
      }
      $inner_id = (isset($lower[$pos])) ? $lower[$pos] : 0;
      $pos++;
    }while($pos <= count($lower));
    return $lower;
  }
  //判斷單位是不是下層
  public function isLower($id,$sub_id){
    $ary = $this->getLowerIdArray($id);
    return in_array($sub_id,$ary);
  }
  //用主管去找 最上頭的負責人
  public function getSuperWithManager($manager_id){
    if(!$manager_id){ return 1; }
    $maps = $this->map('manager_staff_id');
    return $maps[$manager_id]['supervisor_staff_id'];
  }
  //用主管 取得上層單位佬大
  public function getSuperArrayWithManager($manager_id,$end_id = 0){
    $a = $manager_id;
    if($end_id==0){
      $end_id = $this->getCeoStaffId();
    }
    $map = $this->map('manager_staff_id');
    $res = array();
    do{
      $b = $a;
      array_push($res, $b);
      if($a==$end_id){break;}
      $a = isset($map[$b]['supervisor_staff_id']) ? $map[$b]['supervisor_staff_id'] : $end_id;
    }while(!($a == $b));
    return $res;
  }
  //用id 取得上層單位關係
  public function getUpperIdArray($id,$filter_no_manager=false){
    $a = $id;
    $map = $this->map();
    $res = array();
    do{
      $b = $a;
      if( isset($map[$b]) ){
        if($filter_no_manager){
          $map[$b]['manager_staff_id']>0 && array_push($res, $b);
        }else{
          array_push($res, $b);
        }
      }else{
        break;
      }
      $a = $map[$b]['upper_id'];
    }while(!($a == $b));
    return $res;
  }
  //建立樹狀結構
  public function getTree($in=null){
    $data = ($in)?$in:$this->data;
    // $map = $this->map();
    $sub = self::$SubTeam;
    $tree = array( 0=>array($sub=>array() ) );
    $lv = 1;
    $maxLv = 5;
    
    $layer = array( &$tree );
    
    while($lv<$maxLv){
      
      $layer_next = array();
      
      foreach($data as $order=>$v){
        if(!($v['lv']==$lv)){continue;}
        $up_id = $v['upper_id'];
        
        // $data[$order]['order'] = $order;
        // $data[$order][$sub_leader] = array();
        // var_dump($layer);
        foreach($layer as $id=>$loc){
          $singleLayer = &$layer[$id];
          
          if( isset($singleLayer[ $up_id ]) ){
            // $singleLayer[ $up_id ][$sub][$v['id']] = array( 'self'=>$v,$sub=>array(),$sub_leader=>array() );
            $singleLayer[ $up_id ][$sub][$v['id']] = array( 'self'=>$v );
            // $singleLayer[ $up_id ][$sub_path] = array();
            $layer_next[] = &$singleLayer[$up_id][$sub];
          }
          
        }

      }
      // unset($layer);
      $layer = $layer_next;
      $lv++;
    }
    
    return $tree[0][$sub];
  }
  
  public function refreshRelation(){
    $sql = 'update {table} as a 
    left join (select id, upper_id, manager_staff_id as mid from {table} ) as b on a.upper_id = b.id
    left join (select id, upper_id, manager_staff_id as mid from {table} ) as c on if(b.upper_id>0,b.upper_id,0) = c.id
    left join (select id, upper_id, manager_staff_id as mid from {table} ) as d on if(c.upper_id>0,c.upper_id,0) = d.id
    set a.supervisor_staff_id = if(b.mid>0, b.mid, if(c.mid>0,c.mid, if(d.mid>0, d.mid, a.manager_staff_id) ) ) ';
    $this->sql($sql);
    return $this;
  }
  
  public function getCeoStaffId(){
    if( isset($this->CeoStaffId) ){
      $id = $this->CeoStaffId;
    }else{
      $upMap = $this->map('upper_id',true);
      $id = $upMap[0]['manager_staff_id'];
    }
    return $id;
  }
  
  
}
?>
