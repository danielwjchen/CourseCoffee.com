/**
 * Display a series of images in an animated slide show
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
window.SlideShow = function(regionName) {

	var _region = $(regionName);

	_region.addClass('loading');

 /**
  * Loop through the images
	*/
 window._loop = function() {
     
  //if no IMGs have the show class, grab the first image
	var current = ($('li.show', _region)?  $('li.show', _region) : $('li:first', _region));
 
    //Get next image, if it reached the end of the slideshow, rotate it back to the first image
	var next = ((current.next().length) ? ((current.next().hasClass('caption'))? $('li:first', _region) :current.next()) : $('li:first', _region));   
     
  //Get next image caption
  var caption = next.find('img').attr('rel'); 
     
  //Set the fade in effect for the next image, show class has higher z-index
  next.css({opacity: 0.0})
		.addClass('show')
    .animate({opacity: 1.0}, 1000);
 
    //Hide the current image
  current.animate({opacity: 0.0}, 1000)
		.removeClass('show');
     
    //Set the opacity to 0 and height to 1px
  $('.caption', _region).animate({opacity: 0.0}, { queue:false, duration:0}).animate({height: '1px'}, { queue:true, duration:300 }); 
     
    //Animate the caption, opacity to 0.7 and heigth to 100px, a slide up effect
  $('.caption', _region).animate({opacity: 0.7},100 ).animate({height: '100px'},500 );
     
    //Display the caption-content
  $('.caption-content', _region).html(caption);
         
	} 

	this.perform = function() {
 
    //Set the opacity of all images to 0
    $('li', _region).css({opacity: 0.0});
     
    //Get the first image and display it (set it to full opacity)
    $('li:first', _region).css({opacity: 1.0});
     
    //Set the caption background to semi-transparent
    $('.caption', _region).css({opacity: 0.7});
 
    //Resize the width of the caption according to the image width
    $('.caption', _region).css({width: $('li', _region).find('img').css('width')});
     
    //Get the caption of the first image from REL attribute and display it
    $('.caption-content', _region).html(
			$('li:first', _region).find('img').attr('rel')
		).animate({opacity: 1}, 400);
     
    // Call the gallery function to run the slideshow, 6000 = change to next
		// image after 2 seconds
    setInterval('_loop()', 6000);
	};
};
 
