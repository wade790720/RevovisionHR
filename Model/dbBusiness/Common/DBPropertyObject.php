<?php
namespace Model\Business;

include_once __DIR__.'/../../PropertyObject.php';
include_once __DIR__.'/../../DatabaseCenter.php';

use \DateTime;
use \Model\PropertyObject;
use \Model\DatabaseCenter;
use \Exception;

class DBPropertyObject extends PropertyObject
{
  
  public $table_name = "";
  
  public $tables_column = Array();
  
  protected $DB;
  
  protected $tmpWhere;
  
  protected $data;
  
  protected $map_key_cache;
  
  protected $maps;
  
  private $order_position = "_ORDER_POSITION";
  
  private $order_timestamp;
  
  private $insertStorage=array();
  
  public function __construct($db=null){
    if(isset($db)){
      $this->DB = $db;
    }else{
      $this->DB = new DatabaseCenter();
    }
  }
  
  public function create($a=null){
    return $this->DB->add($this->table_name, $a);
  }
  
  public function update($a=null,$b=null){
    if(is_numeric($b)){$b=array('id'=>$b);}
    return $this->DB->update($this->table_name, $a, $b);
  }
  
  public function select($a=null,$b=0,$c=null){
    if($b===0){$b=$a;$a=null;}
    if(is_numeric($b)){$b=array('id'=>$b);}
    $this->data = $this->DB->read($this->table_name, $a, $b, $c);
    $this->map_key_cache = "";
    return $this->data;
  }
  
  public function delete($a=null){
    if(is_numeric($a)){$a=array('id'=>$a);}
    return $this->DB->delete($this->table_name, $a);
  }
  
  public function add($a=null){
    $this->DB->add($this->table_name, $a);
    return $this;
  }
  
  public function addStorage($a=null){
    foreach($a as &$v){
      if(gettype($v)=='string' && !preg_match("/\'+/",$v)){ $v="'$v'"; }
    }
    $this->insertStorage[] = $a;
    return $this;
  }
  
  public function addRelease(){
    $this->DB->addBatch($this->table_name, $this->insertStorage);
    return count($this->insertStorage);
  }
  
  public function read($a=null,$b=0,$c=null){
    
    if($b===0){$b=$a;$a=null;}
    if(is_numeric($b)){$b=array('id'=>$b);}
    $this->data = $this->DB->read($this->table_name, $a, $b, $c);
    $this->map_key_cache = "";
    return $this;
  }
  
  public function sql($s){
    $s =str_replace('{table}',$this->table_name,$s);
    $this->data = $this->DB->doSQL($s);
    return $this;
  }
  
  public function map($key="id",$only=false,$array=false){
    if($this->map_key_cache == $key){return $this->maps;}
    $this->map_key_cache = $key;
    $this->maps = array();
    
    $akey = explode(',',$key);
    if(!(isset($this->data) && is_array($this->data))){
      $this->read();
    }
    
    $date = new DateTime();
    $this->order_timestamp = $date->getTimestamp();
    
    // $origin = $this->order_position.$this->order_timestamp;
    $origin = $this->order_position;
    
    foreach($this->data as $i => $val){
      $innerKeyAry=array();
      foreach($akey as &$v){
        $innerKeyAry[]=$val[$v];
      }
      
      $innerKey=join('-',$innerKeyAry);
      
      if(isset($innerKey)){
        $val[$origin] = $i;
        if(isset($this->maps[$innerKey]) && !$only){
          if( isset($this->maps[$innerKey][0]) ){
            array_push( $this->maps[$innerKey], $val );
          }else{
            $tmp = $this->maps[$innerKey];
            $this->maps[$innerKey] = array( $tmp , $val );
          }
        }else{
          if($array){
            $this->maps[$innerKey] = array( $val ); 
          }else{
            $this->maps[$innerKey] = $val; 
          }
        }
      };
    }
    return $this->maps;
  }
  
