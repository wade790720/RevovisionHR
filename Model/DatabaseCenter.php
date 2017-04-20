<?php
namespace Model;

if(is_null(BASE_PATH)){
  define('BASE_PATH', "../".dirname(__FILE__));
}

use \PDO;
use \Exception;

class DatabaseCenter {
  
  protected $DB;
  
  protected $CONF;
  
  private $limit_record = 1000;
  
  private $log_time;
  
  public function __construct(){
    
    $this->CONF = include(BASE_PATH."/Config/db_config.php");
    $this->limit_record = $this->CONF['DB_CONFIG']['limit_record'];
    
  }
  
  private function buildPDOConnection(){
    
    date_default_timezone_set("Asia/Taipei");
    
    $PdoOptions = array(
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    );try {
      
      $db_server = $this->CONF['DB_CONFIG']['server'];
      $db_content = $this->CONF['DB_CONFIG']['content'];
      $db_user = $this->CONF['DB_CONFIG']['user'];
      $db_pwd = $this->CONF['DB_CONFIG']['pwd'];
      
      
      $pdo = new PDO("mysql:host=$db_server;dbname=$db_content" , $db_user , $db_pwd , $PdoOptions);
      $pdo ->exec('SET CHARACTER SET utf8');
      
      
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage());
    }
    return $pdo;
  }
  
  public function doSQL($sql){
    if(empty($this->DB)){
      $this->DB = $this->buildPDOConnection();
    }
    
    return $this->doPDO($sql);
  }
  
  public function getPDO(){
    if(empty($this->DB)){
      $this->DB = $this->buildPDOConnection();
    }
    return $this->DB;
  }
  
  public function read($table,$match=null,$where=null,$order=null){
    if(!isset($table)){throw new Exception("Not Defined Table Name.");}
    if(isset($match)){
      if(is_array($match)&&count($match)>0){
        $cols = " ";
        foreach($match as $val){
          $cols.= $val.",";
        }
        $cols = preg_replace("/\,$/"," ",$cols);
        
      }else{
        $cols = " * ";
      }
    }else{
      $cols = " * ";
    }
    
    $case = $this->stringParseWhere($where);
    
    $orderBY = $this->stringParseOrderBy($order);
    
    $sql = "select $cols from $table $case $orderBY limit ".$this->limit_record;
    return $this->doSQL($sql);
  }
  
  public function add($table,$set = array()){
    if(!isset($table)){throw new Exception("Not Defined Table Name.");}
    if(is_array($set) && count($set) > 0){
      $cols = array();
      $vals = array();
      foreach($set as $col => $val){
        $val = $this->valueParseForSQL($val);
        array_push($cols,$col);
        array_push($vals,$val);
      }
      $allcol = join(",",$cols);
      $allval = join(",",$vals);
      $sql = "insert into ".$table." (".$allcol.") value (".$allval.");";
      // var_dump($sql);
      $this->doSQL($sql);
      
    }else{
      return false;
    }
    
    return $this->DB->lastInsertId();
    
  }
  
  public function addBatch($table,$sets){
    if(is_array($sets) && count($sets) > 0){
      
      $cols = array();
      foreach($sets[0] as $col => $val){
        array_push($cols,$col);
      }
      // var_dump($cols);
      $values = array();
      foreach($sets as $col => &$val){
        // $val = $this->valueParseForSQL($val);
        // LG($val);
        $values[] = "(".(join(',',$val)).")";
      }
      
      $allvalues = join(',',$values);
      
      $allcol = join(",",$cols);
      $sql = "insert into ".$table." (".$allcol.") value ".$allvalues.";";
      // LG($sql);
      $this->doSQL($sql);
    }else{
      return false;
    }
    return $this->DB->lastInsertId();
  }
  
  public function update($table, $set=array(), $where=null){
    if(!isset($table)){throw new Exception("Not Defined Table Name.");}
    if(is_array($set) && count($set) > 0 && isset($where)){
      
      $case = $this->stringParseWhere($where);
      
      $updArray = array();
      foreach($set as $key => $val){
        $val = $this->valueParseForSQL($val);
        array_push($updArray, "$key = $val");
      }
      
      $updset = join(",",$updArray);
      $sql = "update $table set $updset $case";
      
      return $this->doSQL($sql);
    }else{
      return false;
    }
    
  }
  
  public function delete($table, $where=null){
    if(!isset($table)){throw new Exception("Not Defined Table Name.");}
    if(isset($where)){
      
      $case = $this->stringParseWhere($where);
    
      $sql = "delete from $table $case";
      
      $this->doSQL($sql);
      
    }else{
      return false;
    }
    return true;
    
  }
  
    
  protected function doPDO($sql,$mode='array'){
    
    $this->log_time = microtime(true);
    
    try{
      $sth = $this->DB->prepare($sql);
      $sth->execute();
      if($mode=='object'){
        $result = $sth->fetchAll(PDO::FETCH_OBJ);
      }else{
        if( preg_match( '/select/i', $sth->queryString) ){
          $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        }else if( preg_match( '/update/i', $sth->queryString) ){
          $result = $sth->rowCount();
        }else{
          $result = null;
        }
      }
      $this->writeDBLog($sql);
    }catch (Exception $e){
      if(IS_DEBUG_MODE){
        var_dump($sql);
        var_dump($e->getMessage());
      }
      $this->writeDBLog($sql,$e->getMessage());
      $result = array();
    }
    return $result;
  }
  
  protected function arrayParseForSQL($ary=array()){
      $loc = array();
      foreach($ary as $key => $val){
        $loc_val = $this->valueParseForSQL($val);
        $loc[$key] = $loc_val;
      }
      return $loc;
  }
  
  protected function valueParseForSQL($val){
    if(is_numeric($val)){ $val = (int)$val; }
    $type = gettype($val);
    $newValue = $val;
    switch($type){
      case "integer": break;
      case "array":
      case "object":
        $newValue = "'".(json_encode($val))."'";
      break;
      case "boolean":
      case "string":
      default:
        if(preg_match('/[\,\[\]\(\)]+/',$val)){
          //is array or in
          if(preg_match('/[\{\}]+/',$val)){
            //js json
            $newValue = "'".($val)."'";
          }else if(!preg_match('/^[\(\)]+/',$val)){
            $newValue = "'".($val)."'";
          }else{
            $newValue = ($val);
          }
        }else{
          $newValue = "'".($val)."'";
        }
    }
    return $newValue;
  }
  
  protected function stringParseWhere($ary){
    if(isset($ary) && count($ary)>0){
      if(is_array($ary)){
        $caseArray = array();
        foreach($ary as $key => $val){
          $match = preg_match("/^[\>\<\=i\s]/",$val);
          if($match){
            $symbol = preg_replace("/([\>\<\=]+|in).*/"," $1 ",$val);
            $val = preg_replace("/^([\>\<\=]+|in)/","",$val);
          }else{
            $symbol = ' = ';
          }
          $val = $this->valueParseForSQL($val);
          // LG($val);
          $full = $key.$symbol.$val;
          // LG($full);
          array_push($caseArray,$full);
        }
        $case = " where ".join(" and ",$caseArray);
      }else{
        $case = $ary;
      }
    }else{
      $case = " ";
    }
    // LG($case);
    return $case;
  }
  
  protected function stringParseOrderBy($ord){
    if(isset($ord) && count($ord)>0){
      if(is_array($ord)){
        $caseArray = array();
        foreach($ord as $key => $val){
          $val = ($val=="DESC")?$val:"ASC";
          $val = $key." ".$val;
          array_push($caseArray,$val);
        }
        $order = " ORDEY BY ".join(",",$caseArray);
      }else{
        $order = $ord;
      }
    }
    else{
      $order = " ";
    }
    return $order;
  }
  
  protected function writeDBLog($str,$error=''){
    $time_end = microtime(true);
    $spend_time = $time_end - $this->log_time;
    if(empty($error) && $spend_time <= $this->CONF['DB_CONFIG']['long_time'] ){return;}
    if(!empty($error)){$error=" - $error \n";$file = '/db_error';}else{$file = '/db_longtime';}
    
    $log = (empty($log))? new \Logging() : $log;
    $log->lfile( $file );
    $log->lwrite("\n----------------------------------- Command_START -----------------------------------\n ".$str."\n\r$error - Spend Time : ( ".$spend_time." )\n"."-----------------------------------  Command_END  -----------------------------------\n");
    
    $log->lclose();
  }
  
}

?>
