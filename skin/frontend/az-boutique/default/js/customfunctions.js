    $(document).ready(function() {
      $("#owl-demo").owlCarousel({
        autoPlay: 3000,
        items : 6,
        itemsDesktop : [1199,3],
        itemsDesktopSmall : [979,3],
		itemsDesktopSmall : [800,3],
		itemsTablet: [768,3],
		pagination: false


      });
	  
	    //---- Event Slider 
    jQuery('.event').each(function(){
		var owl = jQuery(this);
		owl.owlCarousel({
			items : 2,
			 itemsDesktop : [980,1],
			 itemsDesktopSmall : [979,1],
			 itemsTablet: [768,1],
			 itemsMobile : [480,1],
    		lazyLoad : true,
			pagination: false,
    		navigation : true

		});

	});
	  	 
		 
		    //---- Event Slider 
    jQuery('.featured ').each(function(){
		var owl = jQuery(this);
		owl.owlCarousel({
			items : 1,
			 itemsDesktop : [1199,1],
			 itemsDesktopSmall : [979,1],
			 itemsDesktopSmall : [800,1],
			 itemsTablet: [768,1],
			 itemsMobile : [479,1],
			 slideSpeed : 700,
    		lazyLoad : true,
			pagination: false,
    		navigation : true

		});

	});
	  	 
    });
 $(document).ready(function() {

      var time = 7; // time in seconds

      var $progressBar,
          $bar, 
          $elem, 
          isPause, 
          tick,
          percentTime;

        //Init the carousel
        $("#owl-demo2").owlCarousel({
          slideSpeed : 500,
          paginationSpeed : 500,
          singleItem : true,
          afterInit : progressBar,
          afterMove : moved,
          startDragging : pauseOnDragging,
		  pagination: true,
		  navigation : true,
		  transitionStyle : "fade"
        });

        //Init progressBar where elem is $("#owl-demo")
        function progressBar(elem){
          $elem = elem;
          //build progress bar elements
          buildProgressBar();
          //start counting
          start();
        }

        //create div#progressBar and div#bar then prepend to $("#owl-demo")
        function buildProgressBar(){
          $progressBar = $("<div>",{
            id:"progressBar"
          });
          $bar = $("<div>",{
            id:"bar"
          });
          $progressBar.append($bar).prependTo($elem);
        }

        function start() {
          //reset timer
          percentTime = 0;
          isPause = false;
          //run interval every 0.01 second
          tick = setInterval(interval, 10);
        };

        function interval() {
          if(isPause === false){
            percentTime += 1 / time;
            $bar.css({
               width: percentTime+"%"
             });
            //if percentTime is equal or greater than 100
            if(percentTime >= 100){
              //slide to next item 
              $elem.trigger('owl.next')
            }
          }
        }

        //pause while dragging 
        function pauseOnDragging(){
          isPause = true;
        }

        //moved callback
        function moved(){
          //clear interval
          clearTimeout(tick);
          //start again
          start();
        }

        //uncomment this to make pause on mouseover 
        // $elem.on('mouseover',function(){
        //   isPause = true;
        // })
        // $elem.on('mouseout',function(){
        //   isPause = false;
        // })
    });
	
	
	$(document).ready(function() {
     
	  $(".mob-navbar").click(function(){
		$(".mob-nav-content").slideToggle(); 
	   });
	  
	  $(".top-filter-content a").click(function(){
			var a = $(this).attr('rel');
			$('.popover-content-box').hide();
			$(a).show();
 			if($(a).hasClass('open')== true)
			{
				$(a).removeClass('open');
            	$(a).hide();
     		}else{
		    $(a).addClass('open');
			$(a).show();
    		 }
			var toggle_switch = $(this);
   			
		  });
		  
		 $("a.selector").click(function(){
			var a = $(this).attr('rel');
			$(a).slideToggle();
		  }); 
	  
	  
	  $(".user-popup").click(function(){
		  var b = $(this).attr('rel');
		  $(b).slideToggle();
	  });
	  
	  $(".search-alphabetically a").click(function(){
		  var c = $(this).attr('rel');
		  $(c).slideToggle();
	  });
	  
	  $(".search-alphabetically a").click(function(){
		  if($(this).hasClass('active')== true)
			{
            	$(this).removeClass('active');
     		}else{
		    $(this).addClass('active');
    		 }
	  });
	  
	  $('#tooltip').tooltip();
	  
	  

    });
	
// Gallery 	
	
$(window).load(function() {
  // The slider being synced must be initialized first
  $('#carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    itemWidth: 154,
    itemMargin: 5,
	asNavFor: '#slider'
  });
   
  $('#slider').flexslider({
    animation: "fade",
	animationSpeed: 2000,
    controlNav: false,
    directionNav: false,
    animationLoop: false,
    slideshow: false,
    sync: "#carousel"
  });
});