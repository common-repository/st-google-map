<?php
class st_map_widget extends WP_Widget {
    function st_map_widget() {
        parent::WP_Widget( $id = 'st_map_widget', $name = 'ST GooGle Map' , $options = array( 'description' => 'Easy to create google map with simple shortcode.' ) );
    }
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['st_map_title'] = strip_tags( $new_instance['st_map_title'] );
        $instance['st_maps'] = $new_instance['st_maps'];
        $instance['st_map_width'] = $new_instance['st_map_width'];
        $instance['st_map_height'] = $new_instance['st_map_height'];
        $instance['st_map_fwidth'] = !empty( $new_instance['st_map_fwidth'] ) ? 1 : 0;
        return $instance;
    }
    function form( $instance ) {
        global $StMapWP;
        if($StMapWP->latlng == null) :
            echo 'please set its view to our chosen geographical coordinates from <a href="'.site_url().'/wp-admin/admin.php?page=st_google_map">here</a>';
            return;
        endif;
        $instance = wp_parse_args( (array) $instance, array( 'st_map_title' => '') );
        $instance['st_map_title'] = esc_attr( $instance['st_map_title'] );
        $instance['st_maps'] = ((isset($instance['st_maps'])) ? $instance['st_maps'] : 0);
        $instance['st_map_width'] = ((isset($instance['st_map_width'])) ? $instance['st_map_width'] : "");
        $instance['st_map_height'] = ((isset($instance['st_map_height'])) ? $instance['st_map_height'] : "");
        $instance['st_map_fwidth'] = ( isset($instance['st_map_fwidth']) ? (bool) $instance['st_map_fwidth'] : false );
?>
        <div>
            <p>
                <label for="<?php echo $this->get_field_id('st_map_title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('st_map_title')?>" value="<?php echo esc_attr( $instance['st_map_title'] ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('st_maps'); ?>"><?php _e('Choose category :'); ?></label>
                <select id="<?php echo $this->get_field_id('st_maps'); ?>" class="widefat" name="<?php echo $this->get_field_name('st_maps'); ?>">
    					<?php echo implode('', $this->get_map( $instance['st_maps'] )); ?>
                </select>
            </p>
            <p>
                <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('st_map_fwidth')?>"<?php checked( $instance['st_map_fwidth'] ); ?> />
                <label for="<?php echo $this->get_field_id('st_map_fwidth'); ?>"><?php _e('Set FullWidth'); ?></label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('st_map_width'); ?>"><?php _e('Width :'); ?></label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('st_map_width')?>" value="<?php echo esc_attr( $instance['st_map_width'] ); ?>" />
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('st_map_height'); ?>"><?php _e('Height :'); ?></label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('st_map_height')?>" value="<?php echo esc_attr( $instance['st_map_height'] ); ?>" />
            </p>
        </div>
<?php
    } // END FORM
    function widget( $args, $instance ) {
        global $StMapWP;
        extract( $args, EXTR_SKIP );
        if($instance['st_map_fwidth'] == 1) :
            $getWidth = '100%';
        else :
            if($instance['st_map_width'] == '') :
                $getWidth = '100%';
            else :
                $getWidth = $instance['st_map_width']."px";
            endif;
        endif;
        $val = $StMapWP->get_map_val($instance['st_maps']);
        $Opts = array(
            'width' => $getWidth,
            'height' => (($instance['st_map_height'] != '') ? $instance['st_map_height']."px" : '500px'),
            'id' => $instance['st_maps'],
            'avatar' => $val['avatar'],
            'popup_title' => $val['popup_title'],
            'description' => $val['description'],
            'setView' => $val['latlng'],
            'scrollwz' => $val['scrollwz'],
            'maxZoom' => $val['maxzoom'],
            'zoom' => $val['zoom'],
            'mapid' => uniqid()
        );
        $title = apply_filters( 'widget_title', empty( $instance['st_map_title'] ) ? "" : $instance['st_map_title'], $instance );
        echo $before_widget;
            if ( $title != '' ) :
                echo $before_title . $title . $after_title;
            endif;
            if($StMapWP->latlng == null) :
                echo 'Please set its view to our chosen geographical coordinates.';
            else :
                echo $StMapWP->add_map( $Opts );
            endif;
        echo $after_widget;
    }
    function get_map( $val ) {
        global $StMapWP;
        $show_map = array();
        $show_map[] = '<option value="0">Select Map...</option>';
        foreach ($StMapWP->latlng as $key=>$value) :
			$selected = $val === $key ? ' selected="selected"' : '';
			$show_map[] = '<option value="' . $key .'"' . $selected . '>' . $value['name'] . '</option>';
		endforeach;
        
        return $show_map;
    }
} // END WIDGET
add_action( 'widgets_init', create_function( '', 'register_widget("st_map_widget");' ) );