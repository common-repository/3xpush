<?php

class xPush_Main_Class
{

    protected $loader;

    protected $plugin_name;

    protected $version;

    public function __construct() {

	  if ( defined( 'PLUGIN_XPUSH_VERSION' ) ) {
		$this->version = PLUGIN_XPUSH_VERSION;
	  } else {
		$this->version = '1.1.0';
	  }

	  $this->plugin_name = '3xpush';
	  $this->xpush_load_dependencies();
	  $this->xpush_define_admin_hooks();
	  $this->xpush_define_public_hooks();
    }

    private function xpush_load_dependencies() {

	  require_once PLUGIN_XPUSH_PATH . 'includes/class-xpush-loader.php';
	  require_once PLUGIN_XPUSH_PATH . 'includes/class-xpush-admin.php';
	  require_once PLUGIN_XPUSH_PATH . 'includes/class-xpush-public.php';

	  $this->loader = new xPush_Loader_Class();
    }

    private function xpush_define_admin_hooks() {

	  $xpush_api_key = get_option('xpush_api_key');
	  $xpush_tuned = get_option('xpush_tuned');

	  if (is_admin()){

		$plugin_admin = new xPush_Admin_Class( $this->xpush_get_plugin_name(), $this->xpush_get_version() );
		$this->loader->xpush_add_action('admin_enqueue_scripts', $plugin_admin, 'xpush_enqueue_styles' );
		$this->loader->xpush_add_action('admin_enqueue_scripts', $plugin_admin, 'xpush_enqueue_scripts' );
		$this->loader->xpush_add_action('admin_menu', $plugin_admin, 'xpush_settings_page' );

		if (strlen($xpush_api_key) > 10) {

		    $this->loader->xpush_add_action('add_meta_boxes', $plugin_admin, 'xpush_meta_box', 999);
		    $this->loader->xpush_add_action('post_updated', $plugin_admin, 'xpush_save_post' );
		    $plugin_admin->xpush_set_api_cash();
		}

		if ($xpush_tuned != 1) {

		    $this->loader->xpush_add_action('admin_notices', $plugin_admin, 'xpush_notice' );
		}

		$this->loader->xpush_add_action( 'wp_ajax_xpush_image_upload', $plugin_admin, 'xpush_image_upload');
	    }
	}

    private function xpush_define_public_hooks() {

	  if (!is_admin() && get_option('xpush_tuned') == 1) {

		$plugin_public = new xPush_Public_Class( $this->xpush_get_plugin_name(), $this->xpush_get_version() );
		$this->loader->xpush_add_action( 'wp_footer', $plugin_public, 'xpush_enqueue_scripts' );
	  }
    }

    public function xpush_run() {

	  $this->loader->xpush_run();
    }

    public function xpush_get_plugin_name() {

	  return $this->plugin_name;
    }

    public function xpush_get_loader() {

	  return $this->loader;
    }

    public function xpush_get_version() {

	  return $this->version;
    }

}
