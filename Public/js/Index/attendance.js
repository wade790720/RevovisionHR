Dropzone.autoDiscover = false;
var $Attendance = $('#Attendance').generalController(function() {
    var ts = this;
    var form = ts.q('form');
    var current = $.ym.get();
    var SelectYM = ts.q('#SelectYM');
    var YearSelect = ts.q('#getYear');
    var MonthSelect = ts.q('#getMonth');

    function initYM() {
        var thisyear = new Date().getFullYear();
        for (i = thisyear; i >= API.create.year ; i--) {
            YearSelect.append('<option value="' + i + '">' + i + '年</option>');
        }
        for (i = 1; i <= 12; i++) {
            MonthSelect.append('<option value="' + i + '">' + i + '月</option>');
        }
        //
        YearSelect.val(current.year).attr('selected');
        YearSelect.change(function(){ current.year = this.value;$.ym.save(); });
        MonthSelect.val(current.month).attr('selected');
        MonthSelect.change(function(){ current.month = this.value;$.ym.save(); });
    }
    initYM();

    // Prevent Dropzone from auto discovering this element:

    var myDropzone = new Dropzone(ts.q("#myDropzone")[0]);
    myDropzone.options.uploadMultiple = true;
    //myDropzone.options.addRemoveLinks = true;
    myDropzone.options.createImageThumbnails = false;
    myDropzone.options.autoProcessQueue = false;
    myDropzone.options.acceptedFiles = ".xls,.xlsx";


    myDropzone.on('addedfile', function(f) {
        console.log(f);
        if (!f.name.match(/\.xlsx?/i)) {
            this.removeFile(f);
            //return alert('不接受的檔案格式');
            return swal("Fail","不接受的檔案格式");
        }
        var data = new FormData();
        data.append('file', f);
        API.addAbsence(data).then(function(e) {
          var cnt = API.format(e);
            if (cnt.is) {
                //alert('上傳成功 : ' + f.name);
                swal('Success','上傳成功 : ' + f.name);
            } else {
                //alert('上傳失敗 : ' + f.name + '\r\n' + cnt.get());
                swal('Fail','上傳失敗 : ' + f.name + '\r\n' + cnt.get() );
            }
            clearInterval(f.setInterval);
            myDropzone.removeFile(f);
        });
        // 上傳文件後，點選文件刪除click and remove file
        //f.previewElement.onclick = function() {
        //  myDropzone.removeFile(f);
        //};

        //假進度條  直接抓 children position 如果套件修改HTML結構會報錯
        f.fakeProcess = 0;
        f.base = 512000 / f.upload.total;
        f.setInterval = setInterval(function() {
            f.fakeProcess += (f.fakeProcess >= 100) ? 0 : Math.random() * f.base + f.base;
            f.previewElement.children[2].firstChild.style.width = f.fakeProcess + "%";
        }, 100);

    });

});
