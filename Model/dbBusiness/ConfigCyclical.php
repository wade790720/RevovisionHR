<?php
namespace Model\Business;

include_once __DIR__.'/Common/DBPropertyObject.php';

class ConfigCyclical extends DBPropertyObject{
  
  //實體表 :: 單表
  public $table_name = "rv_config_cyclical";
  
  protected $year;
  
  protected $month;
  
  //欄位
  public $tables_column = Array(
    'id',
    'year',
    'month',
    'day_start',
    'day_end',
    'day_cut_off',
    'update_date'
  );
  
  public function __construct($y=null,$m=null){
    
    parent::__construct();
    
    if($y && $m){ $this->getConfigWithDate($y,$m); }
    
  }
  
  public function getConfigWithDate($year,$month){
    $this->year = $year;
    $this->month = $month;
    $codition = array("year"=>$year,"month"=>$month);
    $data = $this->select( $codition );
    if( count($data)==0){
      $this->add( $codition );
      sleep(0.1);
      $data = $this->select( $codition );
    }
    
    $this->data = $this->collect()[0];
    // LG($result);
    return $this->data;
  }
  
  public function getNowConfig(){
    $now = explode(',',date('Y,m,d'));
    $year = (int)$now[0];
    $month = (int)$now[1];
    $day = (int)$now[2];
    // LG($day);
    if($month==12){
      $next = (int)$year +1;
      $where = " where year in ($year, $next) and month = $month ";
    }else{
      $next = (int)$month +1;
      $where = " where year = $year and month in ($month, $next) ";
    }
    
    $data = $this->read('*',$where,'order by year,month')->collect();
    if(count($data)==1){
      return $data[0];
    }else{
      if( $day > (int)$data[0]['day_end'] ){
        return $data[1];
      }
      return $data[0];
    }
  }
  
  private function collect(){
    foreach($this->data as &$val){
      $val['RangeEnd'] = $this->getThisDate($val['year'],$val['month'],$val['day_end']);
      $val['RangeStart'] = $this->getLastDate($val['year'],$val['month'],$val['day_start']);
    }
    return $this->data;
  }
  
  public function isLaunch(){
    $il = false;
    if( isset($this->data['monthly_launched']) && $this->data['monthly_launched']==1 ){ $il=true; }
    return $il;
  }
  
  public function getLastDate($y,$m,$day){
    return ( ($m==1) ? ($y-1)."-12-" : $y."-".($m-1)."-" ).$day;
  }
  public function getThisDate($y,$m,$day){
    return "$y-$m-$day";
  }
  
  
}
?>
