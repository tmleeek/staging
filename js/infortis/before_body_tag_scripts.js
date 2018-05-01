if(gc_equal_height==1)
{var gridItemsEqualHeightApplied=false;function setGridItemsEqualHeight($)
{var $list=$('.category-products-grid');var $listItems=$list.children();var centered=$list.hasClass('centered');var gridItemMaxHeight=0;$listItems.each(function(){$(this).css("height","auto");var $object=$(this).find('.actions');if(centered)
{var objectWidth=$object.width();var availableWidth=$(this).width();var space=availableWidth-objectWidth;var leftOffset=space/2;$object.css("padding-left",leftOffset+"px");}
var bottomOffset=parseInt($(this).css("padding-top"));if(centered)
bottomOffset+=10;$object.css("bottom",bottomOffset+"px");if($object.is(":visible"))
{var objectHeight=$object.height();$(this).css("padding-bottom",(objectHeight+bottomOffset)+"px");}
gridItemMaxHeight=Math.max(gridItemMaxHeight,$(this).height());});$listItems.css("height",gridItemMaxHeight+"px");gridItemsEqualHeightApplied=true;}}
jQuery(function($){$('.collapsible').each(function(index){$(this).prepend('<span class="opener">&nbsp;</span>');if($(this).hasClass('active'))
{$(this).children('.block-content').css('display','block');}
else
{$(this).children('.block-content').css('display','none');}});$('.collapsible .opener').click(function(){var parent=$(this).parent();if(parent.hasClass('active'))
{$(this).siblings('.block-content').stop(true).slideUp(300,"easeOutCubic");parent.removeClass('active');}
else
{$(this).siblings('.block-content').stop(true).slideDown(300,"easeOutCubic");parent.addClass('active');}});var ddOpenTimeout;var dMenuPosTimeout;var DD_DELAY_IN=0;var DD_DELAY_OUT=0;var DD_ANIMATION_IN=500;var DD_ANIMATION_OUT=0;$(".clickable-dropdown > .dropdown-toggle").click(function(){$(this).parent().addClass('open');$(this).parent().trigger('mouseenter');});$("#quick-compare").hover(function(){var ddToggle=$(this).children('.dropdown-toggle');var ddMenu=$(this).children('.dropdown-menu');var ddWrapper=ddMenu.parent();ddMenu.css("left","");ddMenu.css("right","");if($(this).hasClass('clickable-dropdown'))
{if($(this).hasClass('open'))
{$(this).children('.dropdown-menu').stop(true,true).delay(DD_DELAY_IN).fadeIn(DD_ANIMATION_IN,"easeOutCubic");}}
else
{clearTimeout(ddOpenTimeout);ddOpenTimeout=setTimeout(function(){ddWrapper.addClass('open');},DD_DELAY_IN);$(this).children('.dropdown-menu').stop(true,true).delay(DD_DELAY_IN).fadeIn(DD_ANIMATION_IN,"easeOutCubic");}
clearTimeout(dMenuPosTimeout);dMenuPosTimeout=setTimeout(function(){if(ddMenu.offset().left<0)
{var space=ddWrapper.offset().left;ddMenu.css("left",(-1)*space);ddMenu.css("right","auto");}},DD_DELAY_IN);},function(){var ddMenu=$(this).children('.dropdown-menu');clearTimeout(ddOpenTimeout);ddMenu.stop(true,true).delay(DD_DELAY_OUT).fadeOut(DD_ANIMATION_OUT,"easeInCubic");if(ddMenu.is(":hidden"))
{ddMenu.hide();}
$(this).removeClass('open');});$("#mini-cart").hover(function(){var ddToggle=$(this).children('.dropdown-toggle');var ddMenu=$(this).children('.dropdown-menu');var ddWrapper=ddMenu.parent();ddMenu.css("left","");ddMenu.css("right","");if($(this).hasClass('clickable-dropdown'))
{if($(this).hasClass('open'))
{$(this).children('.dropdown-menu').stop(true,true).delay(DD_DELAY_IN).fadeIn(DD_ANIMATION_IN,"easeOutCubic");}}
else
{clearTimeout(ddOpenTimeout);ddOpenTimeout=setTimeout(function(){ddWrapper.addClass('open');},DD_DELAY_IN);$(this).children('.dropdown-menu').stop(true,true).delay(DD_DELAY_IN).fadeIn(DD_ANIMATION_IN,"easeOutCubic");}
clearTimeout(dMenuPosTimeout);dMenuPosTimeout=setTimeout(function(){if(ddMenu.offset().left<0)
{var space=ddWrapper.offset().left;ddMenu.css("left",(-1)*space);ddMenu.css("right","auto");}},DD_DELAY_IN);},function(){var ddMenu=$(this).children('.dropdown-menu');clearTimeout(ddOpenTimeout);ddMenu.stop(true,true).delay(DD_DELAY_OUT).fadeOut(DD_ANIMATION_OUT,"easeInCubic");if(ddMenu.is(":hidden"))
{ddMenu.hide();}
$(this).removeClass('open');});$(".dropdownlogin").hover(function(){var ddToggle=$(this).children('.dropdown-toggle');var ddMenu=$(this).children('.dropdown-menu');var ddWrapper=ddMenu.parent();ddMenu.css("left","");ddMenu.css("right","");if($(this).hasClass('clickable-dropdown'))
{if($(this).hasClass('open'))
{$(this).children('.dropdown-menu').stop(true,true).delay(DD_DELAY_IN).fadeIn(DD_ANIMATION_IN,"easeOutCubic");}}
else
{clearTimeout(ddOpenTimeout);ddOpenTimeout=setTimeout(function(){ddWrapper.addClass('open');});$(this).children('.dropdown-menu').fadeIn(DD_ANIMATION_IN,"easeOutCubic");}
clearTimeout(dMenuPosTimeout);dMenuPosTimeout=setTimeout(function(){if(ddMenu.offset().left<0)
{var space=ddWrapper.offset().left;ddMenu.css("left",(-1)*space);ddMenu.css("right","auto");}},DD_DELAY_IN);},function(){var ddMenu=$(this).children('.dropdown-menu');clearTimeout(ddOpenTimeout);$('#name').click(function(){var ddMenu=$(this).children('.dropdown-menu');var ddWrapper=ddMenu.parent();ddWrapper.addClass('open');$(this).children('.dropdown-menu').fadeIn(DD_ANIMATION_IN,"easeOutCubic");});$('#pswd').click(function(){var ddMenu=$(this).children('.dropdown-menu');var ddWrapper=ddMenu.parent();ddWrapper.addClass('open');$(this).children('.dropdown-menu').fadeIn(DD_ANIMATION_IN,"easeOutCubic");});if($("#name").is(":focus")||$("#pswd").is(":focus"))
{var ddMenu=$(this).children('.dropdown-menu');var ddWrapper=ddMenu.parent();ddWrapper.addClass('open');$(this).children('.dropdown-menu').fadeIn(DD_ANIMATION_IN,"easeOutCubic");$('body').click(function(e){if($(e.target).is('#name')||$(e.target).is('#pswd'))
{var ddMenu=$(this).children('.dropdown-menu');var ddWrapper=ddMenu.parent();ddWrapper.addClass('open');$(this).children('.dropdown-menu').fadeIn(DD_ANIMATION_IN,"easeOutCubic");}
else
{$('name').blur();$('#pswd').blur();$('#focus').css('display','none');$('#mini-login').removeClass('open');}});}
else
{var ddMenu=$(this).children('.dropdown-menu');ddMenu.fadeOut(0,"easeInCubic");if(ddMenu.is(":hidden"))
{ddMenu.hide();}
$(this).removeClass('open');}});$(".dropdown").hover(function(){var ddToggle=$(this).children('.dropdown-toggle');var ddMenu=$(this).children('.dropdown-menu');var ddWrapper=ddMenu.parent();ddMenu.css("left","");ddMenu.css("right","");if($(this).hasClass('clickable-dropdown'))
{if($(this).hasClass('open'))
{$(this).children('.dropdown-menu').stop(true,true).delay(DD_DELAY_IN).fadeIn(DD_ANIMATION_IN,"easeOutCubic");}}
else
{clearTimeout(ddOpenTimeout);ddOpenTimeout=setTimeout(function(){ddWrapper.addClass('open');},DD_DELAY_IN);$(this).children('.dropdown-menu').stop(true,true).delay(DD_DELAY_IN).fadeIn(DD_ANIMATION_IN,"easeOutCubic");}
clearTimeout(dMenuPosTimeout);dMenuPosTimeout=setTimeout(function(){if(ddMenu.offset().left<0)
{var space=ddWrapper.offset().left;ddMenu.css("left",(-1)*space);ddMenu.css("right","auto");}},DD_DELAY_IN);},function(){var ddMenu=$(this).children('.dropdown-menu');clearTimeout(ddOpenTimeout);ddMenu.stop(true,true).delay(DD_DELAY_OUT).fadeOut(DD_ANIMATION_OUT,"easeInCubic");if(ddMenu.is(":hidden"))
{ddMenu.hide();}
$(this).removeClass('open');});if(theme_true==1)
{$(".main").addClass("show-bg");}
var windowScroll_t;$(window).scroll(function(){clearTimeout(windowScroll_t);windowScroll_t=setTimeout(function(){if($(this).scrollTop()>100)
{$('#scroll-to-top').fadeIn();}
else
{$('#scroll-to-top').fadeOut();}},500);});$('#scroll-to-top').click(function(){$("html, body").animate({scrollTop:0},600,"easeOutCubic");return false;});if(gc_hover_effect==1)
{var startHeight;var bpad;$('.category-products-grid').on('mouseenter','.item',function(){if(gc_disable_hover_effect<1||(gc_disable_hover_effect>0&&$(window).width()>=gc_disable_hover_effect))
{if(gc_equal_height==1)
{if(gridItemsEqualHeightApplied===false)
{return false;}}
startHeight=$(this).height();$(this).css("height","auto");$(this).find(".display-onhover").fadeIn(400,"easeOutCubic");var h2=$(this).height();var addtocartHeight=0;var addtolinksHeight=0;if(gc_display_addtocart==1)
{var buttonOrStock=$(this).find('.btn-cart');if(buttonOrStock.length==0)
buttonOrStock=$(this).find('.availability');addtocartHeight=buttonOrStock.height();}
if(gc_display_addtolinks)
{var addtolinksEl=$(this).find('.add-to-links');if(addtolinksEl.hasClass("addto-onimage")==false)
addtolinksHeight=addtolinksEl.innerHeight();}
if(gc_equal_height==1&&(gc_display_addtocart==1||gc_display_addtolinks==1))
{var h3=h2+addtocartHeight+addtolinksHeight;var diff=0;if(h3<startHeight)
{$(this).height(startHeight);}
else
{$(this).height(h3);diff=h3-startHeight;}}
else
{var diff=0;if(h2<startHeight)
{$(this).height(startHeight);}
else
{$(this).height(h2);diff=h2-startHeight;}}
$(this).css("margin-bottom","-"+diff+"px");}}).on('mouseleave','.item',function(){if(gc_disable_hover_effect<1||(gc_disable_hover_effect>0&&$(window).width()>=gc_disable_hover_effect))
{$(this).find(".display-onhover").stop(true).hide();$(this).css("margin-bottom","");if(gc_equal_height==1)
{$(this).height(startHeight);}
else
{$(this).css("height","");}}});}
else
{$('.category-products-grid').on('mouseenter','.item',function(){$(this).find(".display-onhover").fadeIn(400,"easeOutCubic");}).on('mouseleave','.item',function(){$(this).find(".display-onhover").stop(true).hide();});}
$('.products-grid, .products-list').on('mouseenter','.item',function(){$(this).find(".alt-img").fadeIn(400,"easeOutCubic");}).on('mouseleave','.item',function(){$(this).find(".alt-img").stop(true).fadeOut(400,"easeOutCubic");});$('.fade-on-hover').on('mouseenter',function(){$(this).animate({opacity:0.75},300,'easeInOutCubic');}).on('mouseleave',function(){$(this).stop(true).animate({opacity:1},300,'easeInOutCubic');});var winWidth=$(window).width();var winHeight=$(window).height();$(window).resize($.debounce(50,onEventResize));function onEventResize(){var winNewWidth=$(window).width();var winNewHeight=$(window).height();if(winWidth!=winNewWidth||winHeight!=winNewHeight)
{afterResize();}
winWidth=winNewWidth;winHeight=winNewHeight;}
function afterResize(){$(document).trigger("themeResize");if(gc_equal_height==1)
{setGridItemsEqualHeight($);}
$('.itemslider').each(function(index){var flex=$(this).data('flexslider');if(flex!=null)
{flex.flexAnimate(0);flex.resize();}});var slideshow=$('.the-slideshow').data('flexslider');if(slideshow!=null)
{slideshow.resize();}}});jQuery(window).load(function(){if(gc_equal_height==1)
{setGridItemsEqualHeight(jQuery);}});