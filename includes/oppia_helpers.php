<?php
define ('OPPIAINT_API_URL', 'https://partner-api.oppia.fi/wordpresslist/');


function oppia_int_add_config_to_list($option) {
  $is_valid = oppia_validate_configuration_name();
  if($is_valid !== TRUE) {
    add_settings_error(
      'oppia_configuration_name',
      esc_attr( 'settings_updated' ),
      'The configuration name is not set!',
      'error'
    );
    return FALSE;
  }
  if($option != '') {
    $config_list = get_option('oppia_config_list');
    $config_settings = [];
    $config_settings['api_key'] = esc_js(get_option('oppia_api_key'));
    $config_settings['sections'] = get_option('oppia_sections');
    $config_settings['section_quantities'] = get_option('oppia_section_qty');
    $config_list[$option] = $config_settings;

    update_option('oppia_config_list', $config_list, 'yes' );

  }

  return $option;
}

function oppia_int_update_config($option) {
  $is_valid = oppia_validate_configuration_name();
  if($is_valid !== TRUE) {
    add_settings_error(
      'oppia_configuration_name',
      esc_attr( 'settings_updated' ),
      'The configuration name is not set!',
      'error'
    );
    return FALSE;
  }
  $is_new_set = get_option('oppia_newconfig');

  if(is_null($is_new_set) || $is_new_set == '') {
    if($option != '') {
      $config_list = get_option('oppia_config_list');
      $config_settings = [];
      $config_settings['api_key'] = esc_js(get_option('oppia_api_key'));
      $config_settings['sections'] = get_option('oppia_sections');
      $config_settings['section_quantities'] = get_option('oppia_section_qty');
      $config_settings['list_size'] = sanitize_text_field(get_option('oppia_list_size'));
      $config_settings['title'] = sanitize_text_field(get_option('oppia_title'));
      $config_settings['before_title'] = esc_html(get_option('oppia_beforetitle'));
      $config_settings['after_title'] = esc_html(get_option('oppia_aftertitle'));
      $config_settings['before_widget'] = esc_html(get_option('oppia_beforewidget'));
      $config_settings['after_widget'] = esc_html(get_option('oppia_afterwidget'));
      $config_settings['widget_height'] = esc_html(get_option('oppia_widget_height'));
      $config_list[$option] = $config_settings;
      update_option('oppia_config_list', $config_list, 'yes' );
    }
  } else {
    $option = $is_new_set;
  }
  return $option;
}

function oppia_int_after_delete_config($option) {

  $config_list = get_option('oppia_config_list');
  unset($config_list[$_REQUEST['config_to_delete']]);
  update_option('oppia_config_list', $config_list, 'yes' );
  reset($config_list);
  $key = key($config_list);
  return $key;
}

function oppia_sanitize_api_key($option) {
  if($option == '') {
    add_settings_error(
      'oppia_api_key',
      esc_attr( 'settings_updated' ),
      esc_attr( 'The Oppia API key is not set'),
      esc_attr( 'error')
    );
  }
  return esc_js($option);
}

function oppia_sanitize_int($option) {
  if((int)$option == 0) {
    return '';
  }
  return (int)$option;
}

function oppia_sanitize_qty($option) {
  $is_valid = oppia_validate_section_values();

  if(is_array($option)) {
    foreach ($option as $key=>$val) {
      if((int)$val > 0) {
        $option[$key] = (int)$val;
      } else {
        $option[$key] = '';
      }

    }
  }
  if($is_valid !== TRUE) {
    add_settings_error(
      'oppia_sanitize_sections',
      esc_attr( 'settings_updated' ),
      'Sections are not correctly filled',
      'error'
    );
    return FALSE;
  }
  return $option;
}

function oppia_sanitize_sections($option) {
  $is_valid = oppia_validate_section_values();
  $message = 'Sections are not correctly filled';
  if($is_valid !== TRUE) {
    if(is_array($is_valid)) {
      $message = $is_valid['error'];
      if(is_array($is_valid['field'])) {
        $message .= '. Fields with errors: ' . join(', ',array_keys($is_valid['field']));
      }
    }
    add_settings_error(
      'oppia_sanitize_sections',
      esc_attr( 'settings_updated' ),
      $message,
      'error'
    );
    return FALSE;
  }
  return $option;
}

