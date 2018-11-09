<?php
/**
 * Plugin Name: Addon Elementor Extention
 * Description: Description for this plugin
 * Plugin URI:  https://Plugin_Url_Page_Description
 * Version:     1.0
 * Author:      Tong Quang Dat
 * Author URI:  https://tongquangdat.com
 * Text Domain: addon_elementor
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'EC_EXTENTIONS_VERSION', '1.0' );
define( 'EC_ELEMENTOR_VERSION', '2.0.0');
define( 'MINIMUM_PHP_VERSION', '5.6');
define( 'EC_EXTENTIONS_URL', plugins_url('/', __FILE__ ) );
define( 'EC_EXTENTIONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'EC_EXTENTIONS_FILE', __FILE__ );
define( 'EC_EXTENTIONS_BASENAME', plugin_basename(__FILE__));


/**
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 1.0.0
 */
function ec_init() {
    // Load localization file
    load_plugin_textdomain( 'addon_elementor' );

    // Check if Elementor installed and activated
    // Notice if the Elementor is not active
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'admin_notice_fail_load' );
        return;
    }

    // Check required version
    if ( ! version_compare( EC_ELEMENTOR_VERSION, 2.0, '>=' ) ) {
        add_action( 'admin_notices', 'admin_notice_minimum_elementor_version' );
        return;
    }

    // Check for required PHP version
    if ( version_compare( PHP_VERSION, MINIMUM_PHP_VERSION, '<' ) ) {
        add_action( 'admin_notices', 'admin_notice_minimum_php_version' );
        return;
    }

    // Require the main plugin file
    require( __DIR__ . '/base.php' );
}
add_action( 'plugins_loaded', 'ec_init' );


function admin_notice_minimum_elementor_version() {
    if ( ! current_user_can( 'update_plugins' ) ) {
        return;
    }

    $file_path = 'elementor/elementor.php';

    $upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
    $message = '<p>' . __( 'Elementor Hello World is not working because you are using an old version of Elementor.', 'addon_elementor' ) . '</p>';
    $message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'addon_elementor' ) ) . '</p>';

    echo '<div class="error">' . $message . '</div>';
}

/**
 * Admin notice
 *
 * Warning when the site doesn't have a minimum required PHP version.
 *
 * @since 1.0.0
 *
 * @access public
 */
function admin_notice_minimum_php_version() {

    if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

    $message = sprintf(
    /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
        esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'addon_elementor' ),
        '<strong>' . esc_html__( 'Elementor Test Extension', 'addon_elementor' ) . '</strong>',
        '<strong>' . esc_html__( 'PHP', 'addon_elementor' ) . '</strong>',
        MINIMUM_PHP_VERSION
    );

    printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
}

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.0.0
 *
 * @return void
 */
function admin_notice_fail_load() {
    $screen = get_current_screen();
    if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
        return;
    }

    $plugin = 'elementor/elementor.php';

    if ( _is_elementor_installed() ) {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        $activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

        $message = '<p>' . __( 'Extentions not working because you need to activate the Elementor plugin.', 'addon_elementor' ) . '</p>';
        $message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, __( 'Activate Elementor Now', 'addon_elementor' ) ) . '</p>';
    } else {
        if ( ! current_user_can( 'install_plugins' ) ) {
            return;
        }

        $install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

        $message = '<p>' . __( 'Extentions not working because you need to install the Elementor plugin', 'addon_elementor' ) . '</p>';
        $message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Elementor Now', 'addon_elementor' ) ) . '</p>';
    }

    echo '<div class="error"><p>' . $message . '</p></div>';
}
