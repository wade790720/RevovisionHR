<?php 
// echo $_SERVER["REQUEST_URI"];exit;
include_once(__DIR__.'/../../Global.php');
include_once(BASE_PATH."/Model/SessionCenter.php");
$SC = new Model\SessionCenter();
?>
(function ($) {
  'use strict';
  var self = window.API = {};
  self.$ = $(self);
  
  var API_PATH = '<?=dirname($_SERVER["REQUEST_URI"])?>'.replace(/\/[^\/]+$/,"/");
  var Member_PATH = API_PATH+"Member/";
  var Data_PATH = API_PATH+"Data/";
  var Absence_PATH = Data_PATH+"Absence/";
  var Setting_PATH = API_PATH+"Setting/";
  var Excel_PATH = API_PATH+"Excel/";
  self.ROOT = '<?=WEB_ROOT?>';
  
  self.member = <?=$SC->getJSON()?>;
  
  self.format = function(jsf){
    return new grenalJSONFormat(jsf);
  }
  
  
  
  self.loginWithData = function(data){
    //必須 username, passwd
    return $.post(Member_PATH+"login", data);
  }
  self.logout = function(){
    return $.get(Member_PATH+"logout");
  }
  self.getDepartmentList = function(data){
    //取得所有組織列表、並檢察組織關係
    //必須 year, mouth
    return $.post(Data_PATH+"getDepartmentList", data);
  }
  self.checkDepartment = function(data){
    //檢察組織關係 產生 報表,考評表
    //可選 year, mouth, del
    return $.post(Data_PATH+"getDepartmentList?&check=true", data);
  }
  self.getMonthlyProcessWithCreator = function(data){
    //取得創作者的月績效考評表
    //必須 
    //可選 year, month, staff_id
    return $.post(Data_PATH+"Monthly/getMonthlyProcessWithCreator", data);
  }
  self.getMonthlyProcessWithOwner = function(data){
    //取得當前擁有者的月績效考評表
    //必須
    //可選 year, month, staff_id
    return $.post(Data_PATH+"Monthly/getMonthlyProcessWithOwner", data);
  }
  self.getMonthlyProcessByAdmin = function(data){
    //取得當月 月績效考評表
    //必須 yaer, month (super user)
    //可選 status_code
    return $.post(Data_PATH+"Monthly/getMonthlyProcessByAdmin", data);
  }
  self.getMonthlyReportWhenRelease = function(data){
    //取得已經核准的月績效報表
    //必須 year, month
    //可選 release
    return $.post(Data_PATH+"Monthly/getMonthlyReportWhenRelease", data);
  }
  self.downloadMonthlyReportWhenRelease = function(data){
    //下載已經核准的月績效報表
    //必須 year, month
    //可選 release
    // console.log(Excel_PATH+"downloadMonthly?year="+data.year+"&month="+data.month+(data.release?"&release=true":""));
    if(!(data && data.year && data.month)){ return false; }
    return window.open(Excel_PATH+"downloadMonthly?year="+data.year+"&month="+data.month+(data.release?"&release=true":""),'_blank');
  }
  self.getMonthlyReport = function(data){
    //取得月績效報表
    //必須 processing_id  |or|  manager_id, year, month  |or|  department_id, year, month
    return $.post(Data_PATH+"Monthly/getMonthlyReport", data);
  }
  
  
  self.saveReport = function(data){
    //儲存修改的月績效報表
    //必須 report[ id,processing_id ]
    //可選 一般 report[ quality, completeness, responsibility, cooperation, attendance, addedValue, mistake, bonus]
    //可選 主管 report[ target, quality, method, error, backtrack, planning, execute, decision, resilience, attendance, attendance_members, addedValue, mistake, bonus]
    return $.post(Data_PATH+"Monthly/saveReport", data);
  }
  self.commitMonthly = function(data){
    //送審核月績效考評表
    //必須 processing_id
    //可選 reason
    return $.post(Data_PATH+"Monthly/commitMonthly", data);
  }
  self.getMonthlyRejectList = function(data){
    //必須 processing_id
    return $.post(Data_PATH+"Monthly/getRejectList", data);
  }
  self.rejectMonthly = function(data){
    //必須 processing_id, staff_id
    //可選 reason , turnback = 1 / 0 (是 admin 系統管理員)
    return $.post(Data_PATH+"Monthly/rejectMonthly", data);
  }
  self.launchMonthly = function(data){
    //開始啟用 月考評
    //必須 year, month   (是 admin 系統管理員)
    return $.post(Data_PATH+"Monthly/launchMonthly", data);
  }
  self.pauseMonthly = function(data){
    //暫時關閉 月考評
    //必須 year, month   (是 admin 系統管理員)
    return $.post(Data_PATH+"Monthly/pauseMonthly", data);
  }
  
  self.getMonthlyProcessHistory = function(data){
    //取得月考評單歷史記錄
    //必須 processing_id
    return $.post(Data_PATH+"Monthly/getMonthlyProcessHistory", data)
  }
  
  
  
  self.addAbsence = function(file){
    //上傳出缺勤
    //必須 file
    return $.ajax({
      url : Absence_PATH+"add",
      type : "POST",
      data : file,
      dataType : "JSON",
      cache : false,
      contentType : false,
      processData: false
    });
  }
  
  self.getAbsence = function(data){
    //取得出缺勤
    //必須 year, month
    //可選 team_id[], staff_id[]
    return $.post(Absence_PATH+"get", data);
  }
  
    
  self.downloadAbsence = function(data){
    //下載出缺勤 excel 表
    //必須 year, month
    //可選 team_id[], staff_id[]
    var str = '',ary=[];
    for(var i in data){
      ary.push(i+'='+data[i]);
    }
    str = ary.join('&');
    return window.open(Excel_PATH+"downloadAbsence?"+str,'_blank');
  }
  
  
  self.addComment = function(data){
    //新增評論
    //必須 staff_id, year ,month ,content  |or|  report_id, report_type ,content 
    return $.post(Data_PATH+"addComment", data);
  }
  self.getComment = function(data){
    //取得評論
    //必須 staff_id, year ,month  |or|  report_id, report_type  
    return $.post(Data_PATH+"getComment", data);
  }
  self.updateComment = function(data){
    //更新評論
    //必須 comment_id, do=>del  |or|   comment_id, do=>upd, content
    return $.post(Data_PATH+"updateComment", data);
  }
  
  
  self.getUnderStaff = function(){
    //取得自己底下的員工 ( 包含自己 )
    return $.get(Data_PATH+"getUnderStaff");
  }
  
  
  self.getAllDepartment = function(){
    //取得所有單位
    return $.get(Data_PATH+"getAllDepartment");
  }
  self.getAllStaff = function(){
    //取得所有員工基礎資料
    //必須 是 super user
    return $.get(Data_PATH+"getAllStaff");
  }
  self.getAllStaffPost = function(){
    //所有職稱
    //必須 是 super user
    return $.get(Data_PATH+"getAllStaffPost");
  }
  self.getAllStaffTitleLv = function(){
    //所有職務
    //必須 是 super user
    return $.get(Data_PATH+"getAllStaffTitleLv");
  }
  self.getAllStaffStatus = function(){
    //所有在職狀態
    //必須 是 super user
    return $.get(Data_PATH+"getAllStaffStatus");
  }
  
  
  self.getCycleConfig = function(data){
    //取得區間設定
    //必須 year, month
    return $.post(Setting_PATH+"getCycleConfig", data);
  }
  
  self.updateCycleConfig = function(data){
    //更新區間設定
    //必須 year, month, day_start, day_end, day_cut_addition
    return $.post(Setting_PATH+"updateCycleConfig", data);
  }
  
  self.addDepartment = function(data){
    //新增單位
    //必須 lv,unit_id, name, upper_id
    return $.post(Setting_PATH+"addDepartment",data);
  }
  self.updateDepartment = function(data){
    //更新單位
    //必須 id
    //可選 upper_id, unit_id, name, manager_staff_id, enable, duty_shift
    return $.post(Setting_PATH+"updateDepartment",data);
  }
  
  self.addStaff = function(data){
    //新增員工
    //必須 department_id, staff_no, account, passwd, name, name_en, email, status_id, title_id, post_id
    //可選 first_day, last_day, update_date, rank
    return $.post(Setting_PATH+"addStaff",data);
  }
  self.updateStaff = function(data){
    //更新員工
    //必須 id, department_id
    //可選 passwd, name, name_en, email, first_day, last_day, update_date, status_id, title_id, post_id, is_admin, rank
    return $.post(Setting_PATH+"updateStaff",data);
  }
  
  self.downloadStaffExcel = function(data){
    //下載員工 excel 表
    //必須 admin
    // return $.post(Setting_PATH+"downloadStaffExcel",data);
    return window.open(Excel_PATH+"downloadStaffExcel",'_blank');
  }
  
  self.batchStaffDataWithExcel = function(file){
    //上傳員工資料
    //必須 file  xls, xlsx
    return $.ajax({
      url : Setting_PATH+"batchStaffDataWithExcel",
      type : "POST",
      data : file,
      dataType : "JSON",
      cache : false,
      contentType : false,
      processData: false
    });
  }

  
  
  
  //混淆 壓縮
  var code_array = '0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ/_-abcdefghijklmnopqrstuvwxyz'.split('');
  var code_array_2 = 'D7jklYmIqJ034M/8Ncde_Wfg1GzHhiOUno-abPprQRSTs5KL6tXuAvwVxEy9@BFZC2'.split('');
  var code_map={},code_map_2={};
  var TemplateKey = self.ROOT+'/Template/';
  var temp = localStorage.getItem('rv-template');
  temp = temp? JSON.parse(temp) : {};
  for(var i in code_array){
    var loc = code_array[i];
    var loc_2 = code_array_2[i];
    code_map[loc] = loc_2;
    code_map_2[loc_2] = loc;
  }
  self.encode = function(code){
    var ary = code.replace(TemplateKey,'').split('');
    for(var i in ary){
      var loc = ary[i];
      ary[i] = code_map[loc];
    }
    var result = ary.join('');
    temp[result]=1;
    localStorage.setItem('rv-template',JSON.stringify(temp));
    return result;
  }
  
  self.decode = function(code){
    if(!temp[code]){return false;}
    var ary = code.replace(TemplateKey,'').split('');
    for(var i in ary){
      var loc = ary[i];
      ary[i] = code_map_2[loc];
    }
    return (ary.length>0) ? TemplateKey+ary.join('') : false;
  }
  self.getCode = function(){
    var hash = location.hash.replace('#','');
    var code = self.decode(hash);
    return code;
  }
  
  //URL移動
  self.go = function(position){
    location.href = self.ROOT+''+position;
  }
  
   // ajax setting
  self.ajaxPassenger = [];
  
  self.clearPassenger = function(){
    var ab = API.ajaxPassenger.length-1;
    while(ab>=0){
      var ajax = API.ajaxPassenger.splice(ab,1)[0];
      // ajax.then(function(){});
      ajax.abort() && ajax==null;
      ab--;
    }
    return self;
  }
  
  $.ajaxSetup({
    beforeSend:function(a){
      // console.log(this);
      // console.log(a);
      self.ajaxPassenger.push(a);
    },
    complete:function(a){
      // console.log(s.ajaxPassenger);
      for(var i in self.ajaxPassenger){
        var loc = self.ajaxPassenger[i];
        if(loc==a){
          // console.log(a);
          self.ajaxPassenger.splice(i,1);break;
        }
      }
    }
  });
  
  isRefresh(self);
  setting(self);
  
  function isRefresh(a){
    var code = a.getCode();
    if(code){
      var cc = code.replace(TemplateKey,'');
      location.href= self.ROOT+"/"+cc;
    }
  }
  
})(jQuery);

