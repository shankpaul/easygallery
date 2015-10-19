$(document).ready(function(e) {
   
	jQuery(".swipebox").swipebox();
	if(jQuery("a.fancybox").length >0){
	jQuery("a.fancybox").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'titlePosition'	: 'inside'
	});
}

});


