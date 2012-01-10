window.addEvent('domready', function() {
	// Text-Images
	$$( '.textimage' ).each( function( el ) {
		createTextImage( el );
  	} );

	function createTextImage( el ) {
		var elText = el.get( 'text' );
		el.empty();
		var tagname = el.tagName.toLowerCase();
		var classRegex = /textimage-([a-z0-9-]+)/;
		var classMatch = classRegex.exec( el.className );
		if ( classMatch ) tagname = classMatch[1];
		new Element( 'img', { 'src': THEME_IMAGE_DIR + '/dynamic/' + tagname + '/' + Base64.encode( elText ) + '.png', 'alt': elText } ).inject( el );
	}

