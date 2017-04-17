var $SettingImport = $('#SettingImport').generalController(function() {
    var ts = this;
    Dropzone.autoDiscover = false;
    ts.onLogin(function() {
    
      var dz = new Dropzone(ts.q(".dropzone")[0]);
      dz.options.uploadMultiple = true;
      dz.options.createImageThumbnails = false;
      dz.options.autoProcessQueue = false;
      dz.options.acceptedFiles = ".xls,.xlsx";
      

      dz.on('addedfile', function(f) {
          if(!f.name.match(/\.xlsx?/i)){
            this.removeFile(f);
            return alert('不接受的檔案格式');
          }
          var data = new FormData();
          data.append('file', f);
          API.batchStaffDataWithExcel(data).then(function(e) {
            var cnt = API.format(e);
              if(cnt.is){
                alert('上傳成功 : ' + f.name);
              }else{
                alert('上傳失敗 : ' + f.name + '\r\n' + cnt.get() );
              }
              clearInterval(f.setInterval);
              dz.removeFile(f);
          });
          
          //假進度條  直接抓 children position 如果套件修改HTML結構會報錯
          f.fakeProcess = 0;
          f.base = 512000 / f.upload.total;
          f.setInterval = setInterval(function(){
            f.fakeProcess += (f.fakeProcess>=100)?0: Math.random()*f.base+f.base;
            f.previewElement.children[2].firstChild.style.width = f.fakeProcess+"%";
          },100);
          
      });
      
      
      ts.q('[data-toggle="downlaod-staff"]').off('click').on('click',API.downloadStaffExcel);
      
      ts.q('[data-toggle="refresh-monthly"]').off('click').on('click',function(){
        API.checkDepartment().then(function(e){
          if(API.format(e).is){
            alert('更新成功');
          }else{
            alert('更新失敗');
          }
        });
      });

    });
    
    
    

    // ts.onShown(function() {
        // console.log(this);
    // });

    // ts.onHidden(function() {
        // console.log(this);
    // });
});