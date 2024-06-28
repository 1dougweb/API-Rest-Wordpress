<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Keep_Nomad_Updater {

    private $api_url;
    private $plugin_file;
    private $plugin_data;

    public function __construct( $api_url, $plugin_file, $plugin_data ) {
        $this->api_url = $api_url;
        $this->plugin_file = $plugin_file;
        $this->plugin_data = $plugin_data;

        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
        add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
        add_filter( 'upgrader_post_install', array( $this, 'post_install' ), 10, 3 );
    }

    public function check_for_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $response = $this->request_update_check();

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            return $transient;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ) );

        if ( version_compare( $this->plugin_data['version'], $data->new_version, '<' ) ) {
            $transient->response[$this->plugin_data['plugin']] = (object) array(
                'new_version' => $data->new_version,
                'slug' => $this->plugin_data['slug'],
                'url' => $this->plugin_data['url'],
                'package' => $data->package_url,
            );
        }

        return $transient;
    }

    public function plugin_info( $result, $action, $args ) {
        if ( 'plugin_information' !== $action || $this->plugin_data['slug'] !== $args->slug ) {
            return $result;
        }

        $response = $this->request_update_check();

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            return false;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ) );

        $result = (object) array(
            'name' => $this->plugin_data['name'],
            'slug' => $this->plugin_data['slug'],
            'version' => $data->new_version,
            'author' => $this->plugin_data['author'],
            'download_link' => $data->package_url,
            'trunk' => $this->plugin_data['url'],
        );

        return $result;
    }

    public function post_install( $response, $hook_extra, $result ) {
        global $wp_filesystem;

        $install_directory = plugin_dir_path( $this->plugin_file );
        $wp_filesystem->move( $result['destination'], $install_directory );
        $result['destination'] = $install_directory;

        return $result;
    }

    private function request_update_check() {
        $request_args = array(
            'body' => array(
                'slug' => $this->plugin_data['slug'],
                'version' => $this->plugin_data['version'],
            ),
            'timeout' => 10,
            'sslverify' => false,
        );

        return wp_remote_post( $this->api_url = 'https://libertysites.com.br/wp-json/wp/v2/plugin-updates' );
    }
}