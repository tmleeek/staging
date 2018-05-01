
jQuery(document).ready(function() {

 jQuery(".product-problem-link").click(function(e){
  e.preventDefault();
    loginFancybox(this);
 });


 jQuery("#cartpopup").click(function(e){ alert("click");
  e.preventDefault();
    loginFancybox(this);
 });

function loginFancybox(urlpopup)
 {  alert("login");
  url = urlpopup;
  //alert(url);
  jQuery.fancybox(
    {
       hideOnContentClick : true,
     //  width:500,
     //  height:510,
       minHeight:475,
      // autoDimensions: true,
       //autoScale : true,
       type : 'iframe',
       href : url,
       showTitle: true,
       scrolling: 'no',
       onComplete: function(){
     jQuery('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
      jQuery('#fancybox-content').height(jQuery(this).contents().find('body').height()+200);
      //jQuery.fancybox.resize();
      });

       }
     }
     );
 }
});


   