//初始設定
function setting(s){
  
  
  var dm = s.developMode = <?php echo (IS_DEBUG_MODE)?'true':'false';?>;
  
  s.when = $.when.all = function(deferreds){
    var deferred = new $.Deferred();
    $.when.apply($, deferreds).then(
      function(){
        deferred.resolve(Array.prototype.slice.call(arguments));
      },
      function(){
        deferred.fail(Array.prototype.slice.call(arguments));
      });	
    return deferred;
  }
  
  //每天檢查一次
  if(s.member.is_admin==1 && !document.cookie.match(/checkDepartment\=1/i)){
    var date = new Date();
    var time = date.getTime();
    date.setTime(time+(12*60*60*1000));
    document.cookie="checkDepartment=1;expires="+(date.toGMTString())+"";
    s.checkDepartment();
  }
  
  s.create={
    year : 2016,
    month : 4,
    day : 20
  }
    
}

//針對後端的API資料 解析統一格式
function grenalJSONFormat(json){
  switch(typeof json){
    case "undefined": json={}; break;
    case "string": 
      try{
        json = JSON.parse(json);
      }catch(e){
        json = {};
      }
    break;
    case "function":
      try{
        json = json.apply(this);
      }catch(e){
        json = {};
      }
    break;
    case "default":
  }
  var contain = {
    result : json.result,
    status : json.status,
    msg : json.msg
  }
  var successCode = 200;
  if(typeof contain.status==="undefined"){
    contain.status = 0;
    contain.msg = "Error, Format Not Match.";
    contain.result = null;
  }
  
  if(Object.defineProperty){
    Object.defineProperty(this,"is",{
      value:contain.status==successCode,
      writable:false,
      configurable:false
    });
  }else{
    this.is = contain.status==successCode;
  }
  
  this.get = function(param){
    var res;
    switch(param){
      case "msg": res=contain.msg; break;
      case "status": res=contain.status; break;
      default:
      if(contain.status!=successCode){
        res=contain.msg;
      }else if(contain.result){
        if(contain.result.length == 1 && contain.result[0]){
          res=contain.result[0];
        }else{
          res=contain.result;
        }
      }
    }
    return res;
  }
  this.res = function(){
    return contain.result;
  }
  this.set = function(data){
    contain.result = data;
  }
  var maps;
  this.map = function(){
    if(!maps){
      maps={};
      for(var i in contain.result){
        var loc = contain.result[i];
        if(loc.id){maps[loc.id]=loc;}else{break;}
      }
    }
    return maps;
  }
}




