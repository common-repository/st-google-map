(function() {
	'use strict';
	
	tinymce.create('tinymce.plugins.st_mapWP_Shortcode', {
		init: function(editor, url) {
            var shortcodeValues = [];
            jQuery.each(list_latlng, function(i) {
                shortcodeValues.push({text: list_latlng[i], value:list_latlng_id[i]});
            });
			editor.addButton('st_mapWP_Shortcode', {
				title: 'ST Map',
				cmd: 'map',
				image: url + '/../images/map-icon.png'
			});
			
			editor.addCommand('map', function() {
				editor.windowManager.open({
                        title: 'ST Map Setting',
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
							type: 'container',
							html: 'Exp: 500px, 350px,...'
						},
                        {
    		            	type: 'listbox', 
    		            	name: 'latlng', 
    		            	label: 'Select Map: ',
                            multiple: true,                                                        
                            minWidth: 75,
                            onselect : function(v) {
                                tinyMCE.activeEditor.windowManager.alert('Value selected:' + v);
                            },
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
                            if (e.data.latlng) {
                				shortcode += ' latlng="' + e.data.latlng;
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