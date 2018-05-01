$jq=jQuery.noConflict();
 // Check setLocation is add product to cart
	// issend Variable check request on send
	var toolbarsend	=	false;
	var toolbarBaseurl	='';
	var ajaxtoolbar	=	function(){
		function lockshowloading(){
			$jq(".category-products").css("position","relative");
			$jq(".category-products").append("<div style='position:absolute;top:0px;left:0px;right:0px;bottom:0px;height:100%;width:100%;background:white;margin:0px;-moz-opacity:.80;filter:alpha(opacity=80);opacity:0.8;z-index:888'></div>");
			img	=	"<div style='position:absolute;top:40%;left:50%;margin-left:-33px;z-index:889'><img src='"+toolbarBaseurl+"frontend/default/default/VS/images/ajaxloading.gif'/></div>";
			$jq(".category-products").append(img);
		}
		return {
			onReady:function(){
				setLocation=function(link){
					if(link.search("limit=")!=-1||link.search("mode=")!=-1||link.search("dir=")!=-1||link.search("order=")!=-1){
						if(toolbarsend==false){
							ajaxtoolbar.onSend(link,'get');
						
						}
					}else{
                        window.location.href=link;
                    }
                    
				};
				$jq('a').click(function(event) {
					link	=	$jq(this).attr('href');
					if((link.search("mode=")!=-1||link.search("dir=")!=-1||link.search("p=")!=-1)&&(toolbarsend==false)){
						event.preventDefault();
						ajaxtoolbar.onSend(link,'get');
					}
					
				});
				
			},//End onReady
			onSend:function(url,typemethod){
				new Ajax.Request(url,
					{parameters:{ajaxtoolbar:1},
					method:typemethod,
					onLoading:function(cp){
						toolbarsend=true;
						lockshowloading();
					},
					onComplete:function(cp){
						toolbarsend=false;
						if(200!=cp.status){
							return false;
						}else{
							// Get success	
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
