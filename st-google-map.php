<?php
/*
Plugin Name: ST Google Map
Plugin URI: http://beautiful-templates.com
Description: Easy to create google map with simple shortcode
Version: 1.0.0
Author: Beautiful Templates
Author URI: http://beautiful-templates.com
License:  GPL2
*/
define('ST_MAP_ROOT',dirname(__FILE__).'/');
define('ST_MAP_CSS_URL',  trailingslashit(plugins_url('/css/', __FILE__) ));
define('ST_MAP_JS_URL',  trailingslashit(plugins_url('/js/', __FILE__) ));
define('ST_MAP_IMG_URL',  trailingslashit(plugins_url('/images/', __FILE__) ));
define('ST_MAP_POPUP_URL',  trailingslashit(plugins_url('/popup/', __FILE__) ));
class StMapWP {
    private $popup_url = ST_MAP_POPUP_URL;
    public $latlng;
    function __construct(){
		$this->actions();
        $this->shortcodes();
        $this->reg_act_hook();
        $this->latlng = get_option('st_latlng');
	}
    function actions() {
        // Active notice 
        $st_mapWP_notice = get_option( 'st_mapWP_admin_notice' );  
        if ( $st_mapWP_notice == 'TRUE' && is_admin() ) :
            add_action('admin_notices', array(&$this, 'st_mapWP_activation_notice'));
            if( get_option('st_latlng') != "" ) :
                delete_option('st_latlng');
            endif;
            update_option('st_mapWP_admin_notice','FALSE');   
        endif;
        if( get_option('st_latlng') != "" ) :
            add_action('admin_footer', array($this, 'get_shortcodes'));
            add_action( 'init', array(&$this, 'add_button'));
        endif;
        add_action( 'admin_menu', array(&$this, 'st_mapWP_register_admin_menu_page') );
        add_action( 'wp_enqueue_scripts', array(&$this, "add_scripts") );
		add_action( 'admin_enqueue_scripts', array(&$this, "add_scripts") );
        add_action( 'admin_print_scripts', array(&$this, "wp_gear_manager_admin_scripts") );
        add_action( 'admin_print_styles', array(&$this, "wp_gear_manager_admin_styles") );
        add_action( 'admin_enqueue_scripts', array(&$this, 'checkForm_script') );
        add_action( 'wp_ajax_getOptions', array(&$this, 'get_Options') );
        add_action( 'wp_ajax_nopriv_getOptions', array(&$this, 'get_Options') );
        add_action( 'plugins_loaded', array($this, 'st_google_map_init') );
    }
     /* 
    
    *Active Notice
    
    */    
    function st_mapWP_activation_notice(){
        echo '<div class="updated" style="background-color: #53be2a; border-color:#199b57">            
        <p>Thank you for installing <strong>ST Google Map</strong></p>
    </div>';
    }
    function st_mapWP_activate(){
        update_option('st_mapWP_admin_notice','TRUE');
    }
    
    function reg_act_hook() {
        register_activation_hook( __FILE__, array(&$this, 'st_mapWP_activate') );
    }
    //END NOTICE
    /* 

    *Admin Menu Item
    
    */
    
