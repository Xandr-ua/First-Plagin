<?php

if(!class_exists('newPostTypeCpt')) {
    class newPostTypeCpt {
        public function register() {
            add_action('init', array($this, 'custom_post_type'));

            add_action('add_meta_boxes', array($this, 'add_meta_box_propery'));
            add_action('save_post', array($this, 'save_metabox'), 10, 2);
        }

        public function add_meta_box_propery() {
            add_meta_box(
                'new_post_type_settings',
                'Property Settings',
                [$this, 'metabox_property_html'],
                'property',
                'normal',
                'default'
            );
        }

        public function save_metabox($post_id, $post) {

            if (!isset($_POST['_newposttype']) || !wp_verify_nonce($_POST['_newposttype'], 'new_post_type_fields')) {
                return $post_id;
            }

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }

            if (($post->post_type) != 'property') {
                return $post_id;
            }

            $post_type = get_post_type_object($post->post_type);
            if (!current_user_can($post_type->cap->edit_post, $post_id)) {
                return $post_id;
            }


            if (is_null($_POST['new_post_type_price'])) {
                delete_post_meta($post_id, 'new_post_type_price');
            } else {
                update_post_meta($post_id, 'new_post_type_price', sanitize_text_field(intval($_POST['new_post_type_price'])));
            }

            if (is_null($_POST['new_post_type_period'])) {
                delete_post_meta($post_id, 'new_post_type_period');
            } else {
                update_post_meta($post_id, 'new_post_type_period', sanitize_text_field($_POST['new_post_type_period']));
            }

            if (is_null($_POST['new_post_type_type'])) {
                delete_post_meta($post_id, 'new_post_type_type');
            } else {
                update_post_meta($post_id, 'new_post_type_type', sanitize_text_field($_POST['new_post_type_type']));
            }

            if (is_null($_POST['new_post_type_agent'])) {
                delete_post_meta($post_id, 'new_post_type_agent');
            } else {
                update_post_meta($post_id, 'new_post_type_agent', sanitize_text_field($_POST['new_post_type_agent']));
            }
        }

        public function metabox_property_html($post) {
            $price = get_post_meta( $post->ID,'new_post_type_price', true);
            $period = get_post_meta( $post->ID,'new_post_type_period', true);
            $type = get_post_meta( $post->ID,'new_post_type_type', true);
            $agent_meta = get_post_meta( $post->ID,'new_post_type_agent', true);


            wp_nonce_field('new_post_type_fields', '_newposttype');


            echo '
                <p>
                    <label for="new_post_type_price">'.esc_html__('Price', 'New Post Type').'</label>
                    <input type="number" id="new_post_type_price" name="new_post_type_price" value="'.esc_attr__($price).'">
                </p>
                
                <p>
                    <label for="new_post_type_period">'.esc_html__('Period', 'New Post Type').'</label>
                    <input type="text" id="new_post_type_period" name="new_post_type_period" value="'.esc_attr__($period).'">
                </p>
                
                <p>
                    <label for="new_post_type_type">'.esc_html__('Type', 'New Post Type').'</label>
                    <select id="new_post_type_type" name="new_post_type_type">
                    <option value="">Select Type</option>
                    <option value="Sale" '.selected('Sale', $type, false).'>'.esc_html__('For Sale', 'New Post Type').'</option>
                    <option value="Rent" '.selected('Rent', $type, false).'>'.esc_html__('For Rent', 'New Post Type').'</option>
                    <option value="Sould" '.selected('Sould', $type, false).'>'.esc_html__('Sould', 'New Post Type').'</option>
                    </select>
                </p>
            ';

            $agents = get_posts(array('post_type'=>'agent', 'posts_per_page'=>-1));

            if ($agents) {

                echo '<p>
                    <label for="new_post_type_agent">'.esc_html__('Agent', 'New Post Type').'</label>
                    <select id="new_post_type_agent" name="new_post_type_agent">
                    <select id="new_post_type_agent" name="new_post_type_agent">
                    <option value="">'.esc_html__('Select Agent', 'New Post Type').'</option>
                    ';

                foreach ($agents as $agent) {?>
                    <option value="<?php echo esc_html__($agent->ID); ?>" <?php if($agent->ID == $agent_meta){echo 'selected'; } ?>><?php echo esc_html__($agent->post_title)?></option>
                <?php }

                echo '
                    </select>
                    </p>
                ';
            }
        }

        public function custom_post_type() {
            register_post_type('property',
                array(
                    'public' => true,
                    'has_archive' => true,
                    'rewrite' => array('slug'=>'properties'),
                    'label' => 'Property',
                    'supports' => array('title', 'editor', 'thumbnail'),
                ));

            register_post_type('agent',
                array(
                    'public' => true,
                    'has_archive' => true,
                    'rewrite' => array('slug'=>'agents'),
                    'label' => 'Agents',
                    'supports' => array('title', 'editor', 'thumbnail'),
                ));

            $labels = array(
                'name'              => esc_html_x( 'Locations', 'taxonomy general name', 'custom_post_type' ),
                'singular_name'     => esc_html_x( 'Location', 'taxonomy singular name', 'custom_post_type' ),
                'search_items'      => esc_html__( 'Search Locations', 'custom_post_type' ),
                'all_items'         => esc_html__( 'All Locations', 'custom_post_type' ),
                'parent_item'       => esc_html__( 'Parent Location', 'custom_post_type' ),
                'parent_item_colon' => esc_html__( 'Parent Location:', 'custom_post_type' ),
                'edit_item'         => esc_html__( 'Edit Location', 'custom_post_type' ),
                'update_item'       => esc_html__( 'Update Location', 'custom_post_type' ),
                'add_new_item'      => esc_html__( 'Add New Location', 'custom_post_type' ),
                'new_item_name'     => esc_html__( 'New Location Name', 'custom_post_type' ),
                'menu_name'         => esc_html__( 'Location', 'custom_post_type' ),
            );

            $args = array(
                'hierarchical' => true,
                'show_ua' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug'=>'properties/location'),
                'labels' => $labels,
            );

            register_taxonomy('location', 'property', $args );

            unset($args);
            unset($labels);


            $labels = array(
                'name'              => esc_html_x( 'Types', 'taxonomy general name', 'custom_post_type' ),
                'singular_name'     => esc_html_x( 'Type', 'taxonomy singular name', 'custom_post_type' ),
                'search_items'      => esc_html__( 'Search Types', 'custom_post_type' ),
                'all_items'         => esc_html__( 'All Types', 'custom_post_type' ),
                'parent_item'       => esc_html__( 'Parent Type', 'custom_post_type' ),
                'parent_item_colon' => esc_html__( 'Parent Type:', 'custom_post_type' ),
                'edit_item'         => esc_html__( 'Edit Type', 'custom_post_type' ),
                'update_item'       => esc_html__( 'Update Type', 'custom_post_type' ),
                'add_new_item'      => esc_html__( 'Add New Type', 'custom_post_type' ),
                'new_item_name'     => esc_html__( 'New Type Name', 'custom_post_type' ),
                'menu_name'         => esc_html__( 'Type', 'custom_post_type' ),
            );

            $args = array(
                'hierarchical' => true,
                'show_ua' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug'=>'properties/type'),
                'labels' => $labels,
            );

            register_taxonomy('property-type', 'property', $args );
        }
    }
}

if(class_exists('newPostTypeCpt')) {
    $newPostTypeCpt = new newPostTypeCpt();
    $newPostTypeCpt->register();
}