var $assessForm = $('#Assess').generalController(function() {
    var ts = this;
    ts.templateArray = [];
    ts.vuesObj = {};
    ts.onLogin(function(member) {

        var today = new Date();
        var currentYear = today.getFullYear()
        var currentMonth = today.getMonth() + 1
        var init = {
            year: currentYear,
            staff_id: member.id
        }
        API.getMonthlyProcessWithOwner(init).then(function(json) {
            var collectHasForm = API.format(json);
            var getMonthlyData = 0;
            var count = 1;

            if (collectHasForm.is) {
                var result = collectHasForm.res();
                var apiArray = [];
                for (var id in result) {
                    var formId = result[id].id;
                    var data = {
                        processing_id: formId
                    }
                    apiArray.push(API.getMonthlyReport(data));
                }
                $.when.all(apiArray).then(function(data) {
                    var newData = (typeof data[1] == "string") ? [data] : data;
                    for (var i in newData) {
                        var loc = newData[i];
                        var result = API.format((loc[0] || loc)).get();
                        for (var r in result) {
                            var getMonthlyData = result[r];
                            callVueRender(getMonthlyData);
                        }
                    }
                    contentMunu();
                    // 人事要移除滾輪加減分的功能
                    //mouseWheel();
                    fix();
                    $("table").fixMe();
                });
            } else {
                ts.q('#NoData').show();
            }

            function callVueRender(param) {
                var rand = 'row' + (count++);
                var tmp1 = null
                ts.q('#AssessForm').append('<div id="' + rand + '" ></div>');
                console.log(JSON.parse(JSON.stringify(param)));
                var next = param.path_staff_id.indexOf(param.owner_staff_id) + 1;
                param._next_staff = param._path_staff[param.path_staff_id[next]];
                var tmp1 = new Vue({
                    template: '#template-1',
                    el: '#' + rand,
                    data: {
                        year: param.year,
                        month: param.month,
                        rand: rand,
                        member: member,
                        recvice: param,
                        changed: {},
                        records: [],
                        comments: [],
                        modal: _vue_modal
                    },
                    methods: {
                        save: function() {
                            if (!this.isEmptyObject(this.changed)) {
                                var changedObj = JSON.parse(JSON.stringify(this.changed))
                                var insertData = {
                                    report: changedObj
                                };
                                API.saveReport(insertData).then(function(e) {
                                    var success = API.format(e);
                                    if (success.is) {
                                        Materialize.toast('已儲存完畢您的變更', 2000)
                                    }
                                })
                            } else {
                                Materialize.toast('資料尚未變更', 2000)
                            }
                        },
                        collectBackData: function(report, e, field) {
                            //console.log(report);
                            if (field == 'bonus') {
                                if (report.bonus) {
                                    report.bonus = 1;
                                } else {
                                    report.bonus = 0;
                                }
                            } else {
                                var tar = e.target;
                                var val = report[field] = parseInt(report[field]);
                                if (!val) {
                                    val = 0;
                                }
                                if (tar.min) {
                                    report[field] = Math.max(val, tar.min);
                                }
                                if (tar.max) {
                                    report[field] = Math.min(val, tar.max);
                                }
                            }

                            if (!this.changed[report.id]) {
                                this.changed[report.id] = {
                                    id: report.id,
                                    processing_id: report.processing_id
                                };
                            }
                            this.changed[report.id][field] = report[field];
                        },
                        commit: function() {
                            this.save();
                            var commitId = {
                                processing_id: this.recvice.id
                            }
                            API.commitMonthly(commitId).then(function(e) {
                                var success = API.format(e);
                                if (success.is) {
                                    Materialize.toast('已提交送審', 2000)
                                }
                            })
                            $(this.$el).remove();
                        },
                        open: function(param) {
                            ts.q('#ReJectModal-' + rand).modal({
                                dismissible: false
                            });
                            var processingId = {
                                processing_id: param.id
                            }
                            API.getMonthlyRejectList(processingId).then(function(e) {
                                var result = API.format(e);
                                if (result.is) {
                                    var list = result.get();
                                    var rj = ts.q('#ReJectModal-' + rand).find("select").empty();
                                    for (var l in list) {
                                        rj.append('<option value="' + list[l].id + '">' + list[l].name + " " + list[l].name_en + '</option>');
                                    }
                                }
                            })
                        },
                        reject: function(param) {
                            this.save();
                            var ownerId = param.id
                            var backId = ts.q('#ReJectModal-' + rand + ' option:selected').val()
                            var backReason = ts.q('#ReJectModal-' + rand + ' textarea').val()
                            var rejectData = {
                                processing_id: ownerId,
                                staff_id: backId,
                                reason: backReason
                            }
                            if (backReason != '') {
                                API.rejectMonthly(rejectData).then(function(e) {
                                    var success = API.format(e);
                                    if (success.is) {
                                        Materialize.toast('已退回該表單', 2000)
                                    }
                                })
                                if (backId != undefined) {
                                    ts.q('#ReJectModal-' + rand).modal("close")
                                    $(this.$el).remove();
                                } else {
                                    ts.q('#ReJectModal-' + rand).modal("close")
                                    Materialize.toast('此單您無法執行退回動作', 2000)
                                }
                            } else {
                                //alert("請輸入退回原因");
                                swal("Hi", "請輸入退回原因!");
                            }
                        },
                        history: function() {
                            this.modal.monthly_history.show(this.recvice.id);
                        },
                        absence: function() {
                            var after;
                            if (this.recvice.type == 1) {
                                after = "&staff=";
                                var ary = [];
                                for (var i in this.recvice._reports) {
                                    ary.push(this.recvice._reports[i].staff_id);
                                }
                                after += ary.join(',');
                            } else {
                                after = "&team=" + this.recvice.created_department_id;
                            }
                            window.open("None/Frame/absence?year=" + this.year + "&month=" + this.month + after);
                        },
                        getComment: function(data) {
                            var vuethis = this;
                            // if (this.recvice.year == this.year && this.recvice.month == this.month) {
                            API.getComment(data).then(function(e) {
                                    var result = API.format(e);
                                    console.log(result.res())
                                    if (result.is) {
                                        var comment = result.res()
                                        for (var loc in comment) {
                                            comment[loc]["name_head"] = comment[loc]._created_staff_name.charAt(0);
                                        }
                                        vuethis.comments = comment
                                    }
                                })
                                // }
                        },
                        comment: function(report) {
                            this.comments = [];
                            console.log(report);
                            var commentData = {
                                staff_id: report.staff_id,
                                year: report.year,
                                month: report.month
                            }
                            this.getComment(commentData)
                        },
                        addComment: function(obj, index) {
                            var txt = ts.q("#CommentText" + (index + 1) + "-" + rand).val()
                            var target = {
                                staff_id: obj.staff_id,
                                year: this.recvice.year,
                                month: this.recvice.month,
                                content: txt
                            }
                            if (txt != "") {
                                var vss = this;
                                API.addComment(target).then(function(e) {
                                    if (API.format(e).is) {
                                        Materialize.toast('已新增一筆評論', 2000);
                                        vss.getComment(target);
                                        ts.q("#CommentText" + (index + 1) + "-" + rand).val('')
                                        var autoScroll = ts.q("#CommentStaffModal-" + (index + 1) + rand);
                                        console.log(autoScroll);
                                        autoScroll.animate({
                                            scrollTop: autoScroll.q('.modal-content').height()
                                        }, 1000);
                                        obj._comment_count++;
                                    } else {
                                        //return alert('無法輸入');
                                        return swal("Error", "無法輸入");
                                    }
                                });
                            } else {
                                //alert('您尚未輸入任何評論');
                                swal("Hi", "您尚未輸入任何評論");
                            }
                        },
                        editOpen: function(txt, index) {
                            // 隱藏輸入欄位下的按鈕
                            // console.log( ts.q('#'+index) );
                            var list = ts.q('#'+index);
                            list.parents('.row').find(".edit-input").hide();
                            // ts.q("#CommentText" + (index + 1) + "-" + rand).siblings().hide();
                            // var list = ts.q("#CommentRecord" + (index ) + "-" + rand)
                            var txt = list.find(".comment-content").text();
                            // console.log(list);
                            list.find(".edit-input").show();
                            list.find("input").val(txt);
                        },
                        editComment: function(index, id) {
                            // var list = ts.q("#CommentRecord" + (index + 1) + "-" + rand)
                            var list = ts.q('#'+index);
                            var content = list.find("input").val()
                            var editParam = {
                                comment_id: id,
                                do: "upd",
                                content: content
                            }
                            API.updateComment(editParam).then(function(e) {
                                var success = API.format(e);
                                if (!success.is) {
                                    list.find(".edit-input").hide()
                                        //return alert('無法編輯');
                                    return swal("!", "無法編輯!");
                                } else {
                                    Materialize.toast('已更新一筆評論', 2000)
                                    list.find(".edit-input").hide()
                                    list.find(".comment-content").text(editParam.content)
                                }
                            })
                        },
                        deleteComment: function(id, obj, index) {
                            var deleteParam = {
                                comment_id: id,
                                do: "del"
                            }
                            var vss = this;
                            API.updateComment(deleteParam).then(function(e) {

                                if (API.format(e).is) {
                                    // 改變資料面，從而改變DOM
                                    vss.comments.splice(index, 1);
                                    Materialize.toast('已刪除一筆評論', 2000)
                                    obj._comment_count--;
                                } else {
                                    Materialize.toast('更新失敗', 2000);
                                }
                            })
                        },
                        total: function(report) {
                            if (this.recvice.type == 1) {
                                // 主管們的總分
                                var score_total = (report.target * 2) + (report.quality * 2) + (report.method * 2) + (report.error * 2) + (report.backtrack * 2) + (report.planning * 2) + (report.execute * 1.4) + (report.decision * 1.4) + (report.resilience * 1.2) + (report.attendance * 2) + (report.attendance_members * 2);
                                score_total = Math.min(score_total, 100) + report.addedValue - report.mistake;
                                if (score_total < 0) {
                                    return score_total = 0
                                } else {
                                    return Math.round(score_total)
                                }
                            } else {
                                // 員工們的總分
                                if (report.duty_shift == 0) {
                                    // 一般員工的總分
                                    var score_total = (report.quality * 5) + (report.completeness * 5) + (report.responsibility * 5) + (report.cooperation * 3) + (report.attendance * 2);

                                } else {
                                    // 值班員工的總分
                                    var score_total = (report.quality * 5) + (report.completeness * 5) + (report.responsibility * 3) + (report.cooperation * 3) + (report.attendance * 4);
                                }
                                score_total = Math.min(score_total, 100) + report.addedValue - report.mistake;
                                if (score_total < 0) {
                                    return score_total = 0
                                } else {
                                    return score_total
                                }
                            }
                        },
                        isEmptyObject: function(obj) {
                            for (var name in obj) {
                                if (obj.hasOwnProperty(name)) {
                                    return false;
                                }
                            }
                            return true;
                        },
                        decideFloat: function(e, pnumber) {
                            if (!/^\+?[0-5]*$/.test(pnumber)) {
                                e.value = /\+?[0-5]*/.exec(e.value);
                                //alert("請輸入0~5的整數")
                                swal("!", "請輸入0~5的整數");
                            }
                            return false;
                        }
                    }
                });
                ts.vuesObj[rand] = tmp1;
                ts.templateArray.push(tmp1);
                var ele = tmp1.$el;
                ts.q(".modal").modal();
                ts.q(ele).q('.collapsible').collapsible();
                ts.q("#CommentText").focus(function() {
                    ts.q("#CommentText" + (index + 1) + "-" + rand).siblings().show();
                });
            }


            function contentMunu() {
                ts.$.on('contextmenu', '.rv-assess >div.row', function(e) {
                    e.preventDefault();
                    $t = ts.q(this);
                    var vueKey = $t.data('vue');
                    var vue_object = ts.vuesObj[vueKey];
                    contextmenu.appendTo(document.body).show().css({ left: e.pageX, top: e.pageY });
                    contextmenu.targetVue = vue_object;
                }).parents(window).on('click', function() { contextmenu.detach(); });

                var contextmenu = $('<div class="content-menu"> <li class="save">儲存</li> <li class="absence">出缺席記錄</li> <li class="history">歷史記錄</li> <li class="top">回到此單頂部</li> </div>').on('click', 'li', function() {
                    //console.log(contextmenu.targetVue);
                    var vue = contextmenu.targetVue;
                    switch (this.className) {
                        case "save":
                            vue.save.apply(vue);
                            break;
                        case "absence":
                            vue.absence.apply(vue);
                            break;
                        case "history":
                            vue.history.apply(vue);
                            break;
                        case "top":
                            var header = ts.q(vue.$el).q('.collapsible-header');
                            var top = header.position().top - header.height();
                            $('body,html').animate({ scrollTop: top }, 500);
                            break;
                    }
                });

            }

            function mouseWheel() {
                var inputEvent = new Event('input');
                var changeEvent = new Event('change');
                ts.$.on('mousewheel', '.rv-assess .card-cell', function(e) {
                    var $t = ts.q(this);
                    var $input = $t.q('input[type=number]'),
                        input = $input[0];
                    if ($input.length == 0) {
                        return;
                    }
                    var value = Number($input.val());
                    //console.log(e);
                    //console.log( e.originalEvent );

                    e.preventDefault();
                    if (e.originalEvent.deltaY > 0) {
                        var res = Math.max(value - 1, input.min || 0);
                    } else {
                        var res = input.max ? Math.min(value + 1, input.max) : value + 1;
                    }
                    $input.val(res);
                    input.dispatchEvent(inputEvent);
                    input.dispatchEvent(changeEvent);
                });
            }

            function fix() {
                $.fn.fixMe = function() {
                    return this.each(function() {
                        var $this = $(this),
                            $t_fixed;

                        function init() {
                            $this.wrap('<div class="staff-table">');
                            $t_fixed = $this.clone();
                            $t_fixed.find("tbody").remove().end().addClass("fixedTable").insertBefore($this);
                            resizeFixed();
                        }

                        function resizeFixed() {
                            // $t_fixed.find("th").each(function(index) {
                            //     $(this).css("width", $this.find("th").eq(index).outerWidth() + "px");
                            // });

                            var tWidth = $this.find('thead').outerWidth();
                            //console.log(tWidth);
                            // $t_fixed.css('width',tWidth+'px');
                            $t_fixed.find('thead').css('width', tWidth + 'px');
                        }

                        function scrollFixed() {
                            var offset = $(this).scrollTop(),
                                tableOffsetTop = $this.offset().top,
                                tableOffsetBottom = tableOffsetTop + $this.height() - $this.find("thead").height();
                            if (offset < tableOffsetTop || offset > tableOffsetBottom) {
                                $t_fixed.hide();
                            } else if (offset >= tableOffsetTop && offset <= tableOffsetBottom && $t_fixed.is(":hidden")) {
                                $t_fixed.show();
                            }
                            resizeFixed();
                        }
                        $(window).resize(resizeFixed);
                        $(window).scroll(scrollFixed);
                        init();
                    });
                };
            }
        });
    });
});
