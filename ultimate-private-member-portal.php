<?php
/*
  Plugin Name: Private Client Boards
  Plugin URI: http://thowzif.com
  Description: Advanced private page functionaity for WordPress with advanced private content, private discussions,
  private files and custom content tabs
  Version: 1.0
  Author: Abdullah Thowzif Hameed
  Author URI: http://www.thowzif.com
 */



// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

register_activation_hook( __FILE__, 'pcb_install_db_tables' );

function pcb_get_plugin_version() {
    $default_headers = array('Version' => 'Version');
    $plugin_data = get_file_data(__FILE__, $default_headers, 'plugin');
    return $plugin_data['Version'];
}

/* Intializing the plugin on plugins_loaded action */
add_action( 'plugins_loaded', 'pcb_plugin_init' );

function pcb_plugin_init(){
    Private_Client_Boards();
}

/* Install database tables required for the plugin */
function pcb_install_db_tables(){
    global $wpdb;

    $table_name = $wpdb->prefix . 'pcb_private_page';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
              id int(11) NOT NULL AUTO_INCREMENT,
              user_id int(11) NOT NULL,
              content longtext NOT NULL,
              type varchar(20) NOT NULL,
              updated_at datetime NOT NULL,
              tab_id int(11) NOT NULL,
              PRIMARY KEY (id)
            );";

    $table_private_page_messages = $wpdb->prefix . 'pcb_private_page_messages';

    $sql_private_page_messages = "CREATE TABLE IF NOT EXISTS $table_private_page_messages (
              id int(11) NOT NULL AUTO_INCREMENT,
              message longtext NOT NULL,
              admin_status varchar(255) NOT NULL,
              user_id int(11) NOT NULL,
              parent_message_id int(11) NOT NULL,
              updated_at datetime NOT NULL,
              user_read_status varchar(255) NOT NULL,
              admin_read_status varchar(255) NOT NULL,
              tab_id int(11) NOT NULL,
              PRIMARY KEY (id)
            );";

    $table_private_page_files = $wpdb->prefix . 'pcb_private_page_files';

    $sql_private_page_files = "CREATE TABLE IF NOT EXISTS $table_private_page_files (
              id int(11) NOT NULL AUTO_INCREMENT,
              file_name varchar(255) NOT NULL,
              description longtext NOT NULL,
              user_id int(11) NOT NULL,
              file_path longtext NOT NULL,
              updated_at datetime NOT NULL,
              status varchar(255) NOT NULL,
              admin_status varchar(255) NOT NULL,
              user_read_status varchar(255) NOT NULL,
              admin_read_status varchar(255) NOT NULL,
              tab_id int(11) NOT NULL,
              PRIMARY KEY (id)
            );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    dbDelta( $sql_private_page_messages );
    dbDelta( $sql_private_page_files );
}

