/* (C) Copyright Bobbing Wide 2016
 * 
 * This code contains logic to handle the responsive menu 
 * and logic to cater for touch screens

 * 
 */
jQuery(function( $ ){

	if( $( document ).scrollTop() > 0 ){
		$( '.site-header' ).addClass( 'dark' );			
	}

	// Add opacity class to site header
	$( document ).on('scroll', function(){

		if ( $( document ).scrollTop() > 0 ){
			$( '.site-header' ).addClass( 'dark' );			

		} else {
			$( '.site-header' ).removeClass( 'dark' );			
		}

	});


	$( '.nav-header .genesis-nav-menu, .nav-secondary .genesis-nav-menu' ).addClass( 'responsive-menu' ).before('<div class="responsive-menu-icon"></div>');

	$( '.responsive-menu-icon' ).click(function(){
		$(this).next( '.nav-header .genesis-nav-menu,  .nav-secondary .genesis-nav-menu' ).slideToggle();
	});

	$( window ).resize(function(){
		if ( window.innerWidth > 480 ) {
			$( '.nav-header .genesis-nav-menu,  .nav-secondary .genesis-nav-menu, nav .sub-menu' ).removeAttr( 'style' );
			$( '.responsive-menu > .menu-item' ).removeClass( 'menu-open' );
		}
	});

	$( '.responsive-menu > .menu-item' ).click(function(event){
		if ( event.target !== this )
		return;
			$(this).find( '.sub-menu:first' ).slideToggle(function() {
			$(this).parent().toggleClass( 'menu-open' );
		});
	});

  //taphover - a solution to the lack of hover on touch devices.
	//more info: http://www.hnldesign.nl/work/code/mouseover-hover-on-touch-devices-using-jquery/
	$('li.menu-item-has-children').on('touchstart', function (e) {
			'use strict'; //satisfy the code inspectors
			var link = $(this); //preselect the link

			if ( window.innerWidth <= 480 ) {
				return true;
			}
			if (link.hasClass('hover')) {
					return true;
			} else {
					link.addClass('hover');
					$('li.menu-item-has-children').not(this).removeClass('hover');
					e.preventDefault();
					return false; //extra, and to make sure the function has consistent return points
		 }
	});



});

