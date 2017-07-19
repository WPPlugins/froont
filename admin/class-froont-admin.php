<?php


/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://froont.com
 * @since      1.0.0
 *
 * @package    Froont
 * @subpackage Froont/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Froont
 * @subpackage Froont/admin
 * @author     Froont <sales@froont.com>
 */
class Froont_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

    if ( !session_id() ) {
      session_start();
    }
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/froont-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/froont-admin.js', array( 'jquery' ), $this->version, false );

	}

  public function add_settings_page() {
    add_options_page(
      'Froont Settings',
      'Froont',
      'manage_options',
      $this->plugin_name,
      array(
        $this,
        'render_settings_page'
      )
    );
  }

  public function render_settings_page() {
    ?>
    <div class="wrap froont_settings_panel">
      <h1>Froont Settings</h1>
        <form method="post" action="options.php">
          <?php
            settings_fields("froont_settings_general");
            do_settings_sections($this->plugin_name);
            submit_button();
          ?>
        </form>

        <p>
          Use placeholders in your Froont project and replace them with the data from your WordPress post or page.<br>
          This sample code replaces post <code>[TITLE]</code>, <code>[DATE]</code> and <code>[AUTHOR]</code><br>
          Code should be inserted in your themes <code>functions.php</code> file. Feel free to modify and adjust this code for your needs.
        </p>

        <pre>
function change_froont_html( $html ) {
  $search = array(
    '[TITLE]',
    '[DATE]',
    '[AUTHOR]'
  );
  $replace = array(
    get_the_title(),
    get_the_date(),
    get_the_author_meta('display_name', get_post_field('post_author'))
  );
  $html = str_replace( $search, $replace, $html );

  return $html;
}

add_filter( 'froont_html', 'change_froont_html' );</pre>

    </div>
    <?php
  }

  public function add_settings_page_fields() {
    add_settings_section("froont_settings_general", null, null, "froont");

    add_settings_field(
      'froont_api_key',
      'Froont API KEY',
      array(
        $this,
        'render_api_key_field'
      ),
      $this->plugin_name,
      'froont_settings_general'
    );

    register_setting('froont_settings_general', 'froont_api_key', array($this, 'sanitize_input'));
  }

  public function render_api_key_field() {
    ?>
      <input type="text" name="froont_api_key" id="froont_api_key" value="<?php echo esc_attr(get_option('froont_api_key')); ?>" />
    <?php
  }

  public function sanitize_input($input) {
    return esc_html($input);
  }

  public function add_settings_panel() {
    add_meta_box(
      'froont_meta_box',
      'Froont',
      array(
        $this,
        'render_settings_panel'
      ),
      array(
        'post',
        'page'
      ),
      'normal'
    );
  }

  public function render_settings_panel($post) {
    $html = '';
    $froont_api_key = get_option('froont_api_key');

    if ($froont_api_key) {
      //load projects for dropdown selectbox
      $api = new Froont_Api($froont_api_key, $this->version);
      $projects = $api->get_projects();

      if (is_array($projects)) {
        $froont_meta = get_post_meta($post->ID, 'froont', true);
        $selected = isset($froont_meta['url']) ? $froont_meta['url'] : null;
        $title = isset($froont_meta['title']) ? $froont_meta['title'] : null;
        $froont = $froont_meta ? esc_html(json_encode($froont_meta)) : '';
        $date_format = isset($froont_meta['date']) ? date(get_option('date_format') . ' ' . get_option('time_format'), $froont_meta['date']) : '';

        if ($projects) {
          $html .= wp_nonce_field('froont_nonce_action', 'froont_nonce', true, false);
          $html .= '<p class="project' . (!$title ? ' hidden' : '') . '"><span>' . esc_html($title) . ' / ' . $date_format . '</span> <a href="#" class="delete">Delete</a></p>';
          $html .= '<select name="froont_project" id="froont_project">';
          $html .= '<option value="">Select a project</option>';
          foreach ($projects as $key => $project) {
            $html .= '<option value="' . esc_attr($project['url']) . '"' .($selected == $project['url'] ? ' selected' : ''). '>' . esc_attr($project['title']) . '</option>';
          }
          $html .= '</select> ';
          $html .= '<input type="button" id="froont_import" class="button" value="Import"> ';
          $html .= '<input type="hidden" id="froont" name="froont" value="' . esc_attr($froont) . '">';
          $html .= '<span class="spinner"></span>';
          $html .= '<span class="error-message"></span>';
          $html .= '<p><a href="http://froont.com/" target="_blank">Create new Froont project</a></p>';
        } else {
          $html .= '<p>You don\'t have any Froont project. Go to <a href="http://froont.com/" target="_blank">Froont</a> and create a project.</p>';
        }
      } else {
        $html .= $projects;
      }

    } else {
      $html .= 'Please set your <a href="options-general.php?page=froont">Froont API KEY</a>';
    }

    echo $html;
  }

  public function import_project() {
    $url = $_POST['url'];

    $froont_api_key = get_option('froont_api_key');
    $api = new Froont_Api($froont_api_key);
    $project_zip_url = $api->get_project_zip_url($url);
    $data = $this->get_project_data($project_zip_url);

    if ($data['error']) {
      http_response_code(400);
    }

    echo json_encode($data);
    wp_die();
  }

  public function get_filesystem_credentials(){
    if (get_filesystem_method() !== 'direct') {
      return false;
    }

    $credentials = request_filesystem_credentials(admin_url(), '', false, false, array());
    if (!WP_Filesystem($credentials)) {
      return false;
    }

    return true;
  }

  public function get_project_data($project_zip_url) {
    global $wp_filesystem;
    $error = null;
    $fs_credentials = $this->get_filesystem_credentials();

    if (!$fs_credentials) {
      return array('error' => 'Cannot initialize filesystem');
    }

    $timestamp = time();
    $wp_upload_dir = wp_upload_dir();
    $path_froont = $wp_upload_dir['basedir'] . '/froont';
    $path = $path_froont . '/' . $timestamp;
    $filename = 'project.zip';
    $full_path = $path . '/' . $filename;

    $tmp_file = download_url($project_zip_url);
    if (is_wp_error($tmp_file)) {
      return array('error' => $tmp_file->get_error_message());
    }

    if (!$wp_filesystem->exists($path_froont) && !$wp_filesystem->mkdir($path_froont, 0777)) {
      return array('error' => 'Cannot create folder');
    }

    if (!$wp_filesystem->exists($path) && !$wp_filesystem->mkdir($path, 0777)) {
      return array('error' => 'Cannot create folder');
    }

    if (!$wp_filesystem->copy($tmp_file, $full_path)) {
      @unlink($tmp_file);
      return array('error' => 'Cannot copy file');
    }
    @unlink($tmp_file);

    $unzip = unzip_file($full_path, $path);
    if ($unzip !== true) {
      return array('error' => 'Failed to open ZIP archive');
    }

    $data = array(
      'date' => $timestamp,
      'date_format' => date(get_option('date_format') . ' ' . get_option('time_format'), $timestamp),
      'error' => $error
    );

    return $data;
  }

  public function save_post($post_id, $post) {
    // Add nonce for security and authentication.
    $nonce = isset( $_POST['froont_nonce'] ) ? $_POST['froont_nonce'] : '';
    $nonce_action = 'froont_nonce_action';

    // Check if nonce is set.
    if ( ! isset( $nonce ) ) {
      return;
    }

    // Check if nonce is valid.
    if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
      return;
    }

    // Check if user has permissions to save data.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }

    // Check if not an autosave.
    if ( wp_is_post_autosave( $post_id ) ) {
      return;
    }

    // Check if not a revision.
    if ( wp_is_post_revision( $post_id ) ) {
      return;
    }

    // Check if is post or page
    if ( ! in_array($post->post_type, array('post', 'page')) ) {
      return;
    }

    $data = json_decode(stripslashes($_POST['froont']), true);
    if ($data) {
      remove_action('save_post', array($this, 'save_post'));
      $update = array(
        'ID' => $post->ID,
        'post_content' => $this->get_plain_text_content($data['date']),
      );

      $this->replace_html_data($post, $data['date']);

      wp_update_post( $update );

      update_post_meta( $post_id, 'froont', $data );
    } else {
      delete_post_meta( $post_id, 'froont' );
    }

  }

  public function get_plain_text_content($timestamp) {
    $text = '';
    $html = $this->get_post_content($timestamp);
    if (preg_match('/(?:<body[^>]*>)(.*)<\/body>/isU', $html, $matches)) {
        $text = $matches[1];
        // Remove scripts
        $text = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $text);
        // Remove styles
        $text = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $text);
        //Remove all html tags
        $text = trim(strip_tags(($text)));
        $text = preg_replace('/\s+/S', " ", $text);
    }

    return $text;
  }

  public function get_post_content($timestamp) {
    global $wp_filesystem;
    $fs_credentials = $this->get_filesystem_credentials();

    if (!$fs_credentials) {
      $_SESSION['froont_plugin_error'] = 'Froont plugin error: Cannot initialize filesystem';
      return;
    }

    $html = '';
    $wp_upload_dir = wp_upload_dir();
    $full_path = $wp_upload_dir['basedir'] . '/froont/' . $timestamp . '/froont-page/index.html';

    if (file_exists($full_path)) {
      $html = $wp_filesystem->get_contents($full_path);
      if ($html === false) {
        $_SESSION['froont_plugin_error'] = 'Froont plugin error: Cannot read file';
        $html = '';
      }
    }

    return $html;
  }

  public function replace_html_data($post, $timestamp) {
    $this->timestamp = $timestamp;
    $html = $this->get_post_content($timestamp);

    //replace title
    $html = preg_replace('/(<title[^>]*>)(.*?)(<\/title>)/i', '$1' . esc_html($post->post_title) . '$3', $html);

    //replace DC.title
    $html = preg_replace('/(name=\"DC.title\".*?content=\")(.*?)(\")/i', '$1' . esc_html($post->post_title) . '$3', $html);

    //replace og:title
    $html = preg_replace('/(property=\"og:title\".*?content=\")(.*?)(\")/i', '$1' . esc_html($post->post_title) . '$3', $html);

    //replace twitter:title
    $html = preg_replace('/(name=\"twitter:title\".*?content=\")(.*?)(\")/i', '$1' . esc_html($post->post_title) . '$3', $html);

    //replace og:image path
    $html = preg_replace_callback('/(property=\"og:image\".*?content=\")(.*?)(\")/i', array($this, 'replace_path'), $html);

    //replace twitter:image path
    $html = preg_replace_callback('/(name=\"twitter:image\".*?content=\")(.*?)(\")/i', array($this, 'replace_path'), $html);

    //replace og:url
    $html = preg_replace('/(property=\"og:url\".*?content=\")(.*?)(\")/i', '$1' . esc_html(get_permalink($post)) . '$3', $html);

    //replace og:site_name
    $html = preg_replace('/(property=\"og:site_name\".*?content=\")(.*?)(\")/i', '$1' . esc_html(get_bloginfo('name')) . '$3', $html);

    //replace style path
    $html = preg_replace_callback('/(rel=\"stylesheet\".*?href=\")(.*?)(\")/i', array($this, 'replace_path'), $html);

    //replace script path
    $html = preg_replace_callback('/(<script.*?src=\")(.*?)(\")/i', array($this, 'replace_path'), $html);

    //replace image path
    $html = preg_replace_callback('/(<img.*?src=\")(.*?)(\")/i', array($this, 'replace_path'), $html);

    //replace image path for gallery widget
    $html = preg_replace_callback('/(src&#34;: &#34;)(.*?)(&#34;)/i', array($this, 'replace_path'), $html);

    //replace og:type article/website
    $og_type = 'article';
    // set og:type to 'website' if it's landing page
    if ($post->post_type == 'page' && $post->ID == get_option( 'page_on_front' )) {
      $og_type = 'website';
    }
    $html = preg_replace('/(property=\"og:type\".*?content=\")(.*?)(\")/i', '$1' . $og_type . '$3', $html);

    //replace msapplication-TileImage path
    $html = preg_replace_callback('/(name=\"msapplication-TileImage\".*?content=\")(.*?)(\")/i', array($this, 'replace_path'), $html);

    //replace apple-touch-icon path
    $html = preg_replace_callback('/(rel=\"apple-touch-icon\".*?href=\")(.*?)(\")/i', array($this, 'replace_path'), $html);

    //replace favicon path
    $html = preg_replace_callback('/(rel=\"icon\".*?href=\")(.*?)(\")/i', array($this, 'replace_path'), $html);

    $this->save_html($timestamp, $html);
  }

  function replace_path($matches) {
    $path = $matches[2];

    //ignore absolute urls
    if (preg_match("/^(http:|https:|\/\/)(.*)/i", $path)) {
      return $matches[1] . $matches[2] . $matches[3];
    }

    $wp_upload_dir = wp_upload_dir();
    $path = $wp_upload_dir['baseurl'] . '/froont/' . $this->timestamp . '/froont-page/' . $path;

    return $matches[1] . $path . $matches[3];
  }

  public function save_html($timestamp, $html) {
    global $wp_filesystem;
    $fs_credentials = $this->get_filesystem_credentials();

    if (!$fs_credentials) {
      $_SESSION['froont_plugin_error'] = 'Froont plugin error: Cannot initialize filesystem';
      return;
    }

    $wp_upload_dir = wp_upload_dir();
    $full_path = $wp_upload_dir['basedir'] . '/froont/' . $timestamp . '/froont-page/index.html';

    if (!$wp_filesystem->put_contents($full_path, $html)) {
      $_SESSION['froont_plugin_error'] = 'Froont plugin error: Cannot write to file';
      return;
    }
  }

  public function show_error() {
    if (isset($_SESSION['froont_plugin_error'])) {
      ?>
        <div class="notice notice-error is-dismissible">
          <p><?php echo $_SESSION['froont_plugin_error']; ?></p>
        </div>
      <?php
      unset($_SESSION['froont_plugin_error']);
    }
  }

}
