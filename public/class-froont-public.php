<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://froont.com
 * @since      1.0.0
 *
 * @package    Froont
 * @subpackage Froont/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Froont
 * @subpackage Froont/public
 * @author     Froont <sales@froont.com>
 */
class Froont_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Froont_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Froont_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/froont-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Froont_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Froont_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/froont-public.js', array( 'jquery' ), $this->version, false );

	}

  public function render_post_template($template) {
    $post = get_post();
    if ($post && in_array($post->post_type, array('page', 'post'))) {
      $froont_meta = get_post_meta($post->ID, 'froont', true);

      if (isset($froont_meta['date'])) {
        $wp_upload_dir = wp_upload_dir();
        $full_path = $wp_upload_dir['basedir'] . '/froont/' . $froont_meta['date'] . '/froont-page/index.html';

        if (file_exists($full_path)) {
          $template = plugin_dir_path( __FILE__ ) . 'template.php';
        }
      }
    }

    return $template;
  }

  public function output_template($full_path) {
    $output = file_get_contents($full_path);
    $output = apply_filters( 'froont_html', $output );

    return $output;
  }

}
