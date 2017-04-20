<?php

define("IS_DEBUG_MODE",1);

if(IS_DEBUG_MODE){
  ini_set('error_reporting', E_ALL | E_STRICT);
  ini_set('display_errors', 1);
}

define('BASE_PATH', str_replace("\\","/",dirname(__FILE__)));

define('WEB_ROOT', preg_replace("/^[\/\\\\]+/" , "/" ,"/".str_replace(str_replace("\\","/",$_SERVER["DOCUMENT_ROOT"]),"",BASE_PATH)));

define('REQUEST_ROOT', dirname($_SERVER['SCRIPT_NAME'])) ;


$_ROT = parseURI();



function U($uri){
  if(!preg_match("/^[\/]/i",$uri)){
    $file_path = debug_backtrace()[0]['file'];
    $file_path = dirname(str_replace('\\','/',$file_path));
    $rep = str_replace(BASE_PATH,'',$file_path);
    // $path = dirname($_SERVER['REQUEST_URI'])."/".$uri;
    $path = $rep.'/'.$uri;
  }else{
    $path =  WEB_ROOT.$uri;
    
  }
  if(IS_DEBUG_MODE){
    return $path .= "?v=".date("h:i:sa");
  }
  return $path;
}

function RP($uri){
  if(!preg_match('/^[\/\\\\]/',$uri)){
    $t = debug_backtrace();
    $f = $t[0]['file'];
    return preg_replace('/[\/\\\\]{1}[\w]*.php$/i','/',$f).$uri;
  }else{
    return BASE_PATH.$uri;
  }
}

function V($path){
  // $content = file_get_contents(RP("/View/$path"));
  $path = preg_replace('/^[\/\\\\]{1}/','',$path);
  $content = include(RP("/View/$path"));
  // var_dump($content);
  // return $content;
}

function parseURI(){
  $loc = str_replace(REQUEST_ROOT,'',$_SERVER['REQUEST_URI']);
  $loc = preg_replace('/\?.*/i','',$loc);
  $loc = bomb('/',$loc);
  $cout = count($loc);
  switch($cout){
    case 0: array_unshift($loc,'index');
    case 1: array_unshift($loc,'Frame');
    case 2: array_unshift($loc,'Index');
    case 3: break;
    default: array_splice($loc,3,$cout-3);
  }
  return $loc;
}

function bomb($key,$str=null){
  if(empty($str)){$str=$key;$key=',';}
  $loc = explode($key,$str);
  $i = 0;
  while($i < count($loc)){
    if( empty($loc[$i]) ){
      array_splice($loc,$i,1);
    }else{
      $i++;
    }
  }
  return $loc;
}

function r404(){
  global $_ROT;
  var_dump($_ROT);
  echo '404 Not Found';
  header("HTTP/1.0 404 Not Found");
  exit;
}

function LG($var){
  if(IS_DEBUG_MODE){
    var_dump($var);exit;
  }
}

function ErrorLog($str){
  stamp_log( $str, null, RP('/error_log') );
}

$time_start;stamp();
function stamp_log($str, $pre_time=null, $path=0){
  $log = (empty($log))? new \Logging() : $log;
  if($path===0){$path= '/php_log';}
  if(empty($pre_time)){
    $pre_time = $GLOBALS['time_start'];
  }
  $time_end = microtime(true);
  $spend_time = $time_end - $pre_time;
  $spend_string = "- Spend Time : ( $spend_time )\n";
  $log->lfile( $path );
  $log->lwrite("\n----------------------------------- START -----------------------------------\n ".$str."\n\r ".$spend_string."-----------------------------------  END  -----------------------------------\n");
  $log->lclose();
  if( IS_DEBUG_MODE ){echo "<br>$str <br> Spend Time = $spend_time<br>";}
}
function stamp(){
  global $time_start;
  $time_start = microtime(true);
}
function ms(){
  return (int) (microtime(true)*1000);
}

class Logging {
    private $log_file, $fp;
    private $file_path;
    public function __construct(){
      $this->file_path = RP('/Log');
      if (!file_exists($this->file_path)){ mkdir($this->file_path, 0777, true); }
    }
    public function lfile($path) { 
        $time = @date('Y_m_d');
        $this->log_file = $this->file_path.$path."_$time.txt";
    }
    public function lwrite($message) {
        if (!is_resource($this->fp)) {
            $this->lopen();
        }
        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        $time = @date('[d/M/Y:H:i:s]');
        fwrite($this->fp, "$time ($script_name) $message" . PHP_EOL);
    }
    public function lclose() {
        fclose($this->fp);
    }
    private function lopen() {
        $lfile = $this->log_file;
        $this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
    }
}

?>