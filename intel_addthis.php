<?php
/**
* Intelligence Addthis bootstrap file
*
* This file is read by WordPress to generate the plugin information in the plugin
* admin area. This file also includes all of the dependencies used by the plugin,
* registers the activation and deactivation functions, and defines a function
* that starts the plugin.
*
* @link              getlevelten.com/blog/tom
* @since             1.0.0
* @package           Intelligence
*
* @wordpress-plugin
* Plugin Name:       Intelligence Addthis
* Plugin URI:        https://wordpress.org/plugins/intelligence-example
* Description:       Example Intelligence add-on plugin.
* Version:           1.0.0.0-dev
* Author:            LevelTen
* Author URI:        https://intelligencewp.com
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       intel_example
* Domain Path:       /languages
* GitHub Plugin URI: https://github.com/levelten/wp-addthis-intelligence
*/

/**
 * This is an starter / example Intelligence add-on plugin. It is designed to
 * extend the fictional "Example plugin".
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

define('INTEL_ADDTHIS_VER', '1.0.0.0-dev');

/**
 * Class Intel_Example
 */
final class Intel_Addthis {

  protected $version = INTEL_ADDTHIS_VER;

  public $plugin_info = array();

  public $plugin_un = 'intel_addthis';

  /**
   * @var Intel_Addthis
   * @since 1.0.0
   */
  private static $instance;

  /**
   * Plugin Directory
   *
   * @since 1.0.0
   * @var string $dir
   */
  public $dir = '';

  /**
   * Plugin URL
   *
   * @since 1.0.0
   * @var string $url
   */
  public $url = '';

  /**
   * Main Plugin Instance
   *
   * Insures that only one instance of a plugin class exists in memory at any one
   * time. Also prevents needing to define globals all over the place.
   *
   * @since 1.0.2
   * @static
   * @static var array $instance
   * @return Intel_Addthis Instance
   */
  public static function instance($options = array()) {
    if (null === static::$instance) {
      static::$instance = new static($options);
    }

    return static::$instance;
  }

  /**
   * constructor.
   *
   */
  public function __construct() {
    global $wp;

    $this->plugin_info = $this->intel_plugin_info();

    $this->dir = plugin_dir_path(__FILE__);

    $this->url = plugin_dir_url(__FILE__);

    // Register hook_admin_menu()
    //add_filter('admin_menu', array( $this, 'admin_menu' ));

    /*
     * Intelligence hooks
     */

    // Register hook_intel_system_info()
    add_filter('intel_system_info', array( $this, 'intel_system_info' ));

    // Registers hook_wp_loaded()
    //add_action('wp_loaded', array( $this, 'wp_loaded' ));
    
    // add _intel_intel_script_info to hook_intel_script_info
    add_filter('intel_intel_script_info', array( $this,'_intel_intel_script_info'));
    
    // Register hook_intel_intel_event_info
    add_filter('intel_intel_event_info', array( $this,'intel_addthis_intel_intel_event_info'));

    // Register hook_intel_menu()
    add_filter('intel_menu', array( $this, 'intel_menu' ));

    // Register hook_intel_demo_pages()
    add_filter('intel_demo_posts', array( $this, 'intel_demo_posts' ));

  }

  /**
   * Implements hook_wp_loaded()
   *
   * Used to check if Intel is not loaded and include setup process if needed.
   * Alternatively this check can be done in hook_admin_menu() if the plugin
   * implements hook_admin_menu()
   */
  public function wp_loaded() {
    // check if Intel is installed, add setup processing if not
    if (!$this->is_intel_installed()) {
      require_once( $this->dir . $this->plugin_un . '.setup.inc' );
    }
  }

  /**
   * Returns if Intelligence plugin is installed
   * @param string $level
   * @return mixed
   */
  public function is_intel_installed($level = 'min') {
    static $flags = array();
    if (!isset($flags[$level])) {
      $flags[$level] = (is_callable('intel_is_installed')) ? intel_is_installed($level) : FALSE;
    }
    return $flags[$level];
  }