if( !class_exists( 'Private_Client_Boards' ) ) {
    
    class Private_Client_Boards{
    
        private static $instance;

        /* Create instances of plugin classes and initializing the features  */
        public static function instance() {
            
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Private_Client_Boards ) ) {
                self::$instance = new Private_Client_Boards();
                self::$instance->setup_constants();

                add_action('wp_enqueue_scripts',array(self::$instance,'load_scripts'),9);
                add_action('admin_enqueue_scripts',array(self::$instance,'load_admin_scripts'),9);
                

                add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
                self::$instance->includes();

                add_action('init', array( self::$instance, 'init_actions' ) );                
                 
                self::$instance->settings           = new pcb_Settings();
                self::$instance->template_loader    = new pcb_Template_Loader();
                self::$instance->roles_capability   = new pcb_Roles_Capability();
                self::$instance->posts              = new pcb_Posts();                
                self::$instance->private_page       = new pcb_Private_Page();
                self::$instance->private_tabs       = new pcb_Private_Tabs();
                
                add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( self::$instance, 'plugin_listing_links' )  );
                
            }
            return self::$instance;
        }

        public function init_actions(){
            self::$instance->private_content_settings  = get_option('pcb_options');
        }

        /* Setup constants for the plugin */
        private function setup_constants() {
            
            // Plugin version
            if ( ! defined( 'pcb_VERSION' ) ) {
                define( 'pcb_VERSION', '1.1' );
            }

            // Plugin Folder Path
            if ( ! defined( 'pcb_PLUGIN_DIR' ) ) {
                define( 'pcb_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }

            // Plugin Folder URL
            if ( ! defined( 'pcb_PLUGIN_URL' ) ) {
                define( 'pcb_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
            
            if ( ! defined( 'pcb_PRIVATE_CONTENT_TABLE' ) ) {
                define( 'pcb_PRIVATE_CONTENT_TABLE', 'pcb_private_page' );
            }

            if ( ! defined( 'pcb_PRIVATE_PAGE_TABS_POST_TYPE' ) ) {
                define( 'pcb_PRIVATE_PAGE_TABS_POST_TYPE', 'pcb_portal_tab' );
            }

            if ( ! defined( 'pcb_ADMIN_GRAVATAR_EMAIL' ) ) {
                define( 'pcb_ADMIN_GRAVATAR_EMAIL', get_option('admin_email') );
            }
        }
             
        /* Define the locations for template files */  
        public function template_loader_locations($locations){
            $location = trailingslashit( pcb_PLUGIN_DIR ) . 'templates/';
            array_push($locations,$location);
            return $locations;
        }
        
        /* Include class files */
        private function includes() {

            require_once pcb_PLUGIN_DIR . 'classes/class-upmp-settings.php';
            require_once pcb_PLUGIN_DIR . 'classes/class-upmp-template-loader.php';
            require_once pcb_PLUGIN_DIR . 'classes/class-upmp-private-page.php';
            require_once pcb_PLUGIN_DIR . 'classes/class-upmp-roles-capability.php';
            require_once pcb_PLUGIN_DIR . 'classes/class-upmp-posts.php';
            require_once pcb_PLUGIN_DIR . 'classes/class-upmp-private-tabs.php';
            require_once pcb_PLUGIN_DIR . 'functions.php';

            require_once pcb_PLUGIN_DIR . 'addons/contact_form_7/contact_form_7.php';
            
            if ( is_admin() ) {}
        }

        public function load_scripts(){

            wp_register_style('pcb_select2_css', pcb_PLUGIN_URL . 'js/select2/upmp-select2.min.css');
            wp_enqueue_style('pcb_select2_css');

            wp_register_style('pcb_private_page_css', pcb_PLUGIN_URL . 'css/upmp-private-page.css');
            wp_enqueue_style('pcb_private_page_css');

            wp_register_style('pcb_front_css', pcb_PLUGIN_URL . 'css/upmp-front.css');
            wp_enqueue_style('pcb_front_css');

            wp_register_script('pcb_select2_js', pcb_PLUGIN_URL . 'js/select2/upmp-select2.min.js');
            wp_enqueue_script('pcb_select2_js');

            wp_register_script('pcb_private_page_js', pcb_PLUGIN_URL . 'js/upmp-private-page.js', array('jquery'));
            wp_enqueue_script('pcb_private_page_js');

            $custom_js_strings = array(        
                'AdminAjax' => admin_url('admin-ajax.php'),
                'images_path' =>  pcb_PLUGIN_URL . 'images/',
                'Messages'  => array(
                                    'userEmpty' => __('Please select a user.','upmp'),
                                    'addToPost' => __('Add to Post','upmp'), 
                                    'insertToPost' => __('Insert Files to Post','upmp'),   
                                    'removeGroupUser' => __('Removing User...','upmp'), 
                                    'fileNameRequired' => __('File Name is required.','upmp'),
                                    'fileRequired' => __('File is required.','upmp'),
                                    'confirmDelete' => __('This message and comments will be deleted and you won\'t be able to find it anymore.','upmp'),
                                    'messageEmpty' => __('Please enter a message.','uupm'),
                                    'selectMember' => __('Select a member','uupm'),

                                ),

                'nonce' => wp_create_nonce('upmp-private-page'),   
            );

            wp_localize_script('pcb_private_page_js', 'UPMPPage', $custom_js_strings);

        }

        public function load_admin_scripts(){

            wp_register_style('pcb_select2_css', pcb_PLUGIN_URL . 'js/select2/upmp-select2.min.css');
            wp_enqueue_style('pcb_select2_css');

            wp_register_style('pcb_private_page_css', pcb_PLUGIN_URL . 'css/upmp-private-page.css');
            wp_enqueue_style('pcb_private_page_css');

            wp_register_script('pcb_select2_js', pcb_PLUGIN_URL . 'js/select2/upmp-select2.min.js');
            wp_enqueue_script('pcb_select2_js');

            wp_register_script('pcb_private_page_js', pcb_PLUGIN_URL . 'js/upmp-private-page.js', array('jquery'));
            wp_enqueue_script('pcb_private_page_js');

            $custom_js_strings = array(        
                'AdminAjax' => admin_url('admin-ajax.php'),
                'images_path' =>  pcb_PLUGIN_URL . 'images/',
                'Messages'  => array(
                                    'userEmpty' => __('Please select a user.','upmp'),
                                    'addToPost' => __('Add to Post','upmp'), 
                                    'insertToPost' => __('Insert Files to Post','upmp'),   
                                    'removeGroupUser' => __('Removing User...','upmp'), 
                                    'fileNameRequired' => __('File Name is required.','upmp'),
                                    'fileRequired' => __('File is required.','upmp'),
                                    'confirmDelete' => __('This message and comments will be deleted and you won\'t be able to find it anymore.','upmp'),
                                    'messageEmpty' => __('Please enter a message.','uupm'),
                                    'selectMember' => __('Select a member','uupm'),

                                ),

                'nonce' => wp_create_nonce('upmp-private-page'),   
            );

            wp_localize_script('pcb_private_page_js', 'UPMPPage', $custom_js_strings);
            
        }

        public function plugin_listing_links($links){
            // TODO            

            return $links;
        }
        
    }
}

/* Intialize Private_Client_Boards instance */
function Private_Client_Boards() {
  global $upmp;    
	$upmp = Private_Client_Boards::instance();
}

add_action('init', 'pcb_load_textdomain',100);
function pcb_load_textdomain() {
    load_plugin_textdomain( 'upmp', false,dirname(plugin_basename(__FILE__)).'/lang');            
}

