<?php

define( 'OPPIA_CONTENT_FETCHER_DOMAIN', 'oppia-integration' );
require_once('oppia_helpers.php');

class Oppia_Content_Fetcher_Admin {
  protected static $instance = null;

  private function __construct() {
    $this->plugin_slug = OPPIA_CONTENT_FETCHER_DOMAIN;
  }

  public static function get_instance() {

    // If the single instance hasn't been set, set it now.
    if ( null == self::$instance ) {
      self::$instance = new self;
    }

    return self::$instance;
  }




  public static function oppia_settings(){

    if(!current_user_can('manage_options')) {
      echo '<h2>You have no access to this page</h2><br><a href="/">Homepage</a>';
      return ;
    }

    $active_tab = 'selection_settings';

    if( isset( $_GET[ 'tab' ] ) ) {
      $active_tab = sanitize_text_field($_GET[ 'tab' ]);
    }
    $selected_config = sanitize_text_field(get_option('oppia_configs'));
    $config_list = get_option('oppia_config_list');
    if( isset( $_GET['config'] ) && $selected_config != '') {
        if(isset($config_list[$_GET['config']])) {
          $selected_config = sanitize_text_field($_GET['config']);
        }
    }

    $selected_section = $config_list[$selected_config]['sections'];
    $quantitites = $config_list[$selected_config]['section_quantities'];
    $api_key = isset($config_list[$selected_config]['api_key'])?$config_list[$selected_config]['api_key']:get_option('oppia_api_key');
    $sections = [];
    $request = Oppia_Content_Fetcher::get_sections($api_key) ;
    $lang = 'name_'.(get_locale() == 'fi_FI' ? 'fi':'en');

    foreach($request as $obj) {
        $sections[$obj->code] = $obj->$lang;
    }
    $list_size=$config_list[$selected_config]['list_size'];
    $title=$config_list[$selected_config]['title'];
    $before_title=$config_list[$selected_config]['before_title'];
    $after_title=$config_list[$selected_config]['after_title'];
    $before_widget=$config_list[$selected_config]['before_widget'];
    $after_widget=$config_list[$selected_config]['after_widget'];
    $widget_height = $config_list[$selected_config]['widget_height'];

    $handle ='content-fetcher-admin-js';
    wp_register_script($handle , OPPIA_INT_PLUGIN_URL.'assets/js/admin.js' , '', '', true );
    wp_enqueue_script($handle);
    wp_localize_script( $handle, 'php_vars', array('config_list'=>$config_list, 'selected_config'=>$selected_config) );

    ?>
      <div class="wrap">
          <h1>Oppia integration plugin settings</h1>
          <p>For page content, text widget... <input
                      id="shortcode_text"
                      class="form-control"
                      style="width: 300px;background-color: #ADFFAD;margin-right: 50px;"
                      type="text"
                      value="[oppia_integration id=&quot;<?php echo $selected_config?>&quot;]"
                      onclick="this.select()"
                      readonly=""></p>
          <h2 class="nav-tab-wrapper" id="option_tabs">
              <a id="selection_settings_tab" href="?page=oppia&tab=selection_settings&config=<?php echo $selected_config?>" class="nav-tab <?php echo $active_tab == 'selection_settings' ? 'nav-tab-active' : ''; ?>">Selection settings</a>
              <a id="display_options_tab" href="?page=oppia&tab=display_options&config=<?php echo $selected_config?>" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>">Display Options</a>
          </h2>
        <?php settings_errors(); ?>
          <form class="form-horizontal" method="post" action="options.php" id="oppia-fetcher-settings-form">
            <?php settings_fields( 'oppia-group' ); ?>
              <div>
                  <div class="form-group row">
                      <div class="col-4" >
                          <label >Select a previous configuration :</label>
                      </div>
                      <div class="col-8" >
                          <select  name="oppia_configs" id="oppia_fetcher_configs">
                            <?php
                            $current_selected = $selected_config;
                            foreach ($config_list as $key=>$item) {
                              $selected = '';
                              if($current_selected == $key) {
                                $selected = ' selected ';
                              }
                              $option = '<option value="'.esc_html($key).'"' . $selected . '>';
                              print $option.esc_html($key).'</option>';
                            }
                            ?>
                          </select>
                      </div>

                  </div>
                  <div class="form-group row">
                      <div class="col-4" >
                          <label>
                              ...or create a new one:
                          </label>
                      </div>
                      <div class="col-8" >
                          <input type="text" name="oppia_newconfig" >
                      </div>
                  </div>
              </div>
            <?php if($active_tab == 'display_options'): ?>

              <div style="display: none">
                <?php else: ?>
                  <div>
                    <?php endif; ?>
                      <div class="form-group row">
                          <div class="col-4">
                              <label>Customer API-key</label>
                          </div>
                          <div class="col-8">
                              <input  type="text" name="oppia_api_key" value="<?php echo esc_html($api_key)?>"  id="api_key">
                          </div>
                      </div>

                      <div class="form-group row module-selection-group">
                          <div class="col-4">
                              <label >Module selection</label>
                          </div>
                          <div class="col-8">
                                <?php self::buildSelector($sections, $quantitites, $selected_section); ?>
                          </div>
                      </div>
                  </div>
                <?php if($active_tab == 'selection_settings'): ?>
                  <div style="display: none">
                    <?php else : ?>
                      <div>
                        <?php endif; ?>
                          <div class="form-group row">
                              <div class="col-4" >
                                  <label>Title</label>
                              </div>
                              <div class="col-8" >
                                  <input  type="text" name="oppia_title" value="<?php echo esc_html($title)?>"  id="title">
                              </div>
                          </div>
                          <div class="form-group row">
                              <div class="col-4" >
                                  <label>Before Title</label>
                              </div>
                              <div class="col-8" >
                                  <input  type="text" name="oppia_beforetitle" value="<?php echo esc_html($before_title)?>" id="before_title">
                              </div>
                          </div>
                          <div class="form-group row">
                              <div class="col-4" >
                                  <label>After Title</label>
                              </div>
                              <div class="col-8" >
                                  <input  type="text" name="oppia_aftertitle" value="<?php echo esc_html($after_title)?>" id="after_title">
                              </div>
                          </div>
                          <div class="form-group row">
                              <div class="col-4" >
                                  <label>Before widget</label>
                              </div>
                              <div class="col-8" >
                                  <input  type="text" name="oppia_beforewidget" value="<?php echo esc_html($before_widget)?>" id="before_widget">
                              </div>
                          </div>
                          <div class="form-group row">
                              <div class="col-4" >
                                  <label>After widget</label>
                              </div>
                              <div class="col-8" >
                                  <input  type="text" name="oppia_afterwidget" value="<?php echo esc_html($after_widget)?>" id="after_widget">
                              </div>
                          </div>
                          <div class="form-group row">
                              <div class="col-4" >
                                  <label>Widget height</label>
                              </div>
                              <div class="col-8" >
                                  <input  type="text" name="oppia_widget_height" value="<?php echo esc_html($widget_height)?>" id="widget_height">
                              </div>
                          </div>
                          <input type="hidden" name="config_to_delete" id="config_to_delete">
                      </div>
                    <?php
                    wp_nonce_field( 'oppia-group-options' );
                    /**
                     * @todo Validate and sanitize data
                     */
                    do_settings_sections( 'oppia-group' ); ?>
                    <?php submit_button(null, 'primary', 'submit', false); submit_button('Delete config', '', 'delete', false);?>
          </form>


      </div>
    <?php

  }