function test100(cbFn){
  var time1 = new Date();
  for(var i = 0; i <= 10000;i++){
    cbFn();
  }
  var time2 = new Date();
  return time2 - time1;
}

function clone(inn){
  var newi;
  if(Array.isArray(inn)){
    newi = inn.slice();
  }else{
    newi = JSON.parse( JSON.stringify(inn));
  }
  return newi;
}

//cookie
(function (factory) {
  factory(jQuery);
}(function ($) {
	var pluses = /\+/g;

	function encode(s) {
		return config.raw ? s : encodeURIComponent(s);
	}
	function decode(s) {
		return config.raw ? s : decodeURIComponent(s);
	}
	function stringifyCookieValue(value) {
		return encode(config.json ? JSON.stringify(value) : String(value));
	}
	function parseCookieValue(s) {
		if (s.indexOf('"') === 0) {
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}
		try {
			s = decodeURIComponent(s.replace(pluses, ' '));
			return config.json ? JSON.parse(s) : s;
		} catch(e) {}
	}

	function read(s, converter) {
		var value = config.raw ? s : parseCookieValue(s);
		return $.isFunction(converter) ? converter(value) : value;
	}

	var config = $.cookie = function (key, value, options) {

		if (value !== undefined && !$.isFunction(value)) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setTime(+t + days * 864e+5);
			}

			return (document.cookie = [
				encode(key), '=', stringifyCookieValue(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		var result = key ? undefined : {};
    
		var cookies = document.cookie ? document.cookie.split('; ') : [];

		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = parts.join('=');

			if (key && key === name) {
				result = read(cookie, value);
				break;
			}

			if (!key && (cookie = read(cookie)) !== undefined) {
				result[name] = cookie;
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) === undefined) {
			return false;
		}
		$.cookie(key, '', $.extend({}, options, { expires: -1 }));
		return !$.cookie(key);
	};
  
  var current = {
    year : $.cookie('year') || new Date().getFullYear(),
    month : $.cookie('month') || new Date().getMonth() + 1
  }
  
  
  $.ym = {
    get : function(){
      return current;
    },
    save : function(ym){
      if(ym){
        current.year=ym.year;
        current.month=ym.month;
      }
      $.cookie('year',current.year);
      $.cookie('month',current.month);
    }
  }

}));