jQuery(document).ready(function()
{jQuery(".footer-container").next('a.top-link-cart').remove();jQuery(".page-popup").find('a.top-link-cart').remove();jQuery(".page-popup").find('div#ajax_topcart').remove();fancybox_update();var updatecart_action=window.top.location.href;if(updatecart_action.search('checkout/cart/configure')!=-1)
{var form_action=jQuery("#product_addtocart_form").attr('action');var ajax_form_action=form_action.replace('checkout/cart/updateItemOptions','ajax/cart/updateItemOptions');jQuery("#product_addtocart_form").attr('action',ajax_form_action);}
else if(updatecart_action.search('checkout/cart')!=-1)
{jQuery(".add-to-box").find('button.btn-cart').removeAttr('onclick');jQuery(".add-to-box").find('button.btn-cart').click(function(){editProduct();});jQuery(".product-options-bottom").find('button.btn-cart').removeAttr('onclick');jQuery(".product-options-bottom").find('button.btn-cart').click(function(){editProduct();});}});var timer;jQuery('.top-link-cart').live("hover",function(){clearInterval(timer);jQuery('#ajax_topcart').show();timer=setInterval(hidediv,8000);});jQuery('#ajax_topcart').live("mouseenter",function(){clearInterval(timer);});jQuery('#ajax_topcart').live("mouseleave",function(){timer=setInterval(hidediv,8000);});jQuery('#close_ajax_topcart').live("click",function(){jQuery('#ajax_topcart').hide();});function hidediv()
{jQuery('#ajax_topcart').hide();}
function displayTopCart(){clearInterval(timer);jQuery('#ajax_topcart').show();timer=setInterval(hidediv,8000);}
function showcartloading(){jQuery(".cart").css("position","relative");jQuery(".cart").append("<div style='position:absolute;top:0px;left:0px;right:0px;bottom:0px;height:100%;width:100%;background:white;margin:0px;-moz-opacity:.40;filter:alpha(opacity=40);opacity:0.4;z-index:888'></div>");var img="<div style='position:absolute;top:25%;left:50%;margin-left:-33px;z-index:889'><img src='"+image+"'/></div>";jQuery(".cart").append(img);}
function showproductloading(){jQuery(".col-main").css("position","relative");jQuery(".col-main").append("<div id='pro-loading' style='position:absolute;top:0px;left:0px;right:0px;bottom:0px;height:100%;width:100%;background:white;margin:0px;-moz-opacity:.40;filter:alpha(opacity=40);opacity:0.4;z-index:888'></div>");var img="<div id='pro-img' style='position:absolute;top:25%;left:50%;margin-left:-33px;z-index:889'><img src='"+image+"'/><br/>"+loading_text+"</div>";jQuery(".col-main").append(img);}
function showviewloading(){jQuery(".product-view").css("position","relative");jQuery(".product-view").append("<div id='pro-loading' style='position:absolute;top:0px;left:0px;right:0px;bottom:0px;height:100%;width:100%;background:white;margin:0px;-moz-opacity:.40;filter:alpha(opacity=40);opacity:0.4;z-index:888'></div>");var img="<div id='pro-img' style='position:absolute;top:25%;left:50%;margin-left:-33px;z-index:889'><img src='"+image+"'/><br/>"+loading_text+"</div>";jQuery(".product-view").append(img);}
function fancybox_update()
{jQuery('.fancybox').fancybox({hideOnContentClick:true,width:520,autoDimensions:true,type:'iframe',showTitle:false,scrolling:'no',onComplete:function(){jQuery('#fancybox-frame').load(function(){jQuery('#fancybox-content').height(jQuery(this).contents().find('body').height()+30);jQuery.fancybox.resize();});}});}
function truncted_details()
{$$('.truncated').each(function(element){Event.observe(element,'mouseover',function(){if(element.down('div.truncated_full_value')){element.down('div.truncated_full_value').addClassName('show')}});Event.observe(element,'mouseout',function(){if(element.down('div.truncated_full_value')){element.down('div.truncated_full_value').removeClassName('show')}});});}
function cartdelete(url,del_id)
{showcartloading();jQuery('#ajax_loader_'+del_id).show();jQuery('#ajax_loader'+del_id).show();url=url.replace('checkout/cart/delete','ajax/ajax/headercartdelete/cart/delete');jQuery.ajax({url:url,type:"POST",dataType:"html",data:{btn_lnk:1},success:function(data)
{jQuery('#ajax_loader_'+del_id).hide();jQuery('#ajax_loader'+del_id).hide();jQuery("#overlay").hide();var result=jQuery(data);data_top_link=jQuery(result).find('div#cart_content');jQuery('.top-link-cart').html(data_top_link);data_top_cart=jQuery(result).find('div#ajax_topcart');jQuery("#ajax_topcart").replaceWith(data_top_cart);truncted_details();displayTopCart();region_id();data_cart_content=jQuery(result).find('div#ajax_cart_content').html();jQuery('.cart').replaceWith(data_cart_content);region_updater();fancybox_update();}});}
function updateheaderCart(item,qty)
{jQuery('#ajax_loader'+item).show();showcartloading();url=url_update;jQuery.ajax({url:url,type:"POST",dataType:"html",data:{"item":item,"qty":qty},success:function(data)
{jQuery('#ajax_loader'+item).hide();var result=jQuery(data);data_top_link=jQuery(result).find('div#cart_content').text();jQuery('.top-link-cart').html(data_top_link);data_top_cart=jQuery(result).find('div#ajax_topcart');jQuery("#ajax_topcart").replaceWith(data_top_cart);truncted_details();displayTopCart();region_id();data_cart_content=jQuery(result).find('div#ajax_cart_content').html();jQuery('.cart').replaceWith(data_cart_content);region_updater();fancybox_update();}});}
function updateCart(item,qty)
{jQuery('#ajax_loader_'+item).show();showcartloading();var url=url_update_shoopingcart;jQuery.ajax({url:url,type:"POST",dataType:"html",data:{"item":item,"qty":qty},success:function(data)
{jQuery('#ajax_loader'+item).hide();var result=jQuery(data);var href=jQuery(location).attr('href');region_id();data_cart_main_content=jQuery(result).find('div.cart');jQuery('.cart').replaceWith(data_cart_main_content);region_updater();var url=url_topcart;jQuery.ajax({url:url,type:"POST",data:{},success:function(data)
{var returndata=data;jQuery('#mini-cart').html(returndata);truncted_details();displayTopCart();}});fancybox_update();}});}
function emptyCart()
{showcartloading();var url=url_update_shoopingcart;jQuery.ajax({url:url,type:"POST",dataType:"html",data:{action:'empty_cart'},success:function(data)
{var result=jQuery(data);data_cart_main_content=jQuery(result).find('div.col-main').html();jQuery('.cart').replaceWith(data_cart_main_content);var url=url_topcart;jQuery.ajax({url:url,type:"POST",data:{},success:function(data)
{var returndata=data;jQuery('#mini-cart').html(returndata);truncted_details();displayTopCart();}});fancybox_update();}});}
function ajaxCompare(url,id)
{url=url.replace("catalog/product_compare/add","ajax/whishlist/compare");url+='isAjax/1/';showproductloading();showviewloading();jQuery.ajax({url:url,dataType:'json',success:function(data){jQuery("#pro-loading").remove();jQuery("#pro-img").remove();jQuery("#pro-view-loading").remove();jQuery("#pro-view-img").remove();if(data.status=='ERROR'){alert(data.message);}else{alert(data.message);if(jQuery('.block-compare').length){jQuery('.block-compare').replaceWith(data.sidebar);}else{if(jQuery('.col-right').length){jQuery('.col-right').prepend(data.sidebar);}}}}});}
function ajaxWishlist(url,id)
{url=url.replace("wishlist/index","ajax/whishlist");url+='isAjax/1/';showproductloading();jQuery.ajax({url:url,dataType:'json',success:function(data){jQuery("#pro-loading").remove();jQuery("#pro-img").remove();if(data.status=='ERROR'){alert(data.message);}else{alert(data.message);var topwish_count=data.counter;var remove_url='"'+data.removeUrl+'/"';var prod_id='"'+data.productId+'"';var wish_id=jQuery('#wishlist'+id);jQuery(wish_id).html('Remove from Wishlist');var remove_btn=jQuery(wish_id).parent().attr('onClick','removeWishlist('+remove_url+','+prod_id+') ;return false;');if(jQuery('ul.links').length){jQuery('ul.links').replaceWith(data.toplink);}
jQuery('#wishlist_count').text(topwish_count);if(jQuery('.block-wishlist').length){jQuery('.block-wishlist').replaceWith(data.sidebar);}else{if(jQuery('.col-right').length){jQuery('.col-right').prepend(data.sidebar);}}
url_topcart=url_headercart;jQuery.ajax({url:url_topcart,type:"POST",data:{},success:function(data)
{var returndata=data;jQuery('#mini-cart').html(returndata);truncted_details();}});}}});}
function removeWishlist(url,id)
{url=url.replace("wishlist/index/remove","ajax/whishlist/remove");url+='isAjax/1/';showproductloading();jQuery.ajax({url:url,dataType:'json',success:function(data){jQuery("#pro-loading").remove();jQuery("#pro-img").remove();if(data.status=='ERROR'){alert(data.message);}else{var topwish_count=data.counter;var add_url='"'+data.addUrl+'/"';var prod_id='"'+data.prod_id+'"';var wish_id=jQuery('#wishlist'+id);jQuery(wish_id).html('Add to Wishlist');var add_btn=jQuery(wish_id).parent().attr('onClick','ajaxWishlist('+add_url+','+prod_id+');return false;');if(jQuery('ul.links').length){jQuery('ul.links').replaceWith(data.toplink);}
jQuery('#wishlist_count').text(topwish_count);if(jQuery('.block-wishlist').length){jQuery('.block-wishlist').replaceWith(data.sidebar);}else{if(jQuery('.col-right').length){jQuery('.col-right').prepend(data.sidebar);}}
url_topcart=url_headercart;jQuery.ajax({url:url_topcart,type:"POST",data:{},success:function(data)
{var returndata=data;jQuery('#mini-cart').html(returndata);truncted_details();}});}}});}
function region_id(){var href=jQuery(location).attr('href');if(href.search('checkout/cart')!=-1)
{$('region_id').setAttribute('defaultValue',estimateRegionId);}}
function region_updater(){var href=jQuery(location).attr('href');if(href.search('checkout/cart')!=-1)
{new RegionUpdater('country','region','region_id',region_json);}}
function editProduct()
{var form_url=jQuery("#product_addtocart_form").attr("action");showviewloading();jQuery.ajax({url:form_url,type:"POST",dataType:"html",data:jQuery('form').serialize(),success:function(data)
{url=cart_url;jQuery.ajax({url:url,type:"POST",dataType:"html",data:{},success:function(data)
{var result=data;jQuery("#pro-view-loading").remove();jQuery("#pro-img").remove();data_top_link=jQuery(result).find('#mini-cart').html();parent.jQuery('#mini-cart').html(data_top_link);data_top_cart=jQuery(result).find('div#ajax_topcart');parent.jQuery("#ajax_topcart").replaceWith(data_top_cart);parent.region_id();data_cart_content=jQuery(result).find('div.cart').html();parent.jQuery('div.cart').html(data_cart_content);parent.region_updater();parent.truncted_details();parent.displayTopCart();parent.fancybox_update();parent.jQuery.fancybox.close();}});}});}
function discountCoupon(coupon_form_url,isremove)
{if(isremove=='1')
{$('coupon_code').removeClassName('required-entry');$('remove-coupone').value="1";}else{$('coupon_code').addClassName('required-entry');$('remove-coupone').value="0";}
showcartloading();jQuery.ajax({url:coupon_form_url,type:'POST',data:jQuery('form').serialize(),success:function(data)
{var result=data;$('region_id').setAttribute('defaultValue',estimateRegionId);data_total_box=jQuery(result).find('div.cart').html();jQuery('div.cart').html(data_total_box);new RegionUpdater('country','region','region_id',region_json);fancybox_update();}});}
function setAjaxData(data,iframe)
{if(data.status=='ERROR'){jQuery('.popup-text').html(data.message);jQuery.fancybox({'content':jQuery("#addtocart_popup").html(),'padding':20,});}else{jQuery.ajax({url:url_topcart,type:"POST",data:{},success:function(data)
{jQuery('#mini-cart').html(data);truncted_details();displayTopCart();}});jQuery('.popup-text').html(data.message);jQuery.fancybox({'content':jQuery("#addtocart_popup").html(),'padding':20,'minWidth':300,});}}

