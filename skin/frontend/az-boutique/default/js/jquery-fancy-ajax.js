function showOptions(t){jQuery("#fancybox"+t).trigger("click")}function setAjaxData(t){"ERROR"==t.status?(jQuery(".popup-text").html(t.message),jQuery.fancybox({content:jQuery("#addtocart_popup").html(),padding:20})):(jQuery.ajax({url:url_topcart,type:"POST",data:{},success:function(t){jQuery("#mini-cart").html(t),jQuery(".fancybox").fancybox({hideOnContentClick:!0,width:520,autoDimensions:!0,type:"iframe",showTitle:!1,scrolling:"no",onComplete:function(){jQuery("#fancybox-frame").load(function(){jQuery("#fancybox-content").height(jQuery(this).contents().find("body").height()+30),jQuery.fancybox.resize()})}}),truncted_details(),displayTopCart()}}),jQuery(".popup-text").html(t.message),jQuery.fancybox({content:jQuery("#addtocart_popup").html(),padding:20,minWidth:300}))}function setLocationAjax(t){t+="isAjax/1",t=t.replace("checkout/cart","ajax/cart"),showproductloading();try{jQuery.ajax({url:t,dataType:"json",success:function(t){jQuery("#pro-loading").remove(),jQuery("#pro-img").remove(),setAjaxData(t,!1)}})}catch(e){}}jQuery(document).ready(function(){jQuery("#min-cart").mouseover(function(){jQuery("#min-cart .dropdown  .dropdown-menu").css({display:"block !important"})}),jQuery("#min-cart").mouseout(function(){alert("dewrewsa"),jQuery("#min-cart .dropdown  .dropdown-menu").css({display:"none !important"})}),jQuery(".fancybox").fancybox({hideOnContentClick:!0,width:520,autoDimensions:!0,type:"iframe",showTitle:!1,scrolling:"no",onComplete:function(){jQuery("#fancybox-frame").load(function(){jQuery("#fancybox-content").height(jQuery(this).contents().find("body").height()+30),jQuery.fancybox.resize()})}})});