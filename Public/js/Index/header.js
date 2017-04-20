var $Header = $('#Header').generalController(function() {
    // console.log(this);
    var ts = this;

    ts.onLogin(function(member) {
        console.log(member);
        //登出按鈕
        ts.on('click', '[data-toggle=logout]', function(e) {
            e.preventDefault();
            API.logout().then(function() {
                location.reload();
            });
        });

        //樣板
        console.log(ts.q('[data-template="header-right"]'));
        var vu = new Vue({
            el: ts.q('[data-template="header-right"]')[0],
            data: member
        });

        var vusilde = new Vue({
            el: ts.q('#slide-out')[0],
            data: member
        })



        //左邊連結
        ts.on('click', '#slide-out a[href]', function(e) {
                var href = $(this).attr('href').replace(/\?.*/i, '');
                //console.log(href);
                if (href.match(/Template\//i)) {
                    location.hash = API.encode(href);
                    e.preventDefault();
                };
            })
            // .$.contextmenu(function(e){
            // var link = (e.target.href) ? e.target.href.replace(/^.*\/Template\/(.+?)\?.*/i,'$1') : false;
            // if(link){
            // e.preventDefault();
            // contextmenu.text(link).appendTo(document.body).show().css({left:e.pageX,top:e.pageY});
            // }
            // }).parents(window).on('click',function(){ contextmenu.detach(); });

        //方便按鈕
        // var contextmenu = $('<div class="content-menu"></div>').click(function(){
        // location.href = this.innerText;
        // });


    });
    (function($) {
      // snow : 這段 可以 但包 jquery inner function 是多餘的
        $.fn.clickToggle = function(func1, func2) {
            var funcs = [func1, func2];
            this.data('toggleclicked', 0);
            this.click(function() {
                var data = $(this).data();
                var tc = data.toggleclicked;
                $.proxy(funcs[tc], this)();     //本來沒有用到這個 ??
                data.toggleclicked = (tc + 1) % 2;
            });
            return this;
        };
    }(jQuery));

    // 左側menu小於1280，變成小漢堡menu，click則打開menu
    var slideMenu = ts.q('#slide-out').css('display','block');    //snow : 該區塊如果要有滑動效果 要一直存在
    var btnCollapse = ts.q('.button-collapse');

    // snow : 重複邏輯
    // btnCollapse.clickToggle(function() {
        // slideMenu.css('transform', 'translateX(0)');
        // slideMenu.show();
    // }, function() {
        // slideMenu.css('transform', 'translateX(-100%)');
        // slideMenu.hide();
    // });

    // snow : 重覆邏輯 & 重覆選取
    
    // 如果width> 1281 則左測menu展開, 小於1280 就關閉
    // $(window).resize(function() {
        // if ($(window).width() > 1281) {
            // slideMenu.css('transform', 'translateX(0)');
            // slideMenu.show();
        // }
        // if ($(window).width() < 1280) {
            // slideMenu.css('transform', 'translateX(-100%)');
            // slideMenu.hide();
            // snow : //如果要用 1,0判斷縮放  那縮回去的時後要記得設成0  不然被 window.resize 縮的時後開合就會不正確
        // }
    // })
    // snow : better
    slideMenu.in = function(){
      slideMenu.css('transform', 'translateX(-100%)');
      btnCollapse.data('toggleclicked', 0);
    }
    slideMenu.out = function(){
      slideMenu.css('transform', 'translateX(0%)');
    }
    btnCollapse.clickToggle( slideMenu.out , slideMenu.in );
    var $w = $(window).resize(function(){
      if($w.width() > 1281){
        slideMenu.out();
      }else{
        slideMenu.in();
      }
    });
    

    ts.onShown(function() {
        // console.log(this);
    });

    ts.onHidden(function() {
        // console.log(this);
    });


});
