var $Review = $('#Review').generalController(function() {
    var ts = this;
    var current = $.ym.get();
    var getYear = current.year;
    var getMonth = current.month;
    var year = ts.q("#getYear").empty();
    var month = ts.q("#getMonth").empty();
    var autocomplete = ts.q('#autocomplete-input');
    function initYM() {
        var thisyear = new Date().getFullYear();
        for (i = thisyear; i >= API.create.year ; i--) {
            year.append('<option value="' + i + '">' + i + '年</option>');
        }
        for (i = 1; i <= 12; i++) {
            month.append('<option value="' + i + '">' + i + '月</option>');
        }
    }
    initYM();

    // 選取當前的年月
    // var currentYear = year.val(getYear);
    // var currentMonth = month.val(getMonth);

    ts.onLogin(function(member) {
        API.getUnderStaff().then(function(e) {
            var result = API.format(e);
            if (result.is) {
                var list = ts.staff_list = result.res();
                var staffArray = [];
                var staffObj = {}
                // ts.staff_map = result.map();
                ts.staff_account_map = {}

                // 資料做成dictionary，[key:value]→["liz.teng 鄧幼華" : Object]。
                for (var i in list) {
                    var loc = list[i].account + ' ' + list[i].name;
                    staffArray.push(loc);
                    ts.staff_account_map[loc] = list[i];
                }

                for (var staff in staffArray) {
                    var key = staffArray[staff]
                    staffObj[key] = null
                }

                var vm = new Vue({
                    el: '#Review .rv-review',
                    data: {
                        inputData: autocomplete.val(),
                        year: current.year,
                        month: current.month,
                        staffObj: staffObj,
                        currentStaff: {},
                        commentRecord: []
                    },
                    methods: {
                        addComment: function() {
                            var txt = ts.q("#CommentText").val()
                            var target = {
                                staff_id: this.currentStaff.id,
                                year: this.year,
                                month: this.month,
                                content: txt
                            }
                            if (txt != "") {
                                var vss = this;
                                API.addComment(target).then(function(e) {
                                    if (API.format(e).is) {
                                        //Materialize.toast('已新增一筆評論', 2000);
                                        swal("OK","已新增一筆評論");

                                        vss.getComment(target);
                                        ts.q("#CommentText").val('')
                                        // AutoScroll to bottom
                                        var autoScroll = ts.q(".comment-content-area");
                                        autoScroll.animate({
                                            scrollTop: autoScroll.q('.comment-content-list').height()
                                        }, 1000);
                                    } else {
                                        //return alert('無法輸入');
                                        return swal("!","無法輸入");
                                    }
                                })
                            } else {
                                //alert('您尚未輸入任何評論');
                                swal("Hi","您尚未輸入任何評論");
                            }
                        },
                        editOpen: function(index) {
                            ts.q(".comment-content-list").find(".edit-input").hide();
                            ts.q("#CommentText").siblings().hide();
                            var list = ts.q("#record-" + (index + 1))
                            var txt = list.find(".content").text();
                            list.find(".edit-input").show()
                            list.find("input").val(txt)
                        },
                        editComment: function(index, id) {
                            var list = ts.q("#record-" + (index + 1))
                            var content = ts.q("#record-" + (index + 1)).find("input").val()
                            var editParam = {
                                comment_id: id,
                                do: "upd",
                                content: content
                            }
                            var vss = this
                            API.updateComment(editParam).then(function(e) {
                                if (!API.format(e).is) {
                                    list.find(".edit-input").hide()
                                    //return alert('無法編輯');
                                    return swal("!","無法編輯");
                                } else {
                                    //Materialize.toast('已更新一筆評論', 2000)
                                    swal("OK","已更新一筆評論");
                                    list.find(".edit-input").hide()
                                    list.find(".comment-content .content").text(editParam.content)
                                }
                            })
                        },
                        deleteComment: function(id, index, record) {
                            var deleteParam = {
                                comment_id: id,
                                do: "del"
                            }
                            var vss = this;
                            API.updateComment(deleteParam).then(function(e) {

                                if (API.format(e).is) {
                                    // 改變資料面，從而改變DOM
                                    //Materialize.toast('已刪除一筆評論', 2000);
                                    swal("OK","已刪除一筆評論!");
                                    vss.commentRecord.splice(index, 1);
                                } else {
                                    //Materialize.toast('更新失敗', 2000);
                                    swal("Error","更新失敗!");
                                }
                            })
                        },
                        getComment: function(staff) {
                            var vss = this;
                            API.getComment(staff).then(function(e) {
                                var result = API.format(e);
                                var comment = result.res();
                                for (var loc in comment) {
                                    comment[loc]["name_head"] = comment[loc]._created_staff_name.charAt(0);
                                }
                                vss.commentRecord = comment;

                                var autoScroll = ts.q(".comment-content-area");

                                var timer = setTimeout(function(){
                                  autoScroll.animate({
                                      scrollTop: autoScroll.q('.comment-content-list').height()
                                  }, 1000);
                                },50);

                            })
                        },
                        selected: function() {
                            $.ym.save(this.$data);
                            if (this.currentStaff.id) {
                                var data = {
                                    staff_id: this.currentStaff.id,
                                    year: this.year,
                                    month: this.month
                                }
                                
                                this.getComment(data);
                            } else {
                                console.log("沒有評論目標");
                            }
                        }
                    },
                    mounted: function() {
                        var vss = this;
                        ts.q('input.autocomplete').autocomplete({
                            data: vss.staffObj,
                            onAutocomplete: function(value) {
                                var targetStaff = ts.staff_account_map[value];
                                vss.currentStaff = targetStaff
                                var staff = {
                                    staff_id: vss.currentStaff.id,
                                    year: vss.year,
                                    month: vss.month
                                }
                                $.ym.save(staff);
                                vss.getComment(staff);
                                ts.q(".comment-area").css("opacity", "1")
                                ts.q(".comment-area").css("top", "0px")
                                ts.q(".search-area").css("top", "0px")
                                ts.q('#autocomplete-input').val('')

                                ts.q("#CommentText").focus(function() {
                                    ts.q("#CommentText").siblings().show();
                                });
                            }
                        })
                    }
                })
            } else {
                console.log("Message:" + result.get())
            }
        });
    });
});