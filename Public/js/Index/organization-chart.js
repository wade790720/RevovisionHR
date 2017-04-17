var $Organization = $('#Organization').generalController(function() {

    function init() {
        var thisyear = new Date().getFullYear();
        for (i = thisyear; i > thisyear - 2; i--) {
            year.append('<option value="' + i + '">' + i + '年</option>');
        }
        //month
        var thismonth = new Date().getMonth() + 1;
        for (i = 1; i <= 12; i++) {
            month.append('<option value="' + i + '">' + i + '月</option>');
        }
        //default current month and year
        year.val(thisyear).attr('selected');
        // month.val(thismonth).attr('selected');
    }

    // 組織圖樣版

    var cell = this.q('#cell-1').html();
    var ts = this;
    var month = ts.q('#ChartMonth');
    var year = ts.q('#ChartYear');
    var selectMonth = ts.q('#SelectMonth').find('select');
    var historyModal = ts.q('#HistoryModal');
    var cell2 = ts.q('#cell-2').html();


    // member 的資料
    ts.onLogin(function(member) {
      console.log(member);
        tyear = new Date().getFullYear();
        tmonth = new Date().getMonth() + 1;
        var now = {
            year: tyear,
            month: tmonth
        }

        setTimeout(function() {
           month.val(tmonth).trigger('change');
        }, 50);

        ts.on('click', '*[data-process-id]', function() {
            // get btn process id number
            var $this_btn = ts.q(this);
            var pid = $this_btn.data('process-id');
            //console.log(pid);
            var record = $this_btn.data('record');
            //console.log(record);
            var unitname = record.name; // 組織名稱
            var man_name = record.manager_name;
            var man_name_en = record.manager_name_en;


            var createblock = ts.q('[data-process=create_date]');
            var unitblock = ts.q('[data-process=unit_name]');
            var timeinfo = ts.q('[data-process=time_info]');
            var process_info = ts.q('[data-process=info]');



            API.getMonthlyProcessHistory({processing_id: pid}).then(function(json) {
                //loading
                var rec = API.format(json);

                if (rec.is) {
                    var recordArray = rec.res();
                    // .res() 資料限制是array；.get() 資料為原始資料
                    createblock.empty();
                    unitblock.empty();
                    timeinfo.empty();
                    unitblock.append('主管:' + man_name_en + man_name + ' : 【' + unitname + ' 】考評單');

                    for (var a in recordArray) {
                        var list = recordArray[a];
                        var timecell = cell2.replace(/\{([\w\d]+?)\}/ig, function(match, param2) {
                            var k = param2;
                            return list[k];
                        });
                        var p_cell = $(timecell);
                        var process_info = p_cell.q('[data-process=info]');
                        var defalt_info = '考評單尚未產生';
                        var create_info = '考評單產生日期 : ' + list.update_date;
                        var commit_info ='【' + list._operating_name_en + list._operating_name + '】 已<b>送審</b>考評單至 【' + list._target_name_en + list._target_name +'】';
                        var return_info ='【' +  list._operating_name_en + list._operating_name + '】 已<i>退回</i>此考評單給【' + list._target_name_en + list._target_name + '】<br> <i>退回原因：</i>' + list.reason;
                        var done_info = '此考評單已<em>考評完成</em>';

                        if (list.action == 'create') {
                            process_info.html(create_info);
                        }

                        if (list.action == 'commit') {
                            process_info.html(commit_info);
                        }

                        if (list.action == 'return') {
                            process_info.html(return_info);
                        }

                        if (list.action == 'done') {
                            process_info.html(done_info);
                        }
                        timeinfo.append(p_cell);
                    }
                } else {
                    //console.log(rec.get());
                }
            });
        });

        // 當使用者選擇月份
        selectMonth.on('change', loadMap);

        function loadMap(param) {
            var mon = month.val();
            var yn = year.val();
            var param = "year=" + yn + "&month=" + mon;

            var rvChart = ts.q('#RvChart');
            // 清空組織圖資料
            // add a loading
            rvChart.empty();

                ts.q('.rv-chart-map').addClass('rv-loading');


            // 資料庫只有4月可以看
            //var data = 'year=2017&month=4';
            //var data = param;
            API.getDepartmentList(param).then(function(json) {
                // 解析資料
                var collect = API.format(json);

                // 分析資料
                if (collect.is) {
                    var unitArray = collect.get();
                    var lvArray = {};

                    for (var i in unitArray) {
                        var loc = unitArray[i];
                        //console.log(loc);
                        loc.manager_name = (loc._manager) ? loc._manager.name : '暫缺';
                        loc.manager_name_en = (loc._manager) ? loc._manager.name_en : '';

                        //判斷是否包含在path id
                        loc.record_if = $.inArray(member.department_id, loc.id_path) >= 0;

                        var la = lvArray[loc['lv']];
                        if (la) {
                            la.push(loc);
                        } else {
                            lvArray[loc['lv']] = [loc];
                        }

                    }

                    for (var a in lvArray) {
                        var loc = lvArray[a];
                        for (var b in loc) {
                            var loc2 = loc[b];
                            //console.log(loc2);
                            var $root = ts.q('[data-department-id=' + loc2['upper_id'] + ']');
                            var newCell = cell.replace(/\{([\w\d]+?)\}/ig, function(match, param1) {
                                var key = param1;
                                return loc2[key];
                                console.log(loc2[key]);
                            });

                            // 這太浪費 by snow
                            var j_cell = $(newCell);
                            var key = j_cell.q('[rv-if]').attr('rv-if');
                            var ablock = j_cell.q('[data-unitid]');
                            var pidBtn = j_cell.q('[data-created-id]');
                            // 如果不是user的下屬則不出現紀錄欄位
                            if (!loc2[key]) {
                                j_cell.q('[rv-if=' + key + ']').remove();
                            }
                            //如果沒主管不出現紀錄欄位？
                            // if (!loc2['_manager']) {
                            //     j_cell.q('[rv-if=' + key + ']').remove();
                            // }


                            if (!loc2['_manager']) {
                                ablock.addClass('noleader')
                            }

                            if (loc2[key]) {
                                //依考評單狀態顯示顏色
                                for (var id in loc2._processing) {
                                    //console.log(loc2._processing[id].status_code);
                                    //console.log(loc2._processing[id].length);
                                    //console.log(loc2._processing[id].created_department_id);
                                    switch (loc2._processing[id].status_code) {
                                        // 草稿
                                        case 1:
                                            break;
                                            //初評
                                        case 2:
                                            ablock.addClass('non-score');
                                            break;
                                            // 審核
                                        case 3:
                                            ablock.addClass('reviewing');
                                            break;
                                            // 核準
                                        case 4:
                                            ablock.addClass('return');
                                            break;
                                            // 退回
                                        case 5:

                                            ablock.addClass('rated');
                                            break;
                                            defalt:
                                                break;
                                    }

                                    // 顯示區塊中有的考評單按鈕 ，部份主管有二個單，則一個顯示主管紀錄，一個顯示紀錄
                                    if (loc2._processing[id] && loc2._processing[id].type == 1) {
                                        var btnHistory1 = '<button data-target="HistoryModal" class="score-history waves-effect waves-light btn" data-process-id=' +
                                            id + '>主管紀錄</button>';
                                        var btn_1 = j_cell.q('[rv-if]').append(btnHistory1);
                                        btn_1.q('[data-process-id]').data('record', loc2);
                                    }

                                    if (loc2._processing[id] && loc2._processing[id].type == 2) {
                                        var btnHistory2 = '<button data-target="HistoryModal" class="score-history waves-effect waves-light btn" data-process-id=' +
                                            id + '>紀錄</button>';
                                        var btn_2 = j_cell.q('[rv-if]').append(btnHistory2);
                                        btn_2.q('[data-process-id]').data('record', loc2);
                                    }

                                }
                            }
                            if (loc2['manager_staff_id'] == member.id) {
                                j_cell.addClass('active');
                            }
                            // 系統管理員可以看到全部
                            if(member.is_admin == 1) {
                              loc2.id = 1;
                              ts.q('#RvChart').children('li').addClass('active');
                            }

                            // 最下層 lv4 加上直行顯示的css
                            if (loc2['lv'] == 3) {
                                j_cell.addClass('nav-row');
                            }
                             $root.append(j_cell);
                             ts.q('.no-data').hide();
                             ts.q('.rv-chart-info').fadeIn('slow');

                            ts.q('.rv-chart-map').removeClass('rv-loading');
                        }
                    }
                } else {

                    ts.q('.no-data').show();
                     ts.q('.rv-chart-info').hide();
                    ts.q('.rv-chart-map').removeClass('rv-loading');
                    // add a fail run down

                }


            });

        }
    });



    init();

    // use materializecss modal
    ts.q('.rv-chart-map').find('.modal').modal({
        inDuration: 100,
        outDuration: 100,
    });
})