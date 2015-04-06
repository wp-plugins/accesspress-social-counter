<?php defined('ABSPATH') or die("No script kiddies please!");
/**
 * Plugin Name: AccessPress Social Counter
 * Plugin URI: https://accesspressthemes.com/wordpress-plugins/accesspress-social-counter/
 * Description: A plugin to display your social accounts fans, subscribers and followers number on your website with handful of backend settings and interface. 
 * Version: 1.1.2
 * Author: AccessPress Themes
 * Author URI: http://accesspressthemes.com
 * Text Domain: aps-counter
 * Domain Path: /languages/
 * Network: false
 * License: GPL2
 */
/**
 * Declartion of necessary constants for plugin
 * */
if (!defined('SC_IMAGE_DIR')) {
    define('SC_IMAGE_DIR', plugin_dir_url(__FILE__) . 'images');
}
if (!defined('SC_JS_DIR')) {
    define('SC_JS_DIR', plugin_dir_url(__FILE__) . 'js');
}
if (!defined('SC_CSS_DIR')) {
    define('SC_CSS_DIR', plugin_dir_url(__FILE__) . 'css');
}
if (!defined('SC_VERSION')) {
    define('SC_VERSION', '1.1.2');
}
/**
 * Register of widgets
 * */
include_once('inc/backend/widget.php');
if (!class_exists('SC_Class')) {

    class SC_Class {

        var $apsc_settings;

        /**
         * Initializes the plugin functions 
         */
        function __construct() {
            $this->apsc_settings = get_option('apsc_settings');
            register_activation_hook(__FILE__, array($this, 'load_default_settings')); //loads default settings for the plugin while activating the plugin
            add_action('init', array($this, 'plugin_text_domain')); //loads text domain for translation ready
            add_action('init', array($this, 'session_init')); //starts the session
            add_action('admin_menu', array($this, 'add_sc_menu')); //adds plugin menu in wp-admin
            add_action('admin_enqueue_scripts', array($this, 'register_admin_assets')); //registers admin assests such as js and css
            add_action('wp_enqueue_scripts', array($this, 'register_frontend_assets')); //registers js and css for frontend
            add_action('admin_post_apsc_settings_action', array($this, 'apsc_settings_action')); //recieves the posted values from settings form
            add_action('admin_post_apsc_restore_default', array($this, 'apsc_restore_default')); //restores default settings;
            add_action('widgets_init', array($this, 'register_apsc_widget')); //registers the widget
            add_shortcode('aps-counter', array($this, 'apsc_shortcode')); //adds a shortcode
            add_action('admin_post_apsc_delete_cache', array($this, 'apsc_delete_cache')); //deletes the counter values from cache
        }

        /**
         * Plugin Translation
         */
        function plugin_text_domain() {
            load_plugin_textdomain('aps-counter', false, basename(dirname(__FILE__)) . '/languages/');
        }

        /**
         * Load Default Settings
         * */
        function load_default_settings() {
            if (!get_option('apsc_settings')) {
                $apsc_settings = $this->get_default_settings();
                update_option('apsc_settings', $apsc_settings);
            }
        }

        /**
         * Plugin Admin Menu
         */
        function add_sc_menu() {
            add_menu_page(__('AccessPress Social Counter', 'aps-counter'), __('AccessPress Social Counter', 'aps-counter'), 'manage_options', 'ap-social-counter', array($this, 'sc_settings'), SC_IMAGE_DIR.'/sc-icon.png');
        }

        /**
         * Plugin Main Settings Page
         */
        function sc_settings() {
            include('inc/backend/settings.php');
        }

        /**
         * Registering of backend js and css
         */
        function register_admin_assets() {
            if (isset($_GET['page']) && $_GET['page'] == 'ap-social-counter') {
                wp_enqueue_style('sc-admin-css', SC_CSS_DIR . '/backend.css', array(), SC_VERSION);
                wp_enqueue_script('sc-admin-js', SC_JS_DIR . '/backend.js', array('jquery', 'jquery-ui-sortable'), SC_VERSION);
            }

            wp_enqueue_style('fontawesome-css', SC_CSS_DIR . '/font-awesome/font-awesome.min.css',false,SC_VERSION);
        }

        /**
         * Registers Frontend Assets
         * */
        function register_frontend_assets() {
            wp_enqueue_style('apsc-font-awesome',SC_CSS_DIR.'/font-awesome/font-awesome.css',array(),SC_VERSION);
            wp_enqueue_style('apsc-frontend-css', SC_CSS_DIR . '/frontend.css', array('apsc-font-awesome'), SC_VERSION);
        }

        /**
         * Saves settings to database
         */
        function apsc_settings_action() {
            if (!empty($_POST) && wp_verify_nonce($_POST['apsc_settings_nonce'], 'apsc_settings_action')) {
                include('inc/backend/save-settings.php');
            }
        }

        /**
         * Prints array in pre format
         */
        function print_array($array) {
            echo "<pre>";
            print_r($array);
            echo "</pre>";
        }

        /**
         * Starts the session
         */
        function session_init() {
            if (!session_id()) {
                session_start();
            }
        }

        /**
         * Restores the default 
         */
        function apsc_restore_default() {
            if (!empty($_GET) && wp_verify_nonce($_GET['_wpnonce'], 'apsc-restore-default-nonce')) {
                $apsc_settings = $this->get_default_settings();
                update_option('apsc_settings', $apsc_settings);
                $_SESSION['apsc_message'] = __('Default Settings Restored Successfully', 'aps-counter');
                wp_redirect(admin_url() . 'admin.php?page=ap-social-counter');
            }
        }

        /**
         * Returns Default Settings
         */
        function get_default_settings() {
            $apsc_settings = array('social_profile' => array('facebook' => array('page_id' => ''),
                    'twitter' => array('username' => '', 'consumer_key' => '', 'consumer_secret' => '', 'access_token' => '', 'access_token_secret' => ''),
                    'googlePlus' => array('page_id' => '', 'api_key' => ''),
                    'instagram' => array('username' => '', 'access_token' => '','user_id'=>''),
                    'youtube' => array('username' => '', 'channel_url' => ''),
                    'soundcloud' => array('username' => '', 'client_id' => ''),
                        'dribbble' => array('username' => ''),
                ),
                'profile_order' => array('facebook', 'twitter', 'googlePlus', 'instagram', 'youtube', 'soundcloud', 'dribbble', 'posts', 'comments'),
                'social_profile_theme' => 'theme-1',
                'counter_format'=>'comma',
                'cache_period' => ''
            );
            return $apsc_settings;
        }

        /**
         * AccessPress Social Counter Widget
         */
        function register_apsc_widget() {
            register_widget('APSC_Widget');
        }

        /**
         * Adds Shortcode
         */
        function apsc_shortcode($atts) {
            ob_start();
            include('inc/frontend/shortcode.php');
            $html = ob_get_contents();
            ob_get_clean();
            return $html;
        }

        /**
         * Clears the counter cache
         */
        function apsc_delete_cache() {
            if (!empty($_GET) && wp_verify_nonce($_GET['_wpnonce'], 'apsc-cache-nonce')) {
                $transient_array = array('apsc_facebook', 'apsc_twitter', 'apsc_youtube', 'apsc_instagram', 'apsc_googlePlus', 'apsc_soundcloud', 'apsc_dribbble', 'apsc_posts', 'apsc_comments');
                foreach ($transient_array as $transient) {
                    delete_transient($transient);
                }
                $_SESSION['apsc_message'] = __('Cache Deleted Successfully', 'aps-counter');
                wp_redirect(admin_url() . 'admin.php?page=ap-social-counter');
            }
        }

        /**
         * 
         * @param type $user
         * @param type $consumer_key
         * @param type $consumer_secret
         * @param type $oauth_access_token
         * @param type $oauth_access_token_secret
         * @return string
         */
        function authorization($user, $consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret) {
            $query = 'screen_name=' . $user;
            $signature = $this->signature($query, $consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret);

            return $this->header($signature);
        }

        /**
         * 
         * @param type $url
         * @param type $query
         * @param type $method
         * @param type $params
         * @return type string
         */
        function signature_base_string($url, $query, $method, $params) {
            $return = array();
            ksort($params);

            foreach ($params as $key => $value) {
                $return[] = $key . '=' . $value;
            }

            return $method . "&" . rawurlencode($url) . '&' . rawurlencode(implode('&', $return)) . '%26' . rawurlencode($query);
        }

        /**
         * 
         * @param type $query
         * @param type $consumer_key
         * @param type $consumer_secret
         * @param type $oauth_access_token
         * @param type $oauth_access_token_secret
         * @return type array
         */
        function signature($query, $consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret) {
            $oauth = array(
                'oauth_consumer_key' => $consumer_key,
                'oauth_nonce' => hash_hmac('sha1', time(), true),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_token' => $oauth_access_token,
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0'
            );
            $api_url = 'https://api.twitter.com/1.1/users/show.json';
            $base_info = $this->signature_base_string($api_url, $query, 'GET', $oauth);
            $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
            $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
            $oauth['oauth_signature'] = $oauth_signature;

            return $oauth;
        }

        /**
         * Build the header.
         *
         * @param  array $signature OAuth signature.
         *
         * @return string           OAuth Authorization.
         */
        public function header($signature) {
            $return = 'OAuth ';
            $values = array();

            foreach ($signature as $key => $value) {
                $values[] = $key . '="' . rawurlencode($value) . '"';
            }

            $return .= implode(', ', $values);

            return $return;
        }

        /**
         * Returns twitter count
         */
        function get_twitter_count() {
            $apsc_settings = $this->apsc_settings;
            $user = $apsc_settings['social_profile']['twitter']['username'];
            $api_url = 'https://api.twitter.com/1.1/users/show.json';
            $params = array(
                'method' => 'GET',
                'sslverify' => false,
                'timeout' => 60,
                'headers' => array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => $this->authorization(
                            $user, $apsc_settings['social_profile']['twitter']['consumer_key'], $apsc_settings['social_profile']['twitter']['consumer_secret'], $apsc_settings['social_profile']['twitter']['access_token'], $apsc_settings['social_profile']['twitter']['access_token_secret']
                    )
                )
            );

            $connection = wp_remote_get($api_url . '?screen_name=' . $user, $params);

            if (is_wp_error($connection)) {
                $count = 0;
            } else {
                $_data = json_decode($connection['body'], true);
                if (isset($_data['followers_count'])) {
                    $count = intval($_data['followers_count']);

                } else {
                    $count = 0;
                }
            }
            return $count;
        }
        
        /**
         * 
         * @param int $count
         * @param string $format
         */
        function get_formatted_count($count, $format) {
            switch ($format) {
                case 'comma':
                    $count = number_format($count);
                    break;
                case 'short':
                    $count = $this->abreviateTotalCount($count);
                    break;
                default:
                    break;
            }
            return $count;
        }
         
         /**
         * 
         * @param integer $value
         * @return string
         */
        function abreviateTotalCount($value) {

            $abbreviations = array(12 => 'T', 9 => 'B', 6 => 'M', 3 => 'K', 0 => '');

            foreach ($abbreviations as $exponent => $abbreviation) {

                if ($value >= pow(10, $exponent)) {

                    return round(floatval($value / pow(10, $exponent)), 1) . $abbreviation;
                }
            }
        }


    }

    $sc_object = new SC_Class(); //initialization of plugin
}