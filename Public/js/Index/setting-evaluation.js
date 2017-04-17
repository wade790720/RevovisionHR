var $SettingEvaluation = $('#SettingEvaluation').generalController(function(){
	var ts = this;
	
	ts.onLogin(function(){
    
    var current = new Date();
    var nowYear = current.getFullYear();
    var year = [];
    while(nowYear >= 2017){
      year.push(nowYear--);
    }
    var month = [];
    for(var i =1; i<=12;i++){
      month.push(i);
    }
    
    ts.vue = new Vue({
      el : ts.q('.had-container')[0],
      data:{
        yearArray : year,
        monthArray : month,
        now:{year:current.getFullYear(),month:current.getMonth()+1},
        setting:{day_start:'',day_end:'',day_cut_addition:''},
        isLaunch:false,
        finalDay:0,
        canUpdate:true
      },
      methods:{
        getCycleConfig : function(){
          var vuethis = this;
          API.getCycleConfig(this.now).then(function(e){
            var cnt = API.format(e);
            if(cnt.is){
              var result = cnt.get();
              vuethis.isLaunch = result['monthly_launched']==1;
              vuethis.setting = result;
              vuethis.canUpdate = current.getTime() < new Date( vuethis.now.year+'-'+vuethis.now.month+'-'+(result.day_end+result.day_cut_addition) ).getTime();
              
              ts.q('#eva-month-'+vuethis.now.month).prop('checked',true);
              
              vuethis.launchBotton();
              
            }else{
              generalFail();
            }
          }).fail( generalFail );
        },
        launchBotton : function(){
          this.setting.monthly_launched = this.isLaunch ? 1:0;
          this.finalDay = parseInt(this.setting.day_end) + parseInt(this.setting.day_cut_addition);
          
          if (this.isLaunch) {
            ts.q('.process-date input').prop("disabled", true);
            ts.q('.rate-days input').prop("disabled", true);
            ts.q('.eva-end-date').show();
          }else{
            ts.q('.process-date input').removeAttr("disabled");
            ts.q('.rate-days input').removeAttr("disabled");
            ts.q('.eva-end-date').hide();
          }
          
        },
        submit : function(){
          var submitData = {
            year : this.now.year,
            month : this.now.month,
            day_start : this.setting.day_start,
            day_end : this.setting.day_end,
            day_cut_addition : this.setting.day_cut_addition,
            monthly_launched : this.setting.monthly_launched
          }
          var vuethis = this;
          API.updateCycleConfig(submitData).then(function(e){
            var cnt = API.format(e);
            if(cnt.is){
              var result = cnt.get();
              vuethis.setting = result;
              if(result.hasChanged){
                vuethis.refreshMonthly();
              }else{
                alert('更新成功');
              }
            }else{
              generalFail(cnt.get());
            }
          }).fail( generalFail );
        },
        refreshMonthly : function(){
          
          var deferred = (this.setting.monthly_launched==1) ? API.launchMonthly(this.now) : API.pauseMonthly(this.now);
          deferred.then(function(e){
            var cnt = API.format(e);
            if(cnt.is){
              alert('更新成功');
            }else{
              generalFail(cnt.get());
            }
          }).fail( generalFail );
          
        }
      },
      mounted : function(){
        this.getCycleConfig();
        this.$watch('now', this.getCycleConfig, {deep:true});
      }
      
      
    });
    
	});
  
  function generalFail(e){
    alert('失敗，請重試. \r\n'+(e?e:''));
  }
  
});
