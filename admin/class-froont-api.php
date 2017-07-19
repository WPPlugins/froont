<?php


/**
 * Froont API.
 *
 * @link       http://froont.com
 * @since      1.0.0
 *
 * @package    Froont
 * @subpackage Froont/admin
 */

/**
 * Froont API.
 *
 * @package    Froont
 * @subpackage Froont/admin
 * @author     Froont <sales@froont.com>
 */
class Froont_Api {

  /**
   * API KEY.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $api_key    Froont API KEY.
   */
  private $api_key;

  private $version;

  private static $api_endpoint = 'https://froont.com';

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $api_key    Froont API KEY.
   */
  public function __construct( $api_key, $version ) {

    $this->api_key = $api_key;
    $this->version = $version;

  }

  /**
   * Get all user projects.
   *
   * @since    1.0.0
   */
  public function get_projects() {
    $api_url = self::$api_endpoint . '/-api/get-projects?api_key=' . $this->api_key;
    $response = wp_remote_get($api_url);
    $response_code = wp_remote_retrieve_response_code( $response );

    if ($response_code !== 200) {
      return 'Not authorised.';
    }

    if (is_wp_error( $response ) ) {
      return $response->get_error_message();
    }

    $headers = wp_remote_retrieve_headers($response);

    if (!isset($headers['x-froont-wordpress-plugin-version']) || $headers['x-froont-wordpress-plugin-version'] !== $this->version) {
      return 'Please update Froont for WordPress plugin to the latest version';
    } else {
      $projects = wp_remote_retrieve_body($response);
    }

    return json_decode($projects, true);
  }

  /**
   * Get project ZIP file url.
   *
   * @since    1.0.0
   */
  public function get_project_zip_url($url) {
    $api_url = self::$api_endpoint . $url . '?api_key=' . $this->api_key;

    return $api_url;
  }
}
