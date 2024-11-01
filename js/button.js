(function() {
	'use strict';
	
	tinymce.create('tinymce.plugins.st_mapWP_Shortcode', {
		init: function(editor, url) {
			editor.addButton('st_mapWP_Shortcode', {
				title: 'ST Google Map',
				cmd: 'map',
				image: url + '/../images/map-icon.png'
			});
			var shortcodeValues = [];
            jQuery.each(st_map_name, function(i)
            {
                shortcodeValues.push({text: st_map_name[i], value:st_map_id[i]});
            });
			editor.addCommand('map', function() {
				editor.windowManager.open({
                        title: 'ST Google Map Setting',
                        body: [
                        {
							type: 'checkbox',
							name: 'fullwidth',
							label: 'Set FullWidth',
							checked: true
						},
                        {
                            type: 'textbox',
                			name: 'width',
                			label: 'Width',
                			minWidth: 200
                        },
                        {
                            type: 'textbox',
                			name: 'height',
                			label: 'Height',
                			minWidth: 200
                        },
                        {
    		            	type: 'listbox', 
    		            	name: 'listmap', 
    		            	label: 'Select Map: ', 
    		            	'values': shortcodeValues
    		        	}
                        ],
                        onsubmit: function( e ) {
                          
                                var shortcode = '[ST-mapWP';
                            
                    			if (e.data.fullwidth) {
                    				shortcode += ' fullwidth="' + e.data.fullwidth;
                                    shortcode += '"';
                    			}		
                    			if (e.data.width && e.data.fullwidth !== true) {
                    				shortcode += ' width="' + e.data.width;
                                    shortcode += '"';
                    			}
                    			if (e.data.height) {
                    				shortcode += ' height="' + e.data.height;
                                    shortcode += '"';
                    			}
                                if (e.data.listmap) {
                    				shortcode += ' mapid="' + e.data.listmap;
                                    shortcode += '"';
                    			}
                                
                    			shortcode += editor.selection.getContent() + ']';
                    			
                    			editor.execCommand('mceInsertContent', 0, shortcode);   
                            
                        }
                    });
			});
		}
	});
	
	tinymce.PluginManager.add('st_mapWP_Shortcode', tinymce.plugins.st_mapWP_Shortcode);
}());