var $SettingEvaluation = $('#SettingEvaluation').generalController(function() {
    var ts = this;

    ts.onLogin(function() {

        var current = $.ym.get();
        var nowYear = new Date().getFullYear();
        var year = [];
        while (nowYear >= API.create.year) {
            year.push(nowYear--);
        }

        var month = [];
        for (var i = 1; i <= 12; i++) {
            if (i < 10) {
                i = '0' + i;
            }
            month.push(i);
        }
        // 小於10的數字前加上 0
        var currentMonth = (current.month)>= 10 ? (current.month) : '0' + (current.month);

        ts.vue = new Vue({
            el: ts.q('.had-container')[0],
            data: {
                yearArray: year,
                monthArray: month,
                now: { year: current.year, month: currentMonth },
                setting: { day_start: '', day_end: '', day_cut_addition: '' },
                isLaunch: false,
                finalDay: 0,
                canUpdate: true
            },
            methods: {
                getCycleConfig: function() {
                    var vuethis = this;
                    API.getCycleConfig(this.now).then(function(e) {
                        var cnt = API.format(e);
                        if (cnt.is) {
                            var result = cnt.get();
                            vuethis.canUpdate = (result['overDate'] == false) && new Date().getMonth()+1<=vuethis.now.month;
                            vuethis.isLaunch = result['monthly_launched'];
                            vuethis.setting = result;
                            

                            ts.q('#eva-month-' + vuethis.now.month).prop('checked', true);

                            vuethis.launchBotton();
                            
                            $.ym.save({
                              year : vuethis.now.year,
                              month : parseInt(vuethis.now.month)
                            });

                        } else {
                            generalFail();
                        }
                    }).fail(generalFail);
                },
                launchBotton: function() {
                    this.setting.monthly_launched = this.isLaunch ? 1 : 0;
                    this.finalDay = parseInt(this.setting.day_end) + parseInt(this.setting.day_cut_addition);

                    if (this.isLaunch) {
                        ts.q('.process-date input').prop("disabled", true);
                        ts.q('.rate-days input').prop("disabled", true);
                        ts.q('.eva-end-date').show();
                    } else {
                        ts.q('.process-date input').removeAttr("disabled");
                        ts.q('.rate-days input').removeAttr("disabled");
                        ts.q('.eva-end-date').hide();
                    }

                },
                submit: function() {
                    var submitData = {
                        year: this.now.year,
                        month: this.now.month,
                        day_start: this.setting.day_start,
                        day_end: this.setting.day_end,
                        day_cut_addition: this.setting.day_cut_addition,
                        monthly_launched: this.setting.monthly_launched
                    }
                    var vuethis = this;
                    API.updateCycleConfig(submitData).then(function(e) {
                        var cnt = API.format(e);
                        if (cnt.is) {
                            var result = cnt.get();
                            vuethis.setting = result;
                            if (result.hasChanged) {
                                vuethis.refreshMonthly();
                            } else {
                               // alert('更新成功');
                                swal("Success", "更新成功!");
                            }
                        } else {
                            generalFail(cnt.get());
                        }
                    }).fail(generalFail);
                },
                refreshMonthly: function() {

                    var deferred = (this.setting.monthly_launched == 1) ? API.launchMonthly(this.now) : API.pauseMonthly(this.now);
                    deferred.then(function(e) {
                        var cnt = API.format(e);
                        if (cnt.is) {
                            //alert('更新成功');
                            swal("Success", "更新成功!");
                        } else {
                            generalFail(cnt.get());
                        }
                    }).fail(generalFail);

                }
            },
            mounted: function() {
                this.getCycleConfig();
                this.$watch('now', this.getCycleConfig, { deep: true });
            }


        });

    });

    function generalFail(e) {
       // alert('失敗，請重試. \r\n' + (e ? e : ''));
        swal('Fail', '失敗，請重試. \r\n' + (e ? e : ''));
    }

});
