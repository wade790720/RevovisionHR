<?php

  include(__DIR__."/../Global.php");
  
  include(BASE_PATH."/Model/SessionCenter.php");
  
  include_once(BASE_PATH."/Model/PropertyObject.php");
  
  include_once(BASE_PATH."/Model/JsonGeneralFomat.php");
  
  use Model\JsonGeneralFomat as JsonFomat;
  
  class ApiCore extends Model\PropertyObject{
    
    protected $postData;
    
    protected $jsonFomat;
    
    public $SC;
    
    public function __construct($data=Array()){
      
      $this->SC = new Model\SessionCenter();
      
      $this->postData = $data;
      
      $this->jsonFomat = new JsonFomat();
      
    }
    
    public function setMsg($str){
      $this->jsonFomat->setMsg($str);
      return $this;
    }
    
    public function setArray($str){
      try{
        $this->jsonFomat->setArray($str);
      }catch(Exception $e){
        return false;
      }
      return true;
    }
    
    public function getJSON(){
      return $this->jsonFomat->getResult();
    }
    
    public function getArray(){
      return $this->jsonFomat->getArray();
    }
    
    public function checkPost($map,$post=Array()){
      if(count($post)==0){$post = $this->postData;}
      foreach($map as $val){
        if(!(is_string($val) && isset($post[$val]))){
          $this->jsonFomat->setStatus(JsonFomat::$ERROR_PARAM);
          return false;
        }
      }
      return true;
    }
    
    public function post($key,$type='value'){
      $loc = false;
      if( isset($this->postData[$key]) ){
        $loc = $this->postData[$key];
        switch($type){
          case "Array":case "array": $loc = explode(',',preg_replace('/[^(\w\,)]+/','',$loc)); break;
          case "Int":case "int": $loc = (int) preg_replace('/[^\d]+/','',$loc); break;
        }
      }
      return $loc;
    }
    
    public function getPost(){
      return $this->postData;
    }
    
    public function denied($res=null){
      $this->jsonFomat->setStatus(JsonFomat::$DENIED);
      if($res){$this->jsonFomat->setMsg($res);}
      print $this->getJSON();exit;
    }
    
    public function sqlError($res=null){
      $this->jsonFomat->setStatus(JsonFomat::$ERROR_SQL);
      if($res){$this->jsonFomat->setMsg($res);}
      print $this->getJSON();exit;
    }
    
    public function inputWrong(){
      $this->jsonFomat->setStatus(JsonFomat::$INPUT_WRONG);
    }
    
    public function inputReject(){
      $this->jsonFomat->setStatus(JsonFomat::$INPUT_REJECT);
    }
    
    
    public function isPast($d){
      $date = strtotime($d);
      $now = strtotime('today');
      return $now >= $date;
    }
    
    public function isFuture($d){
      $date = strtotime($d);
      $now = strtotime('today');
      return $now < $date;
    }
    
    public function condition($ary){
      $tmp = array();
      foreach($ary as $k => $v){
        if( !empty($v) ){ $tmp[$k] = $v; }
      }
      return $tmp;
    }
    
    public function parseDate($b){
      if(is_a($b,'DateTime')){
        $date = $d;
      }else{
        $date = new DateTime($b);
      }
      return $date;
    }
    
    public function getFiles(){
      $i = 0;
      $files = array();
      foreach ($_FILES as $file) {
          if (is_string($file['name'])) {
              $files[$i] = $file;
              $i++;
          }else if (is_array($file['name'])) {
              //暫無
          }
      }
      return $files;
    }
    
    public function uploadFile($fileInfo, $allowExt = array('xlsx', 'xls'), $maxSize = 2097152, $flag = false, $uploadPath = 'rv_uploads'){
      // 存放錯誤訊息
      $res = array();
      // 取得上傳檔案的擴展名
      $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION); 

      // 確保檔案名稱唯一，防止重覆名稱產生覆蓋
      $uniName = md5(uniqid(microtime(true), true)) . '.' . $ext;
      $destination = $uploadPath . '/' . $uniName;
      
      // 判斷是否有錯誤
      if ($fileInfo['error'] > 0) {
          // 匹配的錯誤代碼
          switch ($fileInfo['error']) {
              case 1:
                  $res['mes'] = $fileInfo['name'] . ' 上傳的檔案超過了 php.ini 中 upload_max_filesize 允許上傳檔案容量的最大值';
                  break;
              case 2:
                  $res['mes'] = $fileInfo['name'] . ' 上傳檔案的大小超過了 HTML 表單中 MAX_FILE_SIZE 選項指定的值';
                  break;
              case 3:
                  $res['mes'] = $fileInfo['name'] . ' 檔案只有部分被上傳';
                  break;
              case 4:
                  $res['mes'] = $fileInfo['name'] . ' 沒有檔案被上傳（沒有選擇上傳檔案就送出表單）';
                  break;
              case 6:
                  $res['mes'] = $fileInfo['name'] . ' 找不到臨時目錄';
                  break;
              case 7:
                  $res['mes'] = $fileInfo['name'] . ' 檔案寫入失敗';
                  break;
              case 8:
                  $res['mes'] = $fileInfo['name'] . ' 上傳的文件被 PHP 擴展程式中斷';
                  break;
          }

          // 直接 return 無需在往下執行
          return $res;
      }

      // 檢查檔案是否是通過 HTTP POST 上傳的
      if (!is_uploaded_file($fileInfo['tmp_name']))
          $res['mes'] = $fileInfo['name'] . ' 檔案不是通過 HTTP POST 方式上傳的';
      
      // 檢查上傳檔案是否為允許的擴展名
      if (!is_array($allowExt))  // 判斷參數是否為陣列
          $res['mes'] = $fileInfo['name'] . ' 檔案類型型態必須為 array';
      else {
          if (!in_array($ext, $allowExt))  // 檢查陣列中是否有允許的擴展名
              $res['mes'] = $fileInfo['name'] . ' 非法檔案類型';
      }

      // 檢查上傳檔案的容量大小是否符合規範
      if ($fileInfo['size'] > $maxSize)
          $res['mes'] = $fileInfo['name'] . ' 上傳檔案容量超過限制';

      // 檢查是否為真實的圖片類型
      if ($flag && !@getimagesize($fileInfo['tmp_name']))
          $res['mes'] = $fileInfo['name'] . ' 不是真正的圖片類型';

      // array 有值表示上述其中一項檢查有誤，直接 return 無需在往下執行
      if (!empty($res))
          return $res;
      else {
          // 檢查指定目錄是否存在，不存在就建立目錄
          if (!file_exists($uploadPath))
              mkdir($uploadPath, 0777, true);
          
          // 將檔案從臨時目錄移至指定目錄
          if (!@move_uploaded_file($fileInfo['tmp_name'], $destination))  // 如果移動檔案失敗
              $res['mes'] = $fileInfo['name'] . ' 檔案移動失敗';


          $res['mes'] = '檔案已上傳';
          $res['dest'] = $destination;

          return $res;
      }
    }
    
  }
  
?>

