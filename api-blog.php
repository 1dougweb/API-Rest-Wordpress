<?php
/**
 * Plugin Name: Keep Nomad
 * Description: Exibe posts de outro blog via API dentro do painel WordPress.
 * Version: 1.0.6
 * Author: Douglas Rodrigues
 * License: GPLv2 or later
 * Text Domain: keep-nomad
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin URL and PATH
define( 'KN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'KN_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Include necessary files
require_once KN_PLUGIN_PATH . 'includes/class-keep-nomad-api.php';
require_once KN_PLUGIN_PATH . 'admin/class-keep-nomad-admin.php';

// Initialize the plugin
add_action( 'plugins_loaded', 'keep_nomad_init' );

function keep_nomad_init() {
    new Keep_Nomad_API();
    new Keep_Nomad_Admin();
}

function keep_nomad_localize_script() {
    wp_localize_script( 'keep-nomad-admin', 'keepNomad', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'i18n' => array(
            'back' => __( 'Voltar', 'keep-nomad' ),
        ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'keep_nomad_localize_script' );