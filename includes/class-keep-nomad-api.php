<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Keep_Nomad_API {

    public function __construct() {
        add_action( 'wp_ajax_keep_nomad_get_posts', array( $this, 'get_posts' ) );
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