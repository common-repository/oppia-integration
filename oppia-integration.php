<?php
/*
Plugin Name: Oppia integration plugin
Description: Fetches and embeds Courses, Events, Webinars, etc. from oppia.fi feed
Version: 1.0
License: GPL2
*/

// initials
define( 'OPPIA_INT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OPPIA_INT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
require_once( OPPIA_INT_PLUGIN_DIR . 'includes/class.content-fetcher.php' );
require_once( OPPIA_INT_PLUGIN_DIR . 'includes/class.content-fetcher-admin.php' );
require_once( OPPIA_INT_PLUGIN_DIR . 'includes/oppia_helpers.php' );

// admin actions
if ( is_admin() ){
  add_action('admin_menu', 'oppia_plugin_setup_menu');
}

// register styles
wp_register_style( 'oppia',  plugin_dir_url( __FILE__ ).'assets/css/fetcher.css'  );
wp_enqueue_style( 'oppia' );

function oppia_plugin_setup_menu(){
  add_menu_page(
          'Oppia integration plugin',
          'Oppia  plugin',
          'manage_options',
          'oppia',
          'oppia_settings' );
  add_action( 'admin_init', 'oppia_int_register_settings' );
}

function oppia_settings(){
  Oppia_Content_Fetcher_Admin::oppia_settings();
}

function oppia_content_fetcher_widget($atts) {
    if(!isset($atts['id'])) {
        return '';
    }
  $config_list = get_option('oppia_config_list');

  $args = $config_list[$atts['id']];

  $cfi = new Oppia_Content_Fetcher();
  $output = $cfi->widget($args);
  return $output;
}

add_shortcode('oppia_integration','oppia_content_fetcher_widget');
add_filter('widget_text','do_shortcode');

