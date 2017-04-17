var $Html = $('html').generalController(function(){
  var ts = this;
  var require = {
    main : $Main
  }
  
  this.onLogin(function(){
    //hashPointOut();
    // location.hash = '';
    hashPointOut();
    $(window).on('hashchange',hashPointOut);
    
  });
  
  function hashPointOut(){
    var hash = location.hash.replace('#','');
    var code = API.decode(hash);
    if(code){
      //console.log(code);
      $.get(code).then(function(htm){
        
        require.main.receive(htm);
        
      }).fail(function(){
      });
    }
  }
  
});




var $Main = $('#main').generalController(function(){
  var ts = this;
  
  this.receive = function(html){
    
    //可能加點轉場
    ts.$.hide(0).html(html).fadeIn(300);
    
  }
  
});