function oppia_sanitize_title($option) {

  return sanitize_text_field($option);
}

function oppia_sanitize_formatting($option) {
  return esc_html(esc_js($option));
}


function oppia_validate_section_values() {

  $post_data = $_POST;

  // check for sections values are filled
  $oppia_section_qty = $post_data['oppia_section_qty'];
  $oppia_sections = $post_data['oppia_sections'];
  $err_array  = array();
  $valid = false;

  if(isset($oppia_sections) && is_array($oppia_sections) && isset($oppia_section_qty) && is_array($oppia_section_qty)) {
    foreach($oppia_sections as $key=>$val) {
      if($val != '') {
        if(in_array($key, array_keys($oppia_section_qty)) && (int)$oppia_section_qty[$key] > 0) {
          $err_array[sanitize_text_field($key)]=true;

        } else {
          $err_array[sanitize_text_field($key)]=false;
        }
      }
    }
    // fill up array with error fields
    if(in_array(false,$err_array)) {
      return ['field'=>$err_array,'valid' => false, 'error'=>'The Quantity value is not set for enabled section'];
    }

    $valid = true;
  } else {
    return ['field'=>'oppia_sections','valid'=>false, 'error'=>'Sections are not set'];
  }
  return $valid;

}

function oppia_validate_configuration_name() {

  $post_data = $_POST;

  $valid = false;

  // check for configuration name
  if(isset($post_data['oppia_configs']) || isset($post_data['oppia_newconfig'])) {
    if($post_data['oppia_configs'] != '' || $post_data['oppia_newconfig'] != '') {
      $valid = true;
    } else {
      return ['field'=>'oppia_configs','valid'=>false, 'error'=>'The config name is not set'];
    }
  }

  return $valid;
}

function oppia_int_register_settings() {
  /**
   * @TODO: use update_settings for different configurations.
   *    pass config in shortcuts
   */

  if(isset($_POST['action']) && $_POST['action'] == 'update' && current_user_can('manage_options')) {
    check_admin_referer('oppia-group-options','_wpnonce');
      register_setting( 'oppia-group', 'oppia_api_key', ['description'=>'Oppia API key', 'type'=>'string', 'sanitize_callback' => 'oppia_sanitize_api_key']);
      register_setting( 'oppia-group', 'oppia_title', ['sanitize_callback' => 'oppia_sanitize_title'] );
      register_setting( 'oppia-group', 'oppia_beforetitle',['sanitize_callback' => 'oppia_sanitize_formatting'] );
      register_setting( 'oppia-group', 'oppia_aftertitle',['sanitize_callback' => 'oppia_sanitize_formatting'] );
      register_setting( 'oppia-group', 'oppia_beforewidget',['sanitize_callback' => 'oppia_sanitize_formatting'] );
      register_setting( 'oppia-group', 'oppia_afterwidget',['sanitize_callback' => 'oppia_sanitize_formatting'] );
      register_setting( 'oppia-group', 'oppia_widget_height',['sanitize_callback' => 'oppia_sanitize_int'] );
      register_setting( 'oppia-group', 'oppia_sections',['sanitize_callback' => 'oppia_sanitize_sections']);
      register_setting( 'oppia-group', 'oppia_section_qty',['sanitize_callback' => 'oppia_sanitize_qty']);
      register_setting( 'oppia-group', 'oppia_newconfig', ['sanitize_callback'=>'oppia_int_add_config_to_list'] );
      register_setting( 'oppia-group', 'oppia_configs', ['sanitize_callback'=>'oppia_int_update_config']  );

  }

  if(isset($_REQUEST['delete']) && current_user_can('manage_options')) {
    check_admin_referer('oppia-group-options','_wpnonce');
    register_setting( 'oppia-group', 'oppia_configs', ['sanitize_callback'=>'oppia_int_after_delete_config']);
  }


}