  /**
   * Provides plugin data for hook_intel_system_info
   * @param array $info
   * @return array
   */
  function intel_plugin_info($info = array()) {
    $info = array(
      // The unique name for this plugin
      'plugin_un' => $this->plugin_un,
      // Title of the plugin
      'plugin_title' => __('Intelligence Addthis Add-On', $this->plugin_un),
      // Shorter version of title used when reduced characters are desired
      'plugin_title_short' => __('Intelligence Addthis', $this->plugin_un),
      // Main plugin file
      //TODO
      'plugin_file' => 'intel_addthis.php', // Main plugin file
      // The server path to the plugin files directory
      'plugin_dir' => $this->dir,
      // The browser path to the plugin files directory
      'plugin_url' => $this->url,
      // The install file for the plugin if different than [plugin_un].install
      // Used to auto discover database updates
      'update_file' => 'intel_addthis.install', // default [plugin_un].install
      // If this plugin extends a plugin other than Intelligience, include that
      // plugin's info in 'extends_' properties
      // The extends plugin unique name
      'extends_plugin_un' => 'addthis',
      // the extends plugin title
      'extends_plugin_title' => __('Addthis', 'addthis'),
    );
    return $info;
  }

  /**
   * Implements hook_intel_system_info()
   *
   * Registers plugin with intel_system
   *
   * @param array $info
   * @return array
   */
  function intel_system_info($info = array()) {
    // array of plugin info indexed by plugin_un
    $info[$this->plugin_un] = $this->intel_plugin_info();
    return $info;
  }

  /**
   * Implements hook_intel_menu()
   *
   * @param array $items
   * @return array
   */
  public function intel_menu($items = array()) {
    // route for Admin > Intelligence > Settings > Setup > Addthis
    $items['admin/config/intel/settings/setup/' . $this->plugin_un] = array(
      'title' => 'Setup',
      'description' => Intel_Df::t('Intelligence Addthis initial plugin setup'),
      'page callback' => $this->plugin_un . '_admin_setup_page',
      'access callback' => 'user_access',
      'access arguments' => array('admin intel'),
      'type' => Intel_Df::MENU_LOCAL_TASK,
      'file' => 'admin/' . $this->plugin_un . '.admin_setup.inc',
      'file path' => $this->dir,
    );
    // rout for Admin > Intelligence > Help > Demo > Example
    // $items['admin/help/demo/' . $this->plugin_un] = array(
    //   'title' => $this->plugin_info['extends_plugin_title'],
    //   'page callback' => array($this, 'intel_admin_help_demo_page'),
    //   'access callback' => 'user_access',
    //   'access arguments' => array('admin intel'),
    //   'type' => Intel_Df::MENU_LOCAL_TASK,
    //   'weight' => 10,
    // );
    return $items;
  }
  
  /**
   * Implements hook_intel_intel_script_info
   */
  function _intel_intel_script_info($info = array()) {
    $info['addthis'] = array(
      'title' => Intel_Df::t('AddThis (TESTING)'),
      'description' => Intel_Df::t('Tracks AddThis shares and clickbacks.'),
      'path' => $this->url . 'js/l10intel_addthis.js',
      'enabled' => 1,
      'selectable' => 0,
    );

    return $info;
  }
  