if(load_product_addto_cart_portion)
{

        var productAddToCartForm = new VarienForm('product_addtocart_form');
    	productAddToCartForm.submit = function(button, url) {
		if (this.validator.validate()) {

			var form = this.form;
			var oldUrl = form.action;
			if (url) {
				form.action = url;
			}
			var e = null;
			// Start of our new ajax code
			if (!url) {
				url = jQuery('#product_addtocart_form').attr('action');
			}
			url = url.replace("checkout/cart","ajax/cart"); // New Code
			var data = jQuery('#product_addtocart_form').serialize();
			data += '&isAjax=1';
            showviewloading();
			try {
				jQuery.ajax( {
					url : url,
					dataType : 'json',
					type : 'post',
					data : data,
					success : function(data) {
					    jQuery("#pro-img").remove();
                        jQuery("#pro-loading").remove();
						if(data.status == 'ERROR'){
							alert(data.message);
						}else{
    	                      /*  if(jQuery('.block-cart')){
    	                            jQuery('.block-cart').replaceWith(data.sidebar);
    	                        }
    	                        if(jQuery('#mini-cart')){
    	                            jQuery('#mini-cart').replaceWith(data.toplink);
    	                        }*/

                           var itemid = data.item_id;

                           if(itemid)
                           {
                           var new_url = jQuery('#product_addtocart_form').attr('action');
                           var new_url_length = new_url.indexOf("id/")+3;
                           var last_string = new_url.substr(0,new_url_length)+''+itemid+'/';
                           jQuery("#product_addtocart_form").attr('action',last_string);
                           }

                                jQuery.ajax({
                        	                url: url_topcart,
                        					type:"POST",
                        					data:{},
                        					success: function(data)
                                              {
                            				      var returndata = data;
                                                  jQuery('#mini-cart').html(returndata);
                                                  truncted_details();
                                                  displayTopCart();
                          					   }
                        				    });
                                jQuery('.popup-text').html(data.message);
                                jQuery.fancybox({
    							            'content' : jQuery("#addtocart_popup").html(),
    										'padding' : 20,
                                            'minWidth': 300,
    				                    });

						    }

					}
				});
			} catch (e) {
			}
			// End of our new ajax code
			this.form.action = oldUrl;
			if (e) {
				throw e;
			}
		}
	}.bind(productAddToCartForm);
    productAddToCartForm.submitLight = function(button, url){
            if(this.validator) {
                var nv = Validation.methods;
                delete Validation.methods['required-entry'];
                delete Validation.methods['validate-one-required'];
                delete Validation.methods['validate-one-required-by-name'];
                if (this.validator.validate()) {
                    if (url) {
                        this.form.action = url;
                    }
                    this.form.submit();
                }
                Object.extend(Validation.methods, nv);
            }
        }.bind(productAddToCartForm);


     function truncted_details()
       {
          $$('.truncated').each(function(element){
              Event.observe(element, 'mouseover', function(){
              if (element.down('div.truncated_full_value')) {
              element.down('div.truncated_full_value').addClassName('show')
              }
              });
              Event.observe(element, 'mouseout', function(){
              if (element.down('div.truncated_full_value')) {
              element.down('div.truncated_full_value').removeClassName('show')
              }
              });
          });
       }


}