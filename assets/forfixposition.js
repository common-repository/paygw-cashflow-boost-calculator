

<script>
(function($){


	$(document).ready(function(){

	    $(window).scroll(() => { 
		  // Distance from top of document to top of footer.
		  topOfFooter = $('.footer-wrapper').position().top;
		  // Distance user has scrolled from top, adjusted to take in height of sidebar (570 pixels inc. padding).
		  scrollDistanceFromTopOfDoc = $(document).scrollTop() + 570;
		  // Difference between the two.
		  scrollDistanceFromTopOfFooter = scrollDistanceFromTopOfDoc - topOfFooter;

		  // If user has scrolled further than footer,
		  // pull sidebar up using a negative margin.
		  if (scrollDistanceFromTopOfDoc > topOfFooter) {
		    $('#sidebar-fixed').css('margin-top',  0 - scrollDistanceFromTopOfFooter);
		  } else  {
		    $('#sidebar-fixed').css('margin-top', 0);
		  }
		});
	});


})(jQuery);
</script>



<script>
(function($){


$(document).ready(function(){

    $(window).on('scroll resize', function() {
	    var width = window.innerWidth;

	    $('#sidebar-fixed').css({position: '', right: '', top: ''});
	      console.log('window width', width);
	      console.log('window page y offset', window.pageYOffset );
	      var pageYOffset = 11629;
	      if(width <= 1366) {
	        pageYOffset = 13700;
	      }
	      console.log(pageYOffset);
	    if(width > 1024 && window.pageYOffset <= pageYOffset && window.pageYOffset >= 500) {
	    var maxWidth = '241.797px';

	    if(width <= 1200){
	    maxWidth = '200px';
	    }
	        $('#sidebar-fixed').css({position: 'fixed', maxWidth: maxWidth, top: '50px'});
	    } 
	    });
	});


})(jQuery);
</script>
