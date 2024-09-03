<?php
/*
    Plugin Name: New Post Type
    Plugin URI: http://first-plagin/
    Description: New plugin
    Version: 1.0
    Author: Olexandr
    Author URI: http://first-plagin/
    Licence: GPLv2 or later
    Text Domain: new-post-type
 */

if (!defined('ABSPATH')) {
    die;
}

define('NEWPOSTTYPE_PATH', plugin_dir_path(__FILE__));

if(!class_exists('newPostTypeCpt')) {
    require NEWPOSTTYPE_PATH.'inc/cpt.php';
};

class newPostType {

    function register() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_front'));
    }

    public function enqueue_admin() {
        wp_enqueue_style('newPostType__style', plugins_url('/assets/css/admin/style.css', __FILE__));
        wp_enqueue_script('newPostType__script', plugins_url('/assets/js/admin/scripts.js', __FILE__), array('jquery'), '1.0', true);
    }

    public function enqueue_front() {
        wp_enqueue_style('newPostTypeFront__style', plugins_url('/assets/css/front/style.css', __FILE__));
        wp_enqueue_script('newPostTypeFront__script', plugins_url('/assets/js/front/scripts.js', __FILE__), array('jquery'), '1.0', true);
    }


    static function activation() {
        flush_rewrite_rules();
    }

    static function deactivation() {
        flush_rewrite_rules();
    }
}

if(class_exists('newPostType')) {
    $newPostType = new newPostType();
    $newPostType->register();
}

register_activation_hook(__FILE__, array($newPostType, 'activation'));
register_deactivation_hook(__FILE__, array($newPostType, 'deactivation'));

