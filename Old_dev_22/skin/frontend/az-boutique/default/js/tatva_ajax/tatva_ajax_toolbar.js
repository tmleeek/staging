/* Start of Toolbar using ajax */

	var toolbarsend	=	false;
	var toolbarBaseurl	='';

	var ajaxtoolbar	=	function()
     {
		function showloading(){
			jQuery(".category-products").css("position","relative");
			jQuery(".category-products").append("<div style='position:absolute;top:0px;left:0px;right:0px;bottom:0px;height:100%;width:100%;background:white;margin:0px;-moz-opacity:.80;filter:alpha(opacity=80);opacity:0.8;z-index:888'></div>");
			img	= "<div style='position:absolute;top:40%;left:50%;margin-left:-33px;z-index:889'><img src='"+toolbarBaseurl+"'/></div>";
			jQuery(".category-products").append(img);
		}
	   return{
      		  onReady:function()
                 {
      				setLocation=function(link)
                       {
        				  if(link.search("limit=")!=-1||link.search("mode=")!=-1||link.search("dir=")!=-1||link.search("order=")!=-1)
                         {
        				    if(toolbarsend==false){  ajaxtoolbar.onSend(link,'get'); }
        				   }
                        else {  window.location.href=link; }
                       };
      				jQuery('a').click(function(event)
                       {
      					link	=	jQuery(this).attr('href');
      					if((link.search("mode=")!=-1||link.search("dir=")!=-1||link.search("p=")!=-1)&&(toolbarsend==false))
                           {
      						event.preventDefault();
      						ajaxtoolbar.onSend(link,'get');
      					 }
                       });

      			},//End onReady

  	         onSend:function(toolbar_url,typemethod)
                {
                  new Ajax.Request(toolbar_url,{parameters :{ajaxtoolbar:1},
                                                method:typemethod,
                            					onLoading:function(cp)
                                                  {
                                                    toolbarsend=true;
                                                    showloading();
                                                  },
                        					    onComplete:function(cp)
                                                  {
                                                    toolbarsend=false;
                                                    if(200!=cp.status)
                                                        {  return false;}
                                                    else{  // Get success
                                                           var list	=	cp.responseJSON;
                                                           $$(".category-products").invoke("replace",list.toolbarlistproduct);
                                                           ajaxtoolbar.onReady();
                                                    }
                                                    }

                        				         });
  		     	 }//End onSend
		       }
	 }();
Prototype.Browser.IE?Event.observe(window,"load",function(){ajaxtoolbar.onReady()}):document.observe("dom:loaded",function(){ajaxtoolbar.onReady()});


/* Layerd Navigation Filter Using Ajax */

var Ajaxfilter=Class.create();Ajaxfilter.prototype={initialize:function(){this.request=null,this.url=null,this.pop=!1,document.observe("dom:loaded",this.referencelinks.bind(this)),window.onpopstate=function(){this.pop=!0,this.referenceurl(location.href)}.bind(this)},referenceurl:function(a){this.showSpinner(!1),this.url=a,null!==this.request&&this.request.abort(),this.request=new Ajax.Request(a,{method:"get",onSuccess:this.updatedlist.bind(this),onComplete:this.referencelinks.bind(this)})},getfinalResult:function(a){setElement=Event.element(a),url="",setElement.value?url=setElement.value:setElement.href?url=setElement.href:(setElement=Event.findElement(a,"a"),url=setElement.href),this.referenceurl(url),a.stop()},referencelinks:function(){$$(".pages li a",".view-mode a",".sorter a").invoke("observe","click",this.getfinalResult.bind(this)),$$(".limiter select",".sorter select").invoke("removeAttribute","onchange"),$$(".limiter select",".sorter select").invoke("observe","change",this.getfinalResult.bind(this)),$$(".block-layered-nav a").invoke("observe","click",this.getfinalResult.bind(this))},updatedlist:function(a){jQuery(".page-title:first").remove(),jQuery(".addtocart_popup:first").remove(),final_response=a.responseText,"function"==typeof history.pushState&&(this.pop===!1?history.pushState({url:this.url},document.title,this.url):this.pop=!1);var b=new Element("div");b.update(final_response);var c=b.select("div#ajax-list-container")[0],d=b.select("div#ajax-nav-container")[0];$$(".category-products").each(function(a){Element.replace(a,c.innerHTML)}),$$(".block-layered-nav").each(function(a){Element.replace(a,d.innerHTML)})},showSpinner:function(){$$(".category-products").each(function(a){a.addClassName("page-loading")}),$$(".block-layered-nav").each(function(a){a.addClassName("page-loading")})}},Object.extend(Ajax),Ajax.Request.prototype.abort=function(){this.transport.onreadystatechange=Prototype.emptyFunction,this.transport.abort(),Ajax.activeRequestCount--};var ajaxPager=new Ajaxfilter;