  /**
   * Implements hook_intel_intel_event_info
   */
  function intel_addthis_intel_intel_event_info($event = array()) {
    $event['intel_addthis_share_click'] = array(
      'title' => Intel_Df::t('AddThis share click'),
      //'category' => Intel_Df::t('Social share'),
      'description' => Intel_Df::t('Click on AddThis share button'),
      'mode' => 'valued',
      //'valued_event' => 1,
      'value' => 10,
      //'selector' => '.io-social-share-track',
      'on_event' => 'click',
      'enable' => 1,
      'overridable' => array(
        'selector' => 1,
      ),
      'social_action' => 'share',
      'trigger_alter_callback' => 'intel_addthis:eventHandlerAlter',
      'trigger_callback' => 'intel_addthis:eventHandler',
      //'js_setting' => 1,
    );
    
    $event['intel_addthis_follow_click'] = array(
      'title' => Intel_Df::t('AddThis follow click'),
      //'category' => Intel_Df::t('Social share'),
      'description' => Intel_Df::t('Click on AddThis follow button'),
      'mode' => 'valued',
      //'valued_event' => 1,
      'value' => 10,
      //'selector' => '.io-social-share-track',
      'on_event' => 'click',
      'enable' => 1,
      'overridable' => array(
        'selector' => 1,
      ),
      'social_action' => 'follow',
      'trigger_alter_callback' => 'intel_addthis:eventHandlerAlter',
      'trigger_callback' => 'intel_addthis:eventHandler',
      //'js_setting' => 1,
    );
    $event['intel_addthis_clickback_click'] = array(
      'title' => Intel_Df::t('AddThis clickback'),
      //'category' => Intel_Df::t('Social share'),
      'description' => Intel_Df::t('Clickback from Addthis'),
      'mode' => 'valued',
      //'valued_event' => 1,
      'value' => 10,
      //'selector' => '.io-social-share-track',
      'on_event' => 'click',
      'enable' => 1,
      'overridable' => array(
        'selector' => 1,
      ),
      'social_action' => 'clickback',
      'trigger_alter_callback' => 'intel_addthis:eventHandlerAlter',
      'trigger_callback' => 'intel_addthis:eventHandler',
      //'js_setting' => 1,
    );
    
    return $event;
  }

  /*
   * Provides an Intelligence > Help > Demo > Example page
   */
  function intel_admin_help_demo_page() {
    $output = '';

    $demo_mode = get_option('intel_demo_mode', 0);

    /*
    if (empty($demo_mode)) {
      $msg = Intel_Df::t('Demo is currently disabled for non logged in users. Go to demo settings to enable.');
      Intel_Df::drupal_set_message($msg, 'warning');
    }
    */

    $output .= '<div class="card">';
    $output .= '<div class="card-block clearfix">';

    $output .= '<p class="lead">';
    $output .= Intel_Df::t('Try out Example tracking!');
    //$output .= ' ' . Intel_Df::t('This tutorial will walk you through the essentials of extending Google Analytics using Intelligence to create results oriented analytics.');
    $output .= '</p>';

    /*
    $l_options = Intel_Df::l_options_add_class('btn btn-info');
    $l_options = Intel_Df::l_options_add_destination(Intel_Df::current_path(), $l_options);
    $output .= Intel_Df::l( Intel_Df::t('Demo settings'), 'admin/config/intel/settings/general/demo', $l_options) . '<br><br>';
    */

    $output .= '<div class="row">';
    $output .= '<div class="col-md-6">';
    $output .= '<p>';
    $output .= '<h3>' . Intel_Df::t('First') . '</h3>';
    $output .= __('Launch Google Analytics to see conversions in real-time:', $this->plugin_un);
    $output .= '</p>';

    $output .= '<div>';
    $l_options = Intel_Df::l_options_add_target('ga');
    $l_options = Intel_Df::l_options_add_class('btn btn-info m-b-_5', $l_options);
    $url = 	$url = intel_get_ga_report_url('rt_goal');
    $output .= Intel_Df::l( Intel_Df::t('View real-time conversion goals'), $url, $l_options);

    $output .= '<br>';

    $l_options = Intel_Df::l_options_add_target('ga');
    $l_options = Intel_Df::l_options_add_class('btn btn-info m-b-_5', $l_options);
    $url = 	$url = intel_get_ga_report_url('rt_event');
    $output .= Intel_Df::l( Intel_Df::t('View real-time events'), $url, $l_options);
    $output .= '</div>';
    $output .= '</div>'; // end col-x-6

    $output .= '<div class="col-md-6">';

    $output .= '<p>';
    $output .= '<h3>' . Intel_Df::t('Next') . '</h3>';
    $output .= __('Pick one of your forms to test:', $this->plugin_un);
    $output .= '</p>';

    $forms = $this->intel_form_type_form_info();

    $l_options = Intel_Df::l_options_add_target('example_demo');
    $l_options = Intel_Df::l_options_add_class('btn btn-info m-b-_5', $l_options);
    $l_options['query'] = array();
    $output .= '<div>';
    foreach ($forms as $form) {
      $l_options['query']['fid'] = $form['id'];
      $output .= Intel_Df::l( __('Try', $this->plugin_un) . ': ' . $form['title'], 'intelligence/demo/' . $this->plugin_un, $l_options);
      $output .= '<br>';
    }
    $output .= '</div>';

    $output .= '</div>'; // end col-x-6
    $output .= '</div>'; // end row

    $output .= '</div>'; // end card-block
    $output .= '</div>'; // end card

    return $output;
  }

