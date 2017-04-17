var $Header = $('#Header').generalController(function(){
  // console.log(this);
  var ts = this;
  
  ts.onLogin(function(member){
    console.log(member);
    //登出按鈕
    ts.on('click','[data-toggle=logout]',function(e){
      e.preventDefault();
      API.logout().then(function(){
        location.reload();
      });
    });
    
    //樣板
    var vu = new Vue({
      el:ts.q('[data-template="header-right"]')[0],
      data:member
    });
    
    var vusilde = new Vue({
      el:ts.q('#slide-out')[0],
      data:member
    })
    
    
    
    //左邊連結
    ts.on('click','#slide-out a[href]',function(e){
      var href = $(this).attr('href').replace(/\?.*/i,'');
      //console.log(href);
      if(href.match(/Template\//i)){
        location.hash = API.encode(href);
        e.preventDefault();
      };
    }).$.contextmenu(function(e){
      var link = (e.target.href) ? e.target.href.replace(/^.*\/Template\/(.+?)\?.*/i,'$1') : false;
      if(link){
        e.preventDefault();
        contextmenu.text(link).appendTo(document.body).show().css({left:e.clientX,top:e.clientY}); 
      }
    }).parents(window).on('click',function(){ contextmenu.detach(); });
    
    //方便按鈕
    var contextmenu = $('<div></div>').css({
      position:'absolute',
      top:0,
      left:0,
      width:200,
      'z-index':1000,
      background:'#f9f9f9',
      border:'1px solid #bcbcbc',
      'box-shadow':'1px 1px 2px #333',
      color:'#222',
      display:'none',
      cursor:'pointer',
      'text-align':'center',
      padding:'5px'
    }).click(function(){
      location.href = this.innerText;
    });
    
    
  });
  
  ts.onShown(function(){
    // console.log(this);
  });
  
  ts.onHidden(function(){
    // console.log(this);
  });
  
  
});
