var $SettingOrganization = $('#SettingOrganization').generalController(function() {

    var ts = this;
    ts.vues = {};

    //開發時打開點比較好看
    function devv() {
        ts.q('.container').width('90%');
    };
    devv();


    //開始啟動
    function goLauncher() {
        // console.log(ts);

        prepareData();

        buildDepartmentVue();

        buildDepartmentDetailVue();

        buildStaffList();

        buildStaffDetail();

        //套件
        ts.q('.tabs').tabs();
        ts.q('.dropdown-button').dropdown();
        ts.q('.modal').modal();

        // 日期套件 https://github.com/ripjar/material-datetime-picker
        // ts.dataPicker = new MaterialDatePicker().on('submit', function(val) {
            // var time = val.format("YYYY-MM-DD");
            // console.log(time);
            // ts.q(".date-picker").val(time)

        // });
        // ts.q('.date-picker').on('click', function() {
            // return ts.dataPicker.open() || ts.dataPicker.set( function(){} );
        // });
        
    }

    //單位menu的vue
    function buildDepartmentVue() {
        var lvs = {};
        for (var i in ts.department) {
            var loc = ts.department[i];
            var key = (loc.lv == 1) ? 2 : loc.lv;
            if (!lvs[key]) { lvs[key] = []; }
            lvs[key].push(loc);
        }
        ts.vues.Department = new Vue({
            el: '#DepartmentMenu',
            data: {
                department: ts.department_map,
                devLv: lvs,
                upper_array: [],
                createUnitData: { unit_id: '', name: '', lv: 0, upper: {} }
            },
            methods: {
                openDepartment: function(loc) {
                    console.log(loc);
                    // console.log(ts.vues.DepartmentDetail);
                    ts.vues.DepartmentDetail.show(loc.id);
                    ts.vues.StaffList.show(loc.id);
                    ts.vues.StaffDetail.hide();

                    ts.q('.rv-setting').addClass('zoom');
                    var area = ts.q(this.$el).q('.content-area a');
                    area.removeClass('active').filter('.department_id_' + loc.id).addClass('active');;
                },
                changOfUnitData: function(upper) {
                    this.createUnitData.upper = upper;
                },
                addDepartmentButton: function(lv) {
                    // console.log(lv);
                    this.upper_array = [];
                    for (var i in this.department) {
                        var loc = this.department[i];
                        if (loc.lv == lv - 1) { this.upper_array.push(loc); }
                    }
                    this.createUnitData.upper = this.upper_array[0];
                    this.createUnitData.unit_id = '';
                    this.createUnitData.name = '';
                    this.createUnitData.lv = parseInt(lv);
                },
                addDepartment: function() {
                    // console.log(this.createUnitData);
                    var submitData = {
                        unit_id: this.createUnitData.unit_id,
                        name: this.createUnitData.name,
                        lv: this.createUnitData.lv,
                        upper_id: this.createUnitData.upper.id
                    };
                    var errorMsg = '';
                    if (!submitData.unit_id.match(/^[a-zA-Z]{1}[\d]{2}$/i)) { errorMsg += '單位代號格式錯誤.\n\r'; }
                    if (submitData.name.length == 0) { errorMsg += '部門名稱不能無.\n\r'; }
                    if (errorMsg.length > 0) {
                        alert(errorMsg);
                    } else {
                        // console.log(submitData);
                        var needleDev = this.devLv;
                        API.addDepartment(submitData).then(function(e) {
                            var cot = API.format(e);
                            if (cot.is) {
                                ts.q('#CreateUnit').modal('close');
                                var newOne = cot.get();
                                addClientDepartment(newOne);
                                needleDev[newOne.lv].push(newOne);
                            } else {
                                generalFail(cot.get());
                            }
                        }).fail(generalFail);
                    }
                }
            }
        });
    }

    //單位詳情
    function buildDepartmentDetailVue() {
        var now = clone(ts.department[0]);
        var upper = ts.department_map[now.id];

        ts.vues.DepartmentDetail = new Vue({
            el: '#DepartmentDetail',
            data: {
                department: ts.department_map,
                staff: ts.staff_map,
                managers_array: [],
                manager: {},
                supervisor_name: '',
                upper: upper,
                upper_array: [],
                now: now
            },
            methods: {
                show: function(id) {
                    var team = this.now = clone(this.department[id]);
                    // var team = this.now = this.department[id];
                    if (team) {

                        this.manager = (team.manager_staff_id) ? this.staff[team.manager_staff_id] : { id: 0, name: '無' };
                        this.supervisor_name = this.staff[team.supervisor_staff_id].name;
                        this.upper = this.department[team.upper_id];

                        this.refreshStaffArray(team.lv);
                        this.refreshUpperArray(team.lv);

                        this.submitData = { id: id };

                    } else {
                        console.log('error department id:' + id);
                    }
                },
                refreshStaffArray: function(lv) {
                    this.managers_array = [];
                    for (var i in this.staff) {
                        var loc = this.staff[i];
                        //不能是別組的主管  又必須
                        if (loc.lv <= lv && loc.department_id == this.now.id) {
                            this.managers_array.push(loc);
                        }
                    }
                },
                updateDepartment: function() {

                    var sd = this.submitData;
                    var now = this.now;
                    var manager = this.manager;
                    var managers = this.managers;
                    // console.log(sd);
                    API.updateDepartment(sd).then(function(data) {
                        var cot = API.format(data);
                        if (!cot.is) {
                            return generalFail(cot.get());
                        }

                        alert('更新成功');

                        var dmap = ts.department_map[now.id];
                        for (var i in sd) {
                            var loc = sd[i];
                            dmap[i] = loc;
                        }
						
						API.checkDepartment();

                        // this.refreshStaffArray(val.lv);

                    }).fail(generalFail);

                },
                changeManager: function(val) {
                    this.manager = val || { id: 0, name: '無' };
                    this.submitData['manager_staff_id'] = val.id || 0;
                },
                changeUpper: function(val) {
                    this.upper = val;
                    this.submitData['upper_id'] = val.id;
                },
                refreshUpperArray: function(lv) {
                    this.upper_array = [];
                    lv--;
                    for (var i in this.department) {
                        var loc = this.department[i];
                        if (loc.lv == lv) { this.upper_array.push(loc); }
                    }
                }
            }
        });
    }

    //人員清單
    function buildStaffList() {

        var innerStaffData = function() {
            var no = 'R000';
            for (var i in ts.staff) {
                var loc = ts.staff[i];
                if (no < loc.staff_no) { no = loc.staff_no; }
            }
            return {
                staff_no: no.replace(/[\d]+$/i, function($m) {
                    return Number($m) + 1;
                }),
                account: '',
                passwd: '',
                name: '',
                name_en: '',
                email: '',
                first_day: '',
                last_day: '',
                update_date: '',
                status: ts.staff_status[0],
                title: ts.staff_title[0],
                post: ts.staff_post[0]
            };
        }

        ts.vues.StaffList = new Vue({
            el: '#StaffList',
            data: {
                staff: ts.staff_map,
                department: ts.department_map,
                staff_list: [],
                department_list: [],
                staff_post: ts.staff_post,
                staff_title: ts.staff_title,
                staff_status: ts.staff_status,
                onDuty: false,
                team: {},
                newStaffData: innerStaffData()
            },
            mounted:function(){
              var vuethis = this;
              this.dataPicker = new MaterialDatePicker().on('submit', function(val) {
                  var time = val.format("YYYY-MM-DD");
                  vuethis.newStaffData[ vuethis.choiceDate ] = time;
              });
            },
            methods: {
                show: function(team_id) {
                    this.team = this.department[team_id];
                    this.refresh();
                },
                refresh: function() {
                    this.staff_list = [];
                    for (var i in this.staff) {
                        var loc = this.staff[i];
                        if (!this.onDuty && loc.status_id == 4) {
                            continue;
                        }
                        if (loc.department_id == this.team.id) { this.staff_list.push(loc); }
                    }
                    this.department_list = [];
                    for (var i in this.department) {
                        var loc = this.department[i];
                        if (loc.lv == this.team.lv) { this.department_list.push(loc); }
                    }
                },
                refreshStaffData: innerStaffData,
                openCreate: function() {
                    // this.newStaffData = this.refreshStaffData();
                    // console.log(this.newStaffData);
                },
                onDateChoose : function(e){
                  this.choiceDate = e;
                  this.dataPicker.open();
                },
                showDetail: function(staff) {
                    // console.log(staff);
                    var items = ts.q(this.$el).q('.wrapper .rv-item');
                    items.removeClass('active').filter('.staff_id_' + staff.id).addClass('active');
                    ts.vues.StaffDetail.show(staff.id);
                },
                addStaff: function() {
                    var sdate = clone(this.newStaffData);
                    delete sdate.post && delete sdate.status && delete sdate.title;
                    sdate['post_id'] = this.newStaffData.post.id;
                    sdate['status_id'] = this.newStaffData.status.id;
                    sdate['title_id'] = this.newStaffData.title.id;
                    sdate['department_id'] = this.team.id;

                    var errorMsg = '';
                    if (!sdate['staff_no'].match(/^[A-Z]{1}[\d]{2,4}$/i)) { errorMsg += '人員編號格式錯誤 \n\r'; }
                    if (sdate['account'].length == 0) { errorMsg += '帳號不能為空 \n\r'; }
                    if (sdate['passwd'].length == 0) { errorMsg += '密碼不能為空 \n\r'; }
                    if (sdate['name'].length == 0) { errorMsg += '名子不能為空 \n\r'; }
                    if (sdate['name_en'].length == 0) { errorMsg += '英文名子不能為空 \n\r'; }
                    if (!sdate['email'].match(/^[\w\d\.\_\-]+\@.+$/i)) { errorMsg += 'Email格式錯誤 \n\r'; }
                    if (errorMsg.length > 0) {
                        return alert(errorMsg);
                    }

                    var vts = this;
                    API.addStaff(sdate).then(function(e) {
                        var collect = API.format(e);
                        if (collect.is) {
                            var newclassmate = collect.get();
                            newclassmate.head = newclassmate.name_en.charAt(0);

                            addClientStaff(newclassmate);
                            vts.refresh();
                            ts.q('#AddStaff').modal('close');

                            //等待vue做好內容 在 callback 新的 staff
                            ts.q(vts.$el).animate({ scrollTop: vts.$el.scrollHeight + 50 }, 50, function() { vts.showDetail(newclassmate); });
                            //更新主管選單
                            ts.vues.DepartmentDetail.refreshStaffArray(vts.team.lv);
                            //
                            vts.newStaffData = vts.refreshStaffData();

                        } else {
                            generalFail(collect.get());
                        }
                    }).fail(generalFail);

                }
            }
        });

    }

    //人員詳細
    function buildStaffDetail() {

        var now = clone(ts.staff[0]);
        var nowDepartment = clone(ts.department[0]);

        ts.vues.StaffDetail = new Vue({
            el: '#StaffDetail',
            data: {
                staff: ts.staff_map,
                department: ts.department_map,
                staff_post: ts.staff_post,
                staff_title: ts.staff_title,
                staff_title_map: ts.staff_title_map,
                staff_status: ts.staff_status,
                now: now,
                nowDepartment: nowDepartment,
                isAdmin: 1
            },
            mounted:function(){
              var vuethis = this;
              this.dataPicker = new MaterialDatePicker().on('submit', function(val) {
                  var time = val.format("YYYY-MM-DD");
                  // console.log(time);
                  vuethis.now[ vuethis.choiceDate ] = time;
              });
            },
            methods: {
                show: function(staff_id) {
                    var staff = this.staff[staff_id];
                    if (!staff) {
                        return console.log("Error Staff Id.");;
                    }
                    var staff = clone(this.staff[staff_id]);

                    this.now = staff;
                    this.nowDepartment = this.department[staff.department_id];
                    this.isAdmin = staff.is_admin == 1;
                    console.log(this);
                    ts.q(this.$el).css('opacity', 1);
                },
                hide: function() {
                    ts.q(this.$el).css('opacity', 0);
                },
                onDateChoose : function(e){
                  this.choiceDate = e;
                  this.dataPicker.open();
                },
                updateAdmin: function() {
                    this.now.is_admin = this.isAdmin ? 1 : 0;
                },
                updateStaff : function(){
                  var now = this.now;
                  var haveToRefreshList = !(this.nowDepartment.id==now.Department_id) || now.status_id==4;
                  var submitData = {id:now.id, department_id:this.nowDepartment.id};
                  var olderStaff = this.staff[now.id];
                  // console.log(submitData);
                  for(var i in now){
                    if(i=='title'||i=='status'||i=='post'){continue;}
                    var loc = now[i];
                    if(loc!=olderStaff[i]){ submitData[i]=loc; }
                  }
                  
                  if(haveToRefreshList && !submitData['update_date']){
                    var currentDate = new Date();
                    submitData['update_date']=now['update_date']=currentDate.getFullYear()+'-'+(('0'+(currentDate.getMonth()+1)).slice(-2))+'-'+('0'+currentDate.getDate()).slice(-2);
                  }
                  // console.log(now);
                  // console.log(submitData);
                  var vuethis = this;
                  API.updateStaff(submitData).then(function(e){
                    var cnt = API.format(e);
                    if(cnt.is){
                      alert('更新成功');
                      now.department_id = vuethis.nowDepartment.id;
                      var updatedStaff = vuethis.staff[submitData.id];
                      for(var i in now){
                        if(i=='title_id'){
                          var afLv = vuethis.staff_title_map[now[i]].lv
                          now.lv = afLv;
                          updatedStaff['lv'] = afLv;
                        }
                        updatedStaff[i] = now[i];
                      }
                      ts.vues.DepartmentDetail.refreshStaffArray(vuethis.nowDepartment.lv);
                      
                      if(haveToRefreshList){ ts.vues.StaffList.refresh();API.checkDepartment(); }
                      
                    }else{
                      generalFail(cnt.get());
                    }
                  }).fail( generalFail );
                }
            }
        });

    }


    //預處理資料
    function prepareData() {
        for (var i in ts.staff) {
            var loc = ts.staff[i];
            loc.head = loc.name_en.charAt(0);
        }
    }

    function addClientDepartment(newone) {
        ts.department.push(newone);
        ts.department_map[newone.id] = newone;
    }

    function addClientStaff(newone) {
        ts.staff.push(newone);
        ts.staff_map[newone.id] = newone;
    }

    //發生必須重新讀取的錯誤
    function reloadFail() {
        alert("錯誤，請重新嘗試");
        location.reload();
    }

    function generalFail(e) {
        alert('失敗 \r\n'+(e?e:''));
    }

    this.onLogin(function() {

        //加載資料
        var getArray = [API.getAllDepartment(), API.getAllStaff(), API.getAllStaffPost(), API.getAllStaffTitleLv(), API.getAllStaffStatus()];
        $.when.all(getArray).then(function(all) {
            var contain = [];
            for (var i in all) {
                var cot = API.format(all[i][0]);
                if (!cot.is) {
                    return reloadFail();
                }
                contain.push(cot);
            }
            // console.log(contain);
            ts.department = contain[0].get();
            ts.department_map = contain[0].map();
            ts.staff = contain[1].get();
            ts.staff_map = contain[1].map();
            ts.staff_post = contain[2].get();
            ts.staff_title = contain[3].get();
            ts.staff_title_map = contain[3].map();
            ts.staff_status = contain[4].get();

            //開始
            goLauncher();

        }).fail(reloadFail);
    });
});