  /**
   * Implements hook_intel_demo_pages()
   *
   * Adds a demo page to test tracking for this plugin.
   *
   * @param array $posts
   * @return array
   */
  function intel_demo_posts($posts = array()) {
    $id = -1 * (count($posts) + 1);

    $forms = [];//$this->intel_form_type_form_info();

    $content = '';
    if (!empty($_GET['fid']) && !empty($forms[$_GET['fid']])) {
      $form = $forms[$_GET['fid']];
      $content .= '<br><h3>' . $form['title'] . ':</h3>';
      $content .= '[example-form id="' . $form['id'] . '" title="' . $form['title'] . '"]';
    }
    elseif (!empty($forms)) {
      $form = array_shift($forms);
      $content .= '<br><h3>' . $form['title'] . ':</h3>';
      $content .= '[example-form id="' . $form['id'] . '" title="' . $form['title'] . '"]';
    }
    else {
      $content = __('No Example forms were found', $this->plugin_un);
    }
    $posts["$id"] = array(
      'ID' => $id,
      'post_type' => 'page',
      'post_title' => 'Demo Example',
      'post_content' => $content,
      'intel_demo' => array(
        'url' => 'intelligence/demo/' . $this->plugin_un,
      ),
    );

    return $posts;
  }
}

function intel_addthis() {
  return Intel_addthis::instance();
}
global $intel_addthis;
$intel_addthis = intel_addthis();

/*
 * Implements hook_register_activation_hook()
 *
 * The code that runs during plugin activation.
 *
 * Initializes Intel's database schema update system
 */
function _intel_addthis_activation() {
  // plugin specific installation code.
  // initializes data for plugin when first installed
  require_once plugin_dir_path( __FILE__ ) . 'intel_addthis.install';
  intel_addthis_install();

  // check if Intel is active
  if (is_callable('intel_activate_plugin')) {
    // initializes Intel's database update management system
    intel_activate_plugin('intel_addthis');
  }
}
register_activation_hook( __FILE__, '_intel_addthis_activation' );

/**
 * Implements hook_register_deactivation_hook()
 *
 * The code that runs during plugin deactivation.
 */
function _intel_addthis_deactivate() {

}
register_deactivation_hook( __FILE__, '_intel_addthis_deactivate' );

/*
 * Implements hook_register_uninstall_hook()
 *
 * Runs when plugin is Deleted (uninstalled)
 */
function _intel_addthis_uninstall() {
  // plugin specific installation code.
  // remove plugin data from database before plugin is uninstalled
  require_once plugin_dir_path( __FILE__ ) . 'intel_addthis.install';
  intel_addthis_uninstall();
}
register_uninstall_hook( __FILE__, '_intel_addthis_uninstall' );