    //////////////////////////////////////////////////////////////////////////////
    function st_mapWP_register_admin_menu_page(){
        add_options_page( 'ST Google Map', 'ST Google Map', 'manage_options', 'st_google_map', array(&$this, 'st_mapWP_admin_menu_page') ); 
    }
    /* 

    * ADMIN SETTING FORM
    
    */
    function st_mapWP_admin_menu_page() {

    ?>
<div id="st-map" class="st-mapWP">
    <h2><?php _e('ST Google Map', 'st-google-map')?></h2>
    <div class="main box left">
        <h3 class="box-title"><div class="dashicons dashicons-admin-generic"></div><?php _e('Global Setting', 'st-google-map')?></h3>
        <div class="content">
            <ul id="steps">
                <li class="sel"><a rel="step1" href="#" class="defaulttab"><?php _e('Map Setting', 'st-google-map')?></a></li>
                <li class="sel"><a rel="step2" href="#"><?php _e('Custom Css', 'st-google-map')?></a></li>
            </ul>
            <form method="post" action="" id="signUp">
                <div class="clearBoth fieldset">
                    <div id="step1" class="tabs-panel">
                        <div>
                            <p class="label"><?php _e('Lattitude', 'st-google-map')?></p>
                            <span id="latspan">0</span> 
                        </div>
                        <div>
                            <p class="label"><?php _e('Longitude', 'st-google-map')?></p>
                            <span id="lngspan">0</span> 
                        </div>
                        <div>
                            <p class="label"><?php _e('Lat Lng', 'st-google-map')?></p>
                            <span id="latlong">0</span> 
                        </div>
                        <div>
                            <p class="label"><?php _e('Location', 'st-google-map')?></p> 
                            <input type="text" id="st-map-location" name="location" class="box-big" value="" />
                            <input type="button" class="sts-button sts-code sts-setview-popup" value="Create Map" />
                            <?php $this->form_latlng(); ?>
                            <input type="button" class="sts-button sts-show-list-view show-latlng" value="Hide List Map" />
                            <div id="st-popup-opts" class="hide_option">
                                <div class="hide_main">
                                    <table class="sts-table">
                                        <thead>
                                        <tr>
                                            <th><?php _e('ID', 'st-google-map')?></th>
                                            <th><?php _e('Name', 'st-google-map')?></th>
                                            <th><?php _e('Lat Lng', 'st-google-map')?></th>
                                            <th><?php _e('Actions', 'st-google-map')?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $this->show_latlng()?>
                                        </tbody>
                                    </table>                             
                                </div>
                            </div>
                        </div>
                        <p class="sup">* Select your location on the map below (Use our auto suggestion enabled location box to add your location). You can create as many maps you want to add.</p>
                        <div class="wrap-map">
                            <input id="pac-input" class="controls" type="text"
                                placeholder="<?php _e('Enter a location', 'st-google-map')?>">
                            <div id="type-selector" class="controls">
                              <input type="radio" name="type" id="changetype-all" checked="checked">
                              <label for="changetype-all"><?php _e('All', 'st-google-map')?></label>
                            
                              <input type="radio" name="type" id="changetype-establishment">
                              <label for="changetype-establishment"><?php _e('Establishments', 'st-google-map')?></label>
                            
                              <input type="radio" name="type" id="changetype-address">
                              <label for="changetype-address"><?php _e('Addresses', 'st-google-map')?></label>
                            
                              <input type="radio" name="type" id="changetype-geocode">
                              <label for="changetype-geocode"><?php _e('Geocodes', 'st-google-map')?></label>
                            </div>
                            <div id="map-canvas"></div>   
                        </div>
                    </div>
                    <div id="step2" class="tabs-panel tabs-hide">
                        <div>
                            <p class="label"><?php _e('Custom Css', 'st-google-map')?>:</p>
                            <input type="hidden" class="in" name="files" value="popup.css" />
                            <input type="button" class="sts-button sts-load sts-code sts-show" value="Load Code" />
                            <textarea name="code-content" style="display: none; width: 100%; margin: 0 auto; height: 300px;"></textarea>
                            <p class="sup"><?php _e('* Change css style the within maps the popup with the custom-popup.', 'st-google-map')?></p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="main box right">
        <?php $this->st_map_copyright();?>
    </div>
</div>
<?php $mapDf = $this->set_view_center();?>
<script>
    var Lattitude = <?php echo (($mapDf['lattitude'] && !empty($mapDf['lattitude'])) ? $mapDf['lattitude'] : '34.048108084909835');?>;
    var Longitude = <?php echo (($mapDf['longitude'] && !empty($mapDf['longitude'])) ? $mapDf['longitude'] : '-118.1854248046875');?>;
    var Mapinfo = '<div style="text-align:center"><b><?php echo ((!empty($mapDf['name'])) ? $mapDf['name'] : 'Los Angeles, United States');?></b><br/><p><?php echo ((!empty($mapDf['latlng'])) ? $mapDf['latlng'] : 'Los Angeles Los Angeles County CA');?></p></div>';
</script>
    <?php
    }
    /* 

    * SHORTCODE
    
    */
    function st_mapWP_Shortcode($atts = array()) {
        extract( shortcode_atts( array(
    		'width' => '',
            'height' => '',
            'fullwidth' => '',
            'mapid' => ''
        ), $atts));
        if($fullwidth == true) :
            $getWidth = '100%';
        else :
            if($width == '') :
                $getWidth = '100%';
            else :
                $getWidth = $width."px";
            endif;
        endif;
        $getHeight = (($height != '') ? $height."px" : '500px');
        if( $mapid && $mapid != "" ) :
            $val = $this->get_map_val( $mapid );
            $value = array(
                'width' => $getWidth,
                'height' => $getHeight,
                'id' => $mapid,
                'avatar' => $val['avatar'],
                'setView' => $val['latlng'],
                'popup_title' => $val['popup_title'],
                'description' => $val['description'],
                'scrollwz' => $val['scrollwz'],
                'maxZoom' => $val['maxzoom'],
                'zoom' => $val['zoom'],
                'mapid' => uniqid()
            );
            return $this->add_map( $value );
        else :
            return $notice = 'Please set its view to our chosen geographical coordinates.';
        endif;
    }
    function add_map( $args = array() ) {
        extract( $args );
        $showForm = '<div id="'.$mapid.'" class="st-map" style="width:'.$width.';height:'.$height.';"> </div>';
    	$script = "<script>// <![CDATA[
                    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    		osmAttrib = ' Wordpress Themes | Joomla Templates',
                    		osm = L.tileLayer(osmUrl, {maxZoom: ".$maxZoom.", attribution: osmAttrib});
                    	var map = L.map('".$mapid."', {scrollWheelZoom:".$scrollwz."}).setView([".$setView."], ".$zoom.").addLayer(osm);
                    ";       
        $script .= 'var '.$id.' = '.'\'<div class="dot"></div><div class="st-map-popup"><img class="map-popup_avatar" src="'.$avatar.'" width="40" height="40" /><div class="map-popup-modal" style="padding-left: 36px;"><span class="openBadge"><span class="openBadgeIcon"></span></span><span class="map-popup-name">'.$popup_title.'</span><span class="map-popup-info">'.$description.'</span></div></div>\';';
        $script .= "L.marker([".$setView."]).addTo(map).bindPopup(".$id.").openPopup();";
    	$script .= "// ]]></script>";
    	return $showForm.$script;
    }
    function load_code( $file ) {
        $filename = $this->popup_url . trim($file);
        if( @file_get_contents($filename) == true ) :
            $code = @file_get_contents($filename);
            echo $code;
        else :
            return false;
        endif;
    }
    function save_code( $file, $content ) {
        if ( current_user_can('edit_plugins') ) :
            $filename = ST_MAP_ROOT ."popup/". $file;
            $setContent = wp_unslash($content); // Remove slashes from a string or array of strings.
            if( is_writeable( $filename ) ) :
                $setfile = fopen($filename, "w+") or die("Unable to open file!");
                if( $setfile !== false ) :
                    fwrite($setfile, urldecode($setContent));
                    fclose($setfile);
                    echo $content;
                endif;
            endif;
        else :
            wp_die('<p>'.__('You do not have sufficient permissions to edit plugin for this site.').'</p>');
        endif;
    }
    function ext( $file ) {
        $tmp = explode('.', $file);
        return $ext = end($tmp);
    }              
    function shortcodes() {
		add_shortcode( 'ST-mapWP', array(&$this, 'st_mapWP_Shortcode') );
	}
    function get_map_val( $key ) {
        if($key != '') :
            return $this->latlng[$key];
        else :
            return;
        endif;
    }
    function add_button() {  
        if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) {  
            add_filter('mce_external_plugins', array(&$this, 'add_plugin'));  
            add_filter('mce_buttons', array(&$this, 'register_button'));  
        }  
    }
    function register_button($buttons) {  
        array_push($buttons, "st_mapWP_Shortcode");  
        return $buttons;  
    }
    function add_plugin($plugin_array) { 
        $path = ST_MAP_JS_URL . 'button.js';
        $plugin_array['st_mapWP_Shortcode'] = $path;  
        return $plugin_array;  
    }  
    function get_setting( $name, $default = '' ) {
        if( get_option('st_map_opts_'.$name) !== false ) :
            return get_option('st_map_opts_'.$name, $default);
        else :
            return $default;
        endif;
    }
    function set_setting($name, $value) {
        if( get_option('st_map_opts_'.$name) !== false ) :
            update_option('st_map_opts_'.$name, $value);
        else :
            add_option('st_map_opts_'.$name, $value);
        endif;
    }
    function set_latlng($id, $name, $latlng, $popup_title, $desc, $avatar, $zoom, $maxzoom, $scrollwz) {
        unset($this->latlng[$id]);
        $avatar = (($avatar != "") ? strip_tags($avatar) : ST_MAP_IMG_URL."no-avatar.png");
        $id = trim($id);
        $latlng_opts = array();
        $latlng_opts[$id] = array(
            'name' => strip_tags($name),
            'latlng' => strip_tags($latlng),
            'popup_title' => strip_tags($popup_title),
            'description' => strip_tags($desc),
            'avatar' => $avatar,
            'zoom' => strip_tags($zoom),
            'maxzoom' => strip_tags($maxzoom),
            'scrollwz' => strip_tags($scrollwz)
        );
        $Opts = get_option('st_latlng');
        if( empty($Opts) ) :
            update_option('st_latlng', $latlng_opts);
        else:
            $latlng_opts = array_merge($Opts, $latlng_opts);
            update_option('st_latlng', $latlng_opts);
        endif;
        $Opts = get_option('st_latlng');   
        echo json_encode(
            array(
                'id' => $id,
                'name' => $Opts[$id]['name'],
                'latlng' => $Opts[$id]['latlng'],
                'popup_title' => $Opts[$id]['popup_title'],
                'description' => $Opts[$id]['description'],
                'avatar' => $Opts[$id]['description'],
                'img_url' => ST_MAP_IMG_URL,
                'no_avatar' => ST_MAP_IMG_URL."no-avatar.png"
            )
        );
        die();
    }
    function update_latlng($id, $latlng, $name, $popup_title, $desc, $avatar, $zoom, $maxzoom, $scrollwz) {
        unset($this->latlng[$id]);
        $avatar = (($avatar != "") ? strip_tags($avatar) : ST_MAP_IMG_URL."no-avatar.png");
        $Opts = get_option('st_latlng');
        $value = array();
        $value[$id] = array(
            'latlng' => strip_tags($latlng),
            'name' => strip_tags($name),
            'popup_title' => strip_tags($popup_title),
            'description' => strip_tags($desc),
            'avatar' => $avatar,
            'zoom' => strip_tags($zoom),
            'maxzoom' => strip_tags($maxzoom),
            'scrollwz' => strip_tags($scrollwz)
            
        );
        $latlng_opts = array_merge($Opts, $value);
        update_option('st_latlng', $latlng_opts);
        echo json_encode(array('key' => 1));
        die();
    }
    function del_latlng($id) {
		unset($this->latlng[$id]);
		update_option('st_latlng', $this->latlng);
        echo json_encode( array(
                'row_id' => $id
            )
        );
        die();
    }
    function set_view_center() {
        if($this->latlng != null) :
            $setL = array();
            foreach( $this->latlng as $key ) :
                $setL[] = $key;
            endforeach;
            $latlng = $this->cut($setL[0]['latlng']);
            $val = array(
                'latlng' => $setL[0]['latlng'],
                'lattitude' => $latlng[0],
                'longitude' => $latlng[1],
                'name' => $setL[0]['name']
            );
            return $val;
        else :
            return;
        endif;
    }
    function cut( $value ) {
        return explode(', ', trim($value));
    }
    function get_shortcodes(){
        echo '<script type="text/javascript">
        var st_map_id = new Array();
        var st_map_name = new Array();';
		$count = 0;
		foreach ($this->latlng as $key => $value) {
			echo "st_map_id[{$count}] = '{$key}';";
			echo "st_map_name[{$count}] = '{$value['name']}';";
			$count++;
		}
        echo '</script>';
    }
    function show_latlng() {
        
        if( $this->latlng != null ) :
            foreach( get_option('st_latlng') as $key => $value ) :
?>
                <tr>
                    <td><?=$key;?></td>
                    <td><?=((isset($value['name'])) ? $value['name'] : "")?></td>
                    <?php $latlng = $this->cut($value['latlng']);?>
                    <td><a class="loadmap" title="View <?=((isset($value['name'])) ? $value['name'] : "")?> On Map" onclick="loadMap('<?=$latlng[0]?>','<?=$latlng[1]?>', '<?=((isset($value['name'])) ? $value['name'] : "")?>', '<?=((isset($value['latlng'])) ? $value['latlng'] : "")?>');"><?=((isset($value['latlng'])) ? $value['latlng'] : "")?></a></td>
                    <td>
                        <input type="hidden" id="<?php echo $key."_name";?>" value="<?php echo ((isset($value['name'])) ? $value['name'] : "")?>" />
                        <input type="hidden" id="<?php echo $key."_latlng";?>" value="<?php echo ((isset($value['latlng'])) ? $value['latlng'] : "")?>" />
                        <input type="hidden" id="<?php echo $key."_popup_title";?>" value="<?php echo ((isset($value['popup_title'])) ? $value['popup_title'] : "")?>" />
                        <input type="hidden" id="<?php echo $key."_description";?>" value="<?php echo ((isset($value['description'])) ? $value['description'] : "")?>" />
                        <input type="hidden" id="<?php echo $key."_avatar";?>" value="<?php echo ((isset($value['avatar'])) ? $value['avatar'] : "")?>" />
                        <input type="hidden" id="<?php echo $key."_zoom";?>" value="<?php echo ((isset($value['zoom'])) ? $value['zoom'] : "")?>" />
                        <input type="hidden" id="<?php echo $key."_maxzoom";?>" value="<?php echo ((isset($value['maxzoom'])) ? $value['maxzoom'] : "")?>" />
                        <input type="hidden" id="<?php echo $key."_scrollwz";?>" value="<?php echo ((isset($value['scrollwz'])) ? $value['scrollwz'] : "")?>" />
                        <a title="Edit" id="<?php echo $key;?>" class="latlng_edit"><img src="<?php echo ST_MAP_IMG_URL . "pencil.png"?>" /></a>
                        <input type="hidden" id="latlng_del_id" value="<?php echo $key;?>" />
                        <a title="Delete" id="<?php echo $key;?>" class="latlng_del"><img src="<?php echo ST_MAP_IMG_URL . "trash_can.png"?>" /></a>
                    </td>
                </tr>
<?php
            endforeach;
        else :
            return false;
        endif;
    }
    function st_google_map_init() {
        $plugin_dir = basename(dirname(__FILE__)).'/languages/';
        load_plugin_textdomain( 'st-google-map', false, $plugin_dir );
    }
    function add_scripts() {
		wp_enqueue_script( 'st-mapwp-jquery', ST_MAP_JS_URL."map.js", '1.0.0');
		$this->add_style();
    }
    function add_style() {
        wp_enqueue_style( 'st-mapwp-theme', ST_MAP_CSS_URL.'map.css' );
        wp_enqueue_style( 'st-mapwp-popup', ST_MAP_POPUP_URL.'popup.css' );
        wp_enqueue_style( 'st-mapwp-admin', ST_MAP_CSS_URL.'admin.css' );
    }
    function wp_gear_manager_admin_scripts() {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('jquery');
    }
    function checkForm_script(){
        wp_register_script('st_map_admin_js', ST_MAP_JS_URL . 'admin.js', array('jquery'), null, false);
        wp_localize_script('st_map_admin_js', 'map_ajax', array('url' => admin_url('admin-AJAX.php')));
        wp_enqueue_script('st_map_admin_js');
        wp_enqueue_script( 'st-mapwp-select-location', ST_MAP_JS_URL."select-location.js");
        echo $script = '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&language=en&sensor=false&libraries=places"></script>';
    }
    function wp_gear_manager_admin_styles() {
        wp_enqueue_style('thickbox');
    }
    function get_Options() {
    	global $wpdb;
        if ( !$_POST['control'] ) return;
        if( $_POST['control'] ) :
            switch( $_POST['control'] ) :
                case 'load-code' :
                    $this->load_code($_POST['filename']);
                    break;
                case 'save-code' :
                    $this->save_code($_POST['filename'], $_POST['content']);
                    break;
                case 'set-latlng' :
                    $this->set_latlng(trim($_POST['latlng_id']), trim($_POST['latlng_name']), trim($_POST['latlng']), trim($_POST['popup_title']), trim($_POST['description']), trim($_POST['avatar']), trim($_POST['zoom']), trim($_POST['maxzoom']), trim($_POST['scrollwz']));
                    break;
                case 'latlng-del' :
                    $this->del_latlng($_POST['id']);
                    break;
                case 'update_latlng' :
                    $this->update_latlng(trim($_POST['id']), trim($_POST['latlng']), trim($_POST['latlng_name']), trim($_POST['popup_title']), trim($_POST['description']), trim($_POST['avatar']), trim($_POST['zoom']), trim($_POST['maxzoom']), trim($_POST['scrollwz']));
                    break;
                default:
                    return;
                    break;
            endswitch;
        endif;
    	die();
    }
    function st_map_copyright() {
?>
        <h3 class="box-title"><div class="dashicons dashicons-sos"></div><?php _e('Abouts', 'st-google-map')?></h3>
        <div class="st-box">
        	<div class="box-content">
        		<div class="st-row">
        			Hi,</br></br>We are Beautiful-Templates and we provide Wordpress Themes & Plugins, Joomla Templates & Extensions.</br>Thank you for using our products. Let drop us feedback to improve products & services.</br></br>Best regards,</br> Beautiful Templates Team
        		</div>
        	</div>
        	<div class="st-row st-links">
        		<div class="col col-8 links">
        			<ul>
        				<li>
        					<a href="http://beautiful-templates.com/" target="_blank"><?php _e('Home', 'st-google-map')?></a>
        				</li>
        				<li>
        					<a href="http://beautiful-templates.com/amember/" target="_blank"><?php _e('Submit Ticket', 'st-google-map')?></a>
        				</li>
        				<li>
        					<a href="http://beautiful-templates.com/evo/forum/" target="_blank"><?php _e('Forum', 'st-google-map')?></a>
        				</li>
        				<li>
        					<?php add_thickbox(); ?>
        					<a href="<?php echo plugins_url( '/doc/index.html', __FILE__ ); ?>?TB_iframe=true&width=1000&height=600" class="thickbox"><?php _e('Document', 'st-google-map')?></a>
        				</li>
        			</ul>
        		</div>
        		<div class="col col-2 social">
        			<ul>
        				<li>
        					<a href="https://www.facebook.com/beautifultemplates/" target="_blank"><div class="dashicons dashicons-facebook-alt"></div></a>
        				</li>
        				<li>
        					<a href="https://twitter.com/cooltemplates/" target="_blank"><div class="dashicons dashicons-twitter"></div></a>
        				</li>
        			</ul>
        		</div>
        	</div>
        </div>
        <div class="st-box st-rss">
        	<div class="box-content">
        		<div class="st-row st_load_rss">
        			<span class="spinner" style="display:block;"></span>
        		</div>
        	</div>
        </div>
<?php
}
    function form_latlng() {
?>
        <div class="sts-saveview-popup-wrap wrap-popup">
            <div class="sts-saveview-popup">
                <a class="sts-form-close dashicons dashicons-no-alt"></a>
                <ul id="tabs">
                    <li class="setcol"><a rel="tab1" href="#" class="default"><?php _e('Info', 'st-google-map')?></a></li>
                    <li class="setcol"><a rel="tab2" href="#"><?php _e('Map Setting', 'st-google-map')?></a></li>
                </ul>
                <div id="tab1" class="tabs-panel">
                    <div>
                        <p class="label"><?php _e('Lat Lng', 'st-google-map')?></p>
                        <input type="text" id="setlatlng" name="setlatlng" class="box-big" value="" readonly="readonly" />
                    </div>
                    <div>
                        <p class="label"><?php _e('Name', 'st-google-map')?></p>
                        <input type="text" id="latlng-name" class="val empty" name="latlng-name" value="" />
                    </div>
                    <div>
                        <p class="label"><?php _e('Title', 'st-google-map')?>:</p>
                        <input type="text" class="box-big val empty" name="popup_title" id="popup_title" value="" />
                    </div>
                    <div>
                        <p class="label"><?php _e('Description', 'st-google-map')?>:</p>
                        <textarea class="description" name="description" class="val empty" id="description"></textarea>
                    </div>
                    <div>
                        <p class="label"><?php _e('UpLoad Avatar', 'st-google-map')?>:</p>
                        <p class="avatar">
                    		<input id="upload_image" type="text" size="30" name="upload_image" class="val empty" value="" />
                    		<input id="upload_image_button" type="button" class="sts-button sts-avatar" value="Upload" />
                    	</p>
                    </div>
                    <div class="show-avatar">
                        <div class="wrap-img">
                            <img class="img val empty" src="<?php echo ST_MAP_IMG_URL."no-avatar.png";?>" />
                        </div>
                    </div>
                </div>
                <div id="tab2" class="tabs-panel">
                    <div>
                        <p class="label"><?php _e('Zoom', 'st-google-map')?></p> 
                        <input type="text" id="zoom" name="zoom" class="box-big" value="" />
                    </div>
                    <div>
                        <p class="label"><?php _e('Max Zoom', 'st-google-map')?></p> 
                        <input type="text" id="maxzoom" name="max-zoom" class="box-big" value="" />
                    </div>
                    <div>
                        <p class="label" style="width: 200px;"><?php _e('Turn Off Scroll Wheel Zooming', 'st-google-map')?>:</p>
                        <input type="hidden" name="scrollwz" id="scrollwz" class="scrollwz" value="" />
                        <div class="commutator">
                                <div class="is on">
                                    <?php _e('Yes', 'st-google-map')?>
                                    <div class="is off"> <?php _e('No', 'st-google-map')?> </div>
                                </div>
                        </div>
                    </div>
                    <div class="sts-actions popup">
                        <div class="box-button">
                            <input type="hidden" id="lgetID" value="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}// END StMapWP CLASS
$StMapWP = new StMapWP();
require_once(ST_MAP_ROOT . 'widget.php');