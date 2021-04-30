/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';

    config.language = 'en';
	
    // Uncomment the following line if your users tend to frequently paste in contents from, say, MS-Word.
    // config.forcePasteAsPlainText = true;

    // Uncomment the following block to make CKEditor format self-closing tags the old way e.g. as <br> and <img> instead of <br/> and <img/>
    /*
    CKEDITOR.on( 'instanceReady', function( ev )
    {
        // Ends self closing tags the HTML4 way, like <br/>.
        ev.editor.dataProcessor.writer.selfClosingEnd = '>';
    });
    */
	
	// If you want inserted images in a CKEditor to be responsive
	// you can use the following code. It creates a htmlfilter for the
	// image tag that replaces inline "width" and "style" definitions with
	// their corresponding attributes and add's (in this example) the
	// Bootstrap "img-responsive" class.
	CKEDITOR.on('instanceReady', function (ev) {
		ev.editor.dataProcessor.htmlFilter.addRules( {
			elements : {
				img: function( el ) {
					// Add bootstrap "img-responsive" class to each inserted image
					el.addClass('img-responsive');
			
					// Remove inline "height" and "width" styles and
					// replace them with their attribute counterparts.
					// This ensures that the 'img-responsive' class works
					var style = el.attributes.style;
			
					if (style) {
						// Get the width from the style.
						var match = /(?:^|\s)width\s*:\s*(\d+)px/i.exec(style),
							width = match && match[1];
			
						// Get the height from the style.
						match = /(?:^|\s)height\s*:\s*(\d+)px/i.exec(style);
						var height = match && match[1];
			
						// Replace the width
						if (width) {
							el.attributes.style = el.attributes.style.replace(/(?:^|\s)width\s*:\s*(\d+)px;?/i, '');
							el.attributes.width = width;
						}
			
						// Replace the height
						if (height) {
							el.attributes.style = el.attributes.style.replace(/(?:^|\s)height\s*:\s*(\d+)px;?/i, '');
							el.attributes.height = "auto";
						}
					}
			
					// Remove the style tag if it is empty
					if (!el.attributes.style)
						delete el.attributes.style;
				}
			}
		});
	});
};