  public function join($condition = array(), $data, $matter=true){
    if(isset($data) && is_array($data) && is_array($condition) && count($condition) > 0){
      $ccl = count($condition);

      $a = 0;
      while($a < count($this->data) ){
        
        $b = $this->data[$a];$isMattch = false;
        
        foreach($data as $c => $d){
          $ci = 0;
          foreach($condition as $key => $key_2){
            if($b[$key]==$d[$key_2]){ $ci++; }
          }
          if($ci>=$ccl){
            // $this->data[$a] = array_merge($b, $d);
            $this->data[$a] = array_merge($d,$b);
            $isMattch = true;break;
          }
        }
        
        if($matter && !$isMattch){
          array_splice($this->data, $a, 1);
        }else{
          $a++;
        }
        
      }
      
    }
    return $this->data;
  }
  
  public function search($ary){
    if( !(isset($ary) && is_array($ary)) ){throw new Exception("Search Function Can't Receive Wrong Array.");}
    if(count($ary)>0){
      
      $tmp = array();
      $searchArray = array();
      foreach($ary as $whereKey => &$whereVal){
        if(preg_match("/[\<\>\=]+/",$whereVal)){
          $symbol = preg_replace('/.*?([\<\>\=]+).*/','$1',$whereVal);
          $val = preg_replace('/[\<\>\=]+/','',$whereVal);
        }else{
          $symbol = '==';
          $val = $whereVal;
        }
        if(gettype($val)=='string'){$val="'$val'";}
        $searchArray[$whereKey] = $symbol.$val;
      }
      
      foreach($this->data as $key => &$val){
        $loc = true;
        foreach($searchArray as $whereKey => &$where){
          if(gettype($val[$whereKey])=='string'){
            $render = "'".$val[$whereKey]."'".$where;
          }else{
            $render = $val[$whereKey].$where;
          }
          if(!($this->doOperatorsWithString($render))){
            $loc=false;break;
          }
        }
        
        if($loc){
          array_push($tmp,$val);
        }
        
      }
      return $tmp;
    }else{
      return $this->data;
    }
  }
  
  public function addData($record){
    
    array_push($this->data, $record);
    
    $this->clearCache();
  }
  
  protected function clearCache(){
    $this->map_key_cache = "";
  }
  
  public function invertColumn($ary){
    $new = $this->tables_column;
    if(is_array($ary)){
      foreach($ary as &$v){
        $key = array_search($v,$new);
        unset($new[$key]);
      }
    }else{
      $key = array_search($ary,$new);
      unset($new[$key]);
    }
    return $new;
  }
  
  public function trueColumn(&$ary){
    foreach($ary as $k => &$v){
      if(!in_array($k,$this->tables_column)){
        unset($ary[$k]);
      }
    }
    return $ary;
  }
  
    
  protected function get_data(){
    return isset($this->data) ? $this->data : $this->select() ;
  }
  protected function get_maps(){
    return $this->maps;
  }
  protected function get_table(){
    return $this->table_name;
  }
  protected function get_col(){
    return $this->tables_column;
  }
  protected function get_column(){
    if(!isset($this->data)){ $this->read(); }
    $col = array();
    foreach($this->data[0] as $key => $val){
      array_push($col,$key);
    }
    return $col;
  }
  protected function get_origin(){
    // return $this->order_position.$this->order_timestamp;
    return $this->order_position;
  }
  protected function get_DB(){
    return $this->DB;
  }
  
  protected function parseForTableColumn($ary,$col=null){
    $result = null;
    if(empty($col)){
      $col = $this->tables_column;
    }
    if(isset($ary) && isset($col)){
      $keys = Array();
      $values = Array();
      foreach($ary as $key => $val){
        if(in_array($key,$col)){
          array_push($keys,$key);
          array_push($values,$this->parseForSQL($val));
        }else{
          $result = null;break;
        }
      }
      $result = Array(
        "keys" => $keys,
        "values" => $values
      );
    }
    return $result;
  }
  
  protected function parseToArray($loc){
    return json_decode(json_encode($loc),true);
  }
  
  protected function doOperatorsWithString($str){
    return eval("return ($str);");
  }
  
}

?>