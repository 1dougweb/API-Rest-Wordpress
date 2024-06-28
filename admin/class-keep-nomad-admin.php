<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Keep_Nomad_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_keep_nomad_fetch_post', array( $this, 'fetch_post' ) );
        add_action( 'wp_ajax_keep_nomad_get_posts', array( $this, 'get_posts' ) );
    }

    public function add_admin_menu() {
        add_menu_page(
            __( 'Keep Nomad', 'keep-nomad' ),
            __( 'Keep Nomad', 'keep-nomad' ),
            'manage_options',
            'keep-nomad',
            array( $this, 'create_admin_page' ),
            'dashicons-admin-post',
            6
        );
    }

    public function enqueue_scripts( $hook_suffix ) {
        if ( 'toplevel_page_keep-nomad' !== $hook_suffix ) {
            return;
        }
        
        wp_enqueue_style( 'keep-nomad-admin', KN_PLUGIN_URL . 'admin/css/keep-nomad-admin.css', array(), '1.0.0' );
        wp_enqueue_script( 'keep-nomad-admin', KN_PLUGIN_URL . 'admin/js/keep-nomad-admin.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'keep-nomad-admin', 'keepNomad', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'i18n' => array(
                'back' => __( 'Voltar', 'keep-nomad' ),
            ),
        ) );
    }

    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Keep Nomad', 'keep-nomad' ); ?></h1>
            <div id="keep-nomad-posts">
                <!-- Posts will be loaded here -->
            </div>
            <div class="keep-nomad-footer">
                <p><?php _e( 'Plugin developed by Douglas Rodrigues', 'keep-nomad' ); ?></p>
            </div>
        </div>
        <?php
    }

    public function fetch_post() {
        if ( ! isset( $_POST['post_id'] ) ) {
            wp_send_json_error( 'Missing post ID.' );
        }

        $post_id = intval( $_POST['post_id'] );
        $response = wp_remote_get( "https://libertysites.com.br/wp-json/wp/v2/tutoriais/{$post_id}" );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( 'Failed to fetch post.' );
        }

        $post = wp_remote_retrieve_body( $response );
        wp_send_json_success( $post );
    }

    public function get_posts() {
        $response = wp_remote_get( 'https://libertysites.com.br/wp-json/wp/v2/tutoriais' );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( 'Failed to fetch posts.' );
        }

        $posts = wp_remote_retrieve_body( $response );
        wp_send_json_success( $posts );
    }
}
