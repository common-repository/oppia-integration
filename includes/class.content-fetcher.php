<?php
define ('OPPIAINT_API_KEY', esc_js(get_option('oppia_api_key')));
define ('OPPIA_BASE_HOST', 'oppia.fi');
require_once('oppia_helpers.php');
if(!defined(OPPIA_INT_PLUGIN_URL)){
  define( 'OPPIA_INT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
class Oppia_Content_Fetcher extends WP_Widget {

  private $sections_selected = [];
  private $selected_names = [];
  /**
   * Sets up the widgets name etc
   */
  public function __construct() {
    $widget_ops = array(
      'classname' => 'oppia_content_fethcer',
      'description' => 'Oppia Content Fetcher',
    );
    parent::__construct( 'oppia_content_fethcer', 'Oppia Content Fetcher', $widget_ops );
  }

  /**
   * Outputs the content of the widget
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance=null ) {

    $section = isset($args['sections'])?$args['sections']:1;
    $this->sections_selected = $section;
    $list_size  =isset($args['list_size'])?$args['list_size']:0;
    $quantities = isset($args['section_quantities'])?$args['section_quantities']:[];
    $items = self::retrieve_content($section, $quantities, $list_size, OPPIAINT_API_KEY);
    $return_value = '';
    $widget_height = isset($args['widget_height'])?$args['widget_height']:null;
    $title = '';
    if(isset($args['title']) && $args['title'] != '') {
      if(isset($args['before_title']) && $args['before_title'] != '') {
        $title = html_entity_decode($args['before_title']) .  $args['title'] .  html_entity_decode($args['after_title']);
      } else {
        $title = '<p>' . $args['title'] . '</p>';
      }
    }
    $return_value .= $title;
    if(isset($args['before_widget']) && $args['before_widget'] != '') {
      $return_value .= html_entity_decode($args['before_widget']);
    }
    
    if(is_null($widget_height)) {
      $return_value .= '<div>';
      $return_value .= '<div class="oppia-fetcher-plugin-content"><ul class="oppia-ul-fetcher">';
    } else {
      $return_value .= '<div style="height: ' . $widget_height . 'px;">';
      $return_value .= '<div class="oppia-fetcher-plugin-content" style="height: ' . $widget_height . 'px; overflow: auto"><ul class="oppia-ul-fetcher">';
    }
    $arrow_img_source = OPPIA_INT_PLUGIN_URL.'assets/chevron-right.png';
    if(is_null($items)) {
      return null;
    }
    foreach($items->items as $key=>$item) {

      if(isset($item->link) && $this->check_link($item->link)) {
        $return_value .= '<li class="oppia-li-item"><div class="oppia-col-date"><span>';
        $return_value .= $item->startdate != NULL ? date('j.n', strtotime(sanitize_text_field($item->startdate))) : '';

        $return_value .= '</span></div><div class="oppia-col-title"><div>';
        $return_value .= '<span class="title-text">' . sanitize_text_field($item->title) . '</span>';
        $return_value .= '</div><div style="text-overflow: ellipsis;"><span style="font-size: 0.7em;color:#555;font-style: italic">';
        $return_value .= sanitize_text_field($item->short_description) . '</span>';
        $return_value .= '</div></div><div class="oppia-col-link"><a href="' . $item->link . '" target="_blank">';
        $return_value .= '<img class="chevron" src="' . $arrow_img_source . '"></a></div></li>';
      }
    }

    $return_value .= '</ul></div></div>';

    if(isset($args['after_widget']) && $args['after_widget'] != '') {
        $return_value .= html_entity_decode($args['after_widget']);
    }
    return $return_value;
  }


  public function retrieve_content($sections, $quantitites, $list_size=null, $api_key, $all = 1) {

    $query_array = array('modules' => []);
    if(is_array($sections) && count($sections) > 0) {
      foreach ($sections as $module) {
        $query_array['modules'][] = array($module,isset($quantitites[$module])?$quantitites[$module]:0);
      }

      $query_string = http_build_query($query_array);
      $url = OPPIAINT_API_URL . '?'. $query_string;

      $args = ['headers'=>[
        'x-api-key' => $api_key,
        'content-type' => 'application/json'
      ]];

      $url .= '&all='.$all;
      $request = wp_remote_get( $url, $args );
      $result = json_decode($request['body']);
      return $result;
    }
    return null;
  }


  private function check_link($srcurl) {
    $url = parse_url($srcurl);
    if(!isset($url['scheme'])) {
      return false;
    }

    if(!isset($url['host'])) {
      return false;
    }
    if($url['scheme'] == 'http' || $url['scheme'] == 'https') {
      if($url['host'] == OPPIA_BASE_HOST) {
        return true;
      }
    }
    return false;
  }

  public function retrieve_sections($api_key) {
    $url = OPPIAINT_API_URL . 'modules';
    $args = ['headers'=>[
      'x-api-key' => $api_key,
      'content-type' => 'application/json'
    ]];
    $response = wp_remote_get( $url, $args );

    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
      return json_decode($response['body']);
    }
    return json_decode('[]');
  }

  public static function get_sections($api_key) {
    $url = OPPIAINT_API_URL . 'modules';
    $args = ['headers'=>[
      'x-api-key' => $api_key,
      'content-type' => 'application/json'
    ]];

    $response = wp_remote_get( $url, $args );

    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        return json_decode($response['body']);
    }
    return json_decode('[]');
  }



}