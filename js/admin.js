jQuery(document).ready(function($) {
    
	$('#st-map #steps a').click(function(){
	  switch_tabs($(this));
    }); 
    var cof = 'st_';
    switch_tabs($('#st-map .defaulttab'));
    function switch_tabs(obj) {
    	$('#st-map .fieldset > .tabs-panel').hide();
    	$('#st-map #steps a').removeClass("selected");
        $('#st-map #steps li').removeClass("current");
    	var id = obj.attr("rel");
     
    	$('#'+id).show();
    	obj.addClass("selected");
        obj.parent('li.sel').addClass("current"); 
    }
    $('#st-map .commutator').on('click', function() {
        if($(this).hasClass('off')) {
            $(this).addClass('on').removeClass('off');
            $(this).prev().val('true');
           $(this).next().slideDown(250);
        } else {
           $(this).addClass('off').removeClass('on');
           $(this).prev().val('false');
           $(this).next().slideUp(250);
        }
    });
    $('#st-map .sts-code').on('click', function() {
        if($(this).hasClass('sts-load')) {
            $(this).addClass('sts-save').removeClass('sts-load');
            $(this).next().slideDown(350);
            $(this).val('Save');
            $go = $(this);
            $.ajax({
                type : 'POST',
                data : {'action' : 'getOptions', filename: $(this).prev().val(), control: 'load-code'},
                url : map_ajax.url,
                success : function (resp){
                    $go.next().val(resp);
                }
            });
        }
        $('#st-map .sts-save').click(function() {
            $go = $(this);                        
            $.ajax({
                url: map_ajax.url,
                type: 'POST',
                data: {'action' : "getOptions", filename: $(this).prev().val(), content: $(this).next().val(), edit: $('.st-checkbox').val(), control: 'save-code' },
                success : function (resp){
                    alert('Save the file successfully !');
                    $go.next().val(resp);
                }  
            });
        });
    });
    jQuery('#upload_image_button').click(function() {
        formfield = jQuery('#upload_image').attr('name');
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });
    window.send_to_editor = function(html) {
        imgurl = jQuery('img',html).attr('src');
        jQuery('#upload_image').val(imgurl);
        $('#upload_image').val(imgurl);
        $(".show-avatar .img").attr("src", imgurl);
        tb_remove();
    }
    /*
	 * Load RSS
	 */
	$(window).load(function() {
  		var feedURL = 'http://beautiful-templates.com/evo/category/products/feed/';
    	$.ajax({
	        type: "GET",
	        url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=1000&callback=?&q=' + encodeURIComponent(feedURL),
	        dataType: 'json',
	        success: function(xml){
	            var item = xml.responseData.feed.entries;
	            
	            var html = "<ul>";
	            $.each(item, function(i, value){
	            	html+= '<li><a href="'+value.link+'">'+value.title+'</a></li>';
	            	if (i===9){
	            		return false;
	            	}
	            });
	             html+= "</ul>";
	             $('.st_load_rss').html(html);
	        }
	        
	    });
    });
    $("#st-map .sts-setview-popup").live('click', function(){
        var getlatlng = $('#st-map-location').val();
        if(getlatlng !== '') {
            $(this).next().fadeIn("fast");
            $('#st-map #setlatlng').val(getlatlng);
            $('#st-map .empty').val('');
            $('#st-map #zoom').val('17');
            $('#st-map #maxzoom').val('20');
            $("#st-map #description").val("");
            $("#st-map #scrollwz").val("true");
            $("#st-map #scrollwz").next().removeClass('off').removeClass('on');
            $("#st-map #scrollwz").next().addClass("on");
            $("input[name='reload-button']").remove();
            $('#st-map .sts-saveview-popup .box-button').append('<input class="sts-button save reload-button" name="reload-button" type="button" id="sts-set-latlng" value="Save">');  
        } else {
            alert('No data !');
        }	
    });
    $("#st-map .sts-form-close").click(function(){
    	$("#st-map .sts-saveview-popup-wrap").fadeOut("fast");	
    });
    $("#sts-set-latlng").live('click', function(){
		ajax("save");
	});
    $('.latlng_del').live('click', function() {
        if(confirm("Do you really want to delete this record ?")){
            ajax("delete", $(this).prev().val());
        }
    });
    $('#st-map #update_latlng').live('click', function() {
        ajax("update", $(this).prev().val());    
    });
    $('#st-map .latlng_edit').live('click', function() {
       var id = $(this).next().val();
       var getL = $('#'+id+'_latlng').val();
       var getN = $('#'+id+'_name').val();
       var getT = $('#'+id+'_popup_title').val();
       var getD = $('#'+id+'_description').val();
       var getA = $('#'+id+'_avatar').val();
       var getZ = $('#'+id+'_zoom').val();
       var getMz = $('#'+id+'_maxzoom').val();
       var getS = $('#'+id+'_scrollwz').val();
       if(id !== '') {
            $("#st-map .sts-saveview-popup-wrap").fadeIn("fast");
            $('#st-map #setlatlng').val(getL);
            $('#st-map #latlng-name').val(getN);
            $('#st-map #popup_title').val(getT);
            $('#st-map #description').val(getD);
            $('#st-map #description').val(getD);
            $('#st-map #upload_image').val(getA);
            $('#st-map .img.val').attr('src', getA);
            $('#st-map #zoom').val(getZ);
            $('#st-map #maxzoom').val(getMz);
            $('#st-map #scrollwz').val(getS);
            $('#st-map #scrollwz').next().removeClass('off').removeClass('on');
            if($('#st-map #scrollwz').val(getS) == "true") {
                $('#st-map #scrollwz').next().addClass('on');
            } else {
                $('#st-map #scrollwz').next().addClass('off');
            }
            $("input[name='reload-button']").remove();
            $('#st-map .sts-saveview-popup .box-button').append('<input class="sts-button save" name="reload-button" type="button" id="update_latlng" value="Update">');
            $('#lgetID').val(id);	
       }
    });
    function ajax( action, del_id ) {
        if(action == 'save') {
            data = {'action' : "getOptions", latlng_id: cof+Math.random().toString(36).slice(2,8), latlng: $('.sts-saveview-popup-wrap #setlatlng').val(), latlng_name: $('.sts-saveview-popup-wrap #latlng-name').val(), popup_title: $('.sts-saveview-popup-wrap #popup_title').val(), description: $('.sts-saveview-popup-wrap #description').val(), avatar: $('.sts-saveview-popup-wrap #upload_image').val(), zoom: $('.sts-saveview-popup-wrap #zoom').val(), maxzoom: $('.sts-saveview-popup-wrap #maxzoom').val(), scrollwz: $('.sts-saveview-popup-wrap #scrollwz').val(), control: 'set-latlng' };
        }else if ( action == 'delete' ){
            data = {'action' : "getOptions", id: del_id, control: 'latlng-del' };
        }else if ( action == 'update' ){
            data = {'action' : "getOptions", id: del_id, latlng: $('.sts-saveview-popup-wrap #setlatlng').val(), latlng_name: $('.sts-saveview-popup-wrap #latlng-name').val(), popup_title: $('.sts-saveview-popup-wrap #popup_title').val(), description: $('.sts-saveview-popup-wrap #description').val(), avatar: $('.sts-saveview-popup-wrap #upload_image').val(), zoom: $('.sts-saveview-popup-wrap #zoom').val(), maxzoom: $('.sts-saveview-popup-wrap #maxzoom').val(), scrollwz: $('.sts-saveview-popup-wrap #scrollwz').val(), control: 'update_latlng' };
        }
        $.ajax({
            url: map_ajax.url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success : function (resp){
                //console.log(resp);
                if(action == 'save') {
                    $("#st-map .sts-saveview-popup-wrap").fadeOut("fast", function() {
                        location.reload();
                    });
                    $("#st-map .val").each(function(){
						$(this).val("");
                        $("#description").val("");
                        $('#st-map .img').attr('src', resp.no_avatar);
					});	
                }else if(action == 'delete') {
                    $("a[id='"+resp.row_id+"']").closest("tr").fadeOut();
                } else if(action == 'update') {
                    if(resp.key == 1) {
                        $("#st-map .sts-saveview-popup-wrap").fadeOut("fast", function() {
                            location.reload();
                        });
                    }
                }
            } 
        });  
    }
    $('#st-map .sts-show-list-view').on('click', function() {
        if($(this).hasClass('show-latlng')) {
            $(this).addClass('hide-latlng').removeClass('show-latlng');
            $(this).next().slideUp(350);
            $(this).val('Show List Map');
        } else {
           $(this).addClass('show-latlng').removeClass('hide-latlng');
           $(this).next().slideDown(350);
           $(this).val('Hide List Map')
        }
    });
    $('#st-map .sts-saveview-popup-wrap #tabs a').click(function(){
	  switch_tab($(this));
    });
    switch_tab($('#st-map #tabs .default'));
    function switch_tab(obj) {
    	$('#st-map .sts-saveview-popup > .tabs-panel').hide();
    	$('#st-map #tabs a').removeClass("selected");
        $('#st-map #tabs li').removeClass("current");
    	var id = obj.attr("rel");
     
    	$('#'+id).show();
    	obj.addClass("selected");
        obj.parent('li.setcol').addClass("current"); 
    }
});