  /**
   * function for build modules selector
   * @param $sections
   * @param $quantitites
   * @param $selected_section
   */
  private function buildSelector($sections, $quantitites, $selected_section) {

      ?>
    <div id="modules-list-conatiner" class="tabs-panel">
        <ul id="module-list">
    <?php
    foreach ($sections as $key=>$object) {
      $selected = '';
      if(gettype($selected_section) == 'array') {
        if(in_array($key,$selected_section)) {
          $selected = ' checked="checked" ';
        }
      } else {
        if($key === $selected_section) {
          $selected = ' checked="checked" ';
        }
      }

    ?>
      <li class="module-selection-item" id="module-item-<?php echo $key ?>">
          <label class="select-it">
              <input value="<?php echo $key?>" type="checkbox" name="oppia_sections[<?php echo $key?>]" id="section-<?php echo esc_html($key)?>"
                     <?php echo esc_html($selected) ?>>
              <?php echo $object ?>

          </label>
          <div class="quantity-enter">
              <input
                      type="text"
                      name="oppia_section_qty[<?php echo $key?>]"
                      id="section_quantity_<?php echo $key ?>"
                      class="input-number"
                      value="<?php echo isset($quantitites[$key]) ? esc_html($quantitites[$key]):'';?>"
              >
          </div>

      </li>
      <?php
    }
    ?>
        </ul>
    </div>
    <?php
  }



}
