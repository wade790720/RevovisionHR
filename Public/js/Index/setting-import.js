Dropzone.autoDiscover = false;
var $SettingImport = $('#SettingImport').generalController(function() {
    var ts = this;

    ts.onLogin(function() {

        var current = new Date();
        API.getCycleConfig({ year: current.getFullYear(), month: current.getMonth() + 1 }).then(function(e) {
            var cnt = API.format(e);
            if (cnt.is) {
                var data = cnt.get();
                // var endingDate = new Date(data.cut_off_date);
                // endingDate.setDate( endingDate.getDate() + 1);
                if (data.settingAllow!=true) {
                    ts.$.hide();
                    //alert('月考評啟動期間，不能修改組織.');
                    swal({
                        title: "注意！",
                        text: "月考評啟動期間，不能修改組織!",
                        timer: 2500,
                        showConfirmButton: true
                    });
                    // window.history.back();
                    setTimeout(function(){ API.go('/setting-evaluation');}, 2500);

                } else {
                    startInit();
                    ts.$.show();
                }
            }

        });

    });

    function startInit() {

        ts.$.off('click').on('click', '[data-toggle="downlaod-staff"]', API.downloadStaffExcel);

        ts.$.on('click', '[data-toggle="refresh-monthly"]', function() {
            var doo = window.confirm('執行更新將會重新整理所有月考評表，本月份的先前記錄將消失，是否繼續？');
            if (!doo) {
                return false; }
            API.checkDepartment({ del: true }).then(function(e) {
                if (API.format(e).is) {
                    //alert('更新成功');
                    swal("Success", "更新成功!");
                } else {
                    //alert('更新失敗');
                     swal("Fail", "更新失敗!");
                }
            });
        });

        dzoon();

    }

    function dzoon() {
        var dz = ts.dz = new Dropzone(ts.q(".dropzone")[0]);
        dz.options.uploadMultiple = true;
        dz.options.createImageThumbnails = false;
        dz.options.autoProcessQueue = false;
        dz.options.acceptedFiles = ".xls,.xlsx";
        dz.on('addedfile', function(f) {
            if (!f.name.match(/\.xlsx?/i)) {
                this.removeFile(f);
                //return alert('不接受的檔案格式');
                return swal("Error", "不接受的檔案格式!");
            }
            var data = new FormData();
            data.append('file', f);
            API.batchStaffDataWithExcel(data).then(function(e) {
                var cnt = API.format(e);
                if (cnt.is) {
                    //alert('上傳成功 : ' + f.name);
                    swal("上傳成功", f.name);
                } else {
                    //alert('上傳失敗 : ' + f.name + '\r\n' + cnt.get());
                    swal("上傳失敗", f.name + '\r\n' + cnt.get());
                }
                clearInterval(f.setInterval);
                dz.removeFile(f);
            });

            //假進度條  直接抓 children position 如果套件修改HTML結構會報錯
            f.fakeProcess = 0;
            f.base = 512000 / f.upload.total;
            f.setInterval = setInterval(function() {
                f.fakeProcess += (f.fakeProcess >= 100) ? 0 : Math.random() * f.base + f.base;
                f.previewElement.children[2].firstChild.style.width = f.fakeProcess + "%";
            }, 100);

        });
    }


    // ts.onShown(function() {
    // console.log(this);
    // });

    // ts.onHidden(function() {
    // console.log(this);
    // });
});
