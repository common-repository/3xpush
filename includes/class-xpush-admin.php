<?php

class xPush_Admin_Class
{

    private $plugin_name;

    private $version;

    protected $api;

    protected $options;

    public function __construct($plugin_name, $version ) {

	  $this->plugin_name = $plugin_name;

	  $this->version = $version;

	  require_once PLUGIN_XPUSH_PATH . 'includes/class-xpush-api.php';

	  $this->api = new xPush_API_Class();

	  $this->options = $this->xpush_get_options();

	}

    protected function xpush_get_options() {

	  return array(
		'xpush_sites',
		'xpush_langs',
		'xpush_regions',
		'xpush_tags');
    }

    public function xpush_enqueue_styles() {

	  $screen = get_current_screen();

	  wp_enqueue_style('admin-styles', PLUGIN_XPUSH_URL . 'assets/css/admin-styles.css', array(), $this->version, 'all' );

	  if (get_current_screen()->id == 'settings_page_xpush-settings') {
		wp_enqueue_style('tooltips-styles', PLUGIN_XPUSH_URL . 'assets/libs/darktooltip/darktooltip.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'thickbox' );
	  }

	  if ($screen->id == 'post') {
		  wp_enqueue_style('jqueryui', PLUGIN_XPUSH_URL . 'assets/css/jquery-ui-smoothness.css', false, null );
		  wp_enqueue_style('timepicker', PLUGIN_XPUSH_URL . 'assets/libs/timepicker/jquery-ui-timepicker-addon.min.css', array(), $this->version, 'all' );
		  wp_enqueue_style('bootstrap', PLUGIN_XPUSH_URL . 'assets/libs/bootstrap/bootstrap.min.css', array(), $this->version, 'all' );
		  wp_enqueue_style('multiselect', PLUGIN_XPUSH_URL . 'assets/libs/multiselect/bootstrap-multiselect.css', array(), $this->version, 'all' );
	  }
    }

    public function xpush_enqueue_scripts() {

	  $lang = $this->xpush_get_site_lang();
	  $screen = get_current_screen();

	  $js_lang = ($lang[0] == 'ru') ? 'ru' : 'en';

	  wp_enqueue_script('scripts-lang', PLUGIN_XPUSH_URL . 'assets/js/langs/'. $js_lang .'.js', false, $this->version, false );

	  if ($screen->id == 'settings_page_xpush-settings') {
		wp_enqueue_script('thickbox' );
		wp_enqueue_media();
		wp_enqueue_script('custom-header');
		wp_enqueue_script('tooltips-script', PLUGIN_XPUSH_URL . 'assets/libs/darktooltip/jquery.darktooltip.js', array( 'jquery' ), $this->version, true );
	  }

	  if ($screen->id == 'post') {
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('timepicker', PLUGIN_XPUSH_URL . 'assets/libs/timepicker/jquery-ui-timepicker-addon.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script('bootstrap', PLUGIN_XPUSH_URL . 'assets/libs/bootstrap/bootstrap.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script('multiselect', PLUGIN_XPUSH_URL . 'assets/libs/multiselect/bootstrap-multiselect.js', array( 'jquery' ), $this->version, true );
	  }

	  wp_enqueue_script('admin-scripts', PLUGIN_XPUSH_URL . 'assets/js/admin-scripts.js', array( 'jquery' ), $this->version, true );
    }

    public function xpush_settings_page(){

	  add_options_page('xpush-settings', '<img class="xpush-menu-icon" src="' . PLUGIN_XPUSH_URL . 'assets/images/menu-icon.png" alt="">3XPUSH', 'manage_options', 'xpush-settings', array($this, 'xpush_show_settings_page'));
    }

    public function xpush_notice(){

	  if (get_current_screen()->id != 'settings_page_xpush-settings') {
		include(PLUGIN_XPUSH_PATH .'templates/notice.php');
	  }
    }

    public function xpush_show_settings_page(){

	  if (count($_POST) > 0){
		$post = $this->xpush_sanitize_data($_POST);
		$notice = $this->xpush_post_processing($post);
	  }

	  $api_key = get_option('xpush_api_key');
	  $api_settings = get_option('xpush_settings');
	  $xpush_tuned = get_option('xpush_tuned');

	  if ($api_settings){
		$api_settings = maybe_unserialize($api_settings);
	  }

	  $fclass = ($api_key != '') ? true : false;

	  $tag_options = array(
		'not' => __( 'Not', '3xpush' ),
		'category' => __( 'Category', '3xpush' )
	  );

	  $log = $this->xpush_get_api_log();
	  $icon = ($api_settings['psx_site_icon'] != '') ? $api_settings['psx_site_icon'] : PLUGIN_XPUSH_URL. 'assets/images/default-icon.png';

	  include(PLUGIN_XPUSH_PATH .'templates/settings-page.php');
    }

    public function xpush_meta_box(){
	  $is_gutenberg = $this->xpush_is_gutenberg_active();
	  $location = ($is_gutenberg == 1) ? 'normal' : 'side';
	  add_meta_box('xpush-meta', __('Widget title','3xpush'), array($this, 'xpush_show_meta_box'), null, $location, 'high');
    }

    public function xpush_show_meta_box($post, $meta){

	  wp_nonce_field( plugin_basename(__FILE__), 'xpush_noncename' );

	  $xpush_options = array();
	  $xpush_lang = $this->xpush_get_site_lang();
	  $xplush_domen = $this->xpush_get_site_domen();
	  $post_meta = get_post_meta($post->ID);

	  $api_settings = get_option('xpush_settings');

	  if ($api_settings){
		$api_settings = maybe_unserialize($api_settings);
	  }
	    wp_nonce_field( plugin_basename(__FILE__), 'xpush_noncename' );

	    foreach ($this->options as $item){
		  $cache = get_transient($item);
		  if( false !== $cache){
			$xpush_options[$item] = $cache;
		  }
		  else {
			$cache = $this->api->xpush_get_api_options($item);
			$xpush_options[$item] = $cache;
		  }
	    }
	    $xpush_options['xpush_regions'] = json_decode(json_encode($xpush_options['xpush_regions']), true);
	    $xpush_options['xpush_langs'] = json_decode(json_encode($xpush_options['xpush_langs']), true);

	    include(PLUGIN_XPUSH_PATH .'templates/widget.php');

	}

    public function xpush_save_post($post_id){

	  if ( ! wp_verify_nonce( $_POST['xpush_noncename'], plugin_basename(__FILE__) ) )
		return;

	  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return;

	  if( ! current_user_can( 'edit_post', $post_id ) )
		return;

	  $_REQUEST['is_push'] = 0;

	  $postData = $this->xpush_sanitize_data($_POST);

	  if ($postData['xpush_send'] == 1 && $_REQUEST['is_push'] == 0) {

		$settings = maybe_unserialize(get_option('xpush_settings'));

		$is_gutenberg = $this->xpush_is_gutenberg_active();

		if ($is_gutenberg == 1) {
		    $post = get_post($post_id);
		    $title = $post->post_title;
		    $content = str_replace('\\', '', $post->post_content);
		}
		else {
		    $title = $postData['post_title'];
		    $content = str_replace('\\', '', $postData['content']);
		}

		if (empty($settings['psx_site_icon']) || $settings['psx_site_icon'] == '') {
		    $settings['psx_site_icon'] =  PLUGIN_XPUSH_URL. 'assets/images/default-icon.png';
		}

		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);

		$first_img = (is_array($matches[1]) && count($matches[1]) > 0) ? $matches[1][0] : '';

		if ($postData['xpush-date'] == '') {
		    $date = current_time('Y-m-d H:i', 0);
		}
		else {
		    $date = $postData['xpush-date'];
		}

		$tags = ($postData['xpush_tags'] != '') ? explode(',', $postData['xpush_tags']) : array();
		$langs = ($postData['xpush_langs'] != '') ? explode(',', $postData['xpush_langs']) : array();
		$regions = ($postData['xpush_regions'] != '') ? explode(',', $postData['xpush_regions']) : array();
		$sites = ($postData['xpush_sites'] != '') ? explode(',', $postData['xpush_sites']) : array();

		$tag_query = '';
		foreach ($tags as $tag) {
		    $tag_query .= '&tags[]='.$tag;
		}
		$langs_query = '';
		foreach ($langs as $tag) {
		    $langs_query .= '&langs[]='.$tag;
		}
		$regions_query = '';
		foreach ($regions as $tag) {
		    $regions_query .= '&regions[]='.$tag;
		}
		$sites_query = '';
		foreach ($sites as $tag) {
		    $sites_query .= '&sids[]='.$tag;
		}

		$params_query = $tag_query.$langs_query.$regions_query.$sites_query;

		$params = array(
		    'title' => $this->xpush_trim_text($title, 33),
		    'text' => $this->xpush_trim_text($content, 123),
		    'icon' => $settings['psx_site_icon'],
		    'image' => $first_img,
		    'url' => urlencode(get_permalink($post_id)),
		    'params_query' => $params_query,
		    'send_time' => $date
		);

		update_option('post_data', $params_query);

		$send_push = $this->api->xpush_send_api_push($params);
	      $params['url'] = get_permalink($post_id);
		$this->xpush_set_api_log($params, $postData, $send_push);
		$_REQUEST['is_push'] = 1;
	  }
	}

    protected function xpush_sanitize_data($data) {
	  $result = array();
	  foreach ($data as $key => $val) {
		$result[$key] = sanitize_text_field($val);
	  }
	  return $result;
    }

    protected function xpush_set_api_log($params, $post, $send){
	  global $wpdb;
	  if (is_numeric($send)) {
		$status = 1;
		$recipients = $send;
		$class = 'success';
		$text = __( 'Sent', '3xpush' );
	  }
	  else {
		$status = 0;
		$recipients = 0;
		$class = 'error';
		$text = __( 'ERROR', '3xpush' );
	  }
	  $content = '<p>'.$params['send_time']. ' <a href="'.$params['url'].'" target="_blank">'.$params['title'].'</a> <span class="'.$class.'">'.$text;

	  $sitesCache = json_decode(json_encode (get_transient('xpush_sites')), true);
	  $sitesArr  = explode(',', $post['xpush_sites']);
	  $sites = '';
	  foreach ($sitesArr as $key => $val) {
		$sites .= $sitesCache[$val]. ',';
	  }
	  if ($sites{strlen($sites)-1} == ',') {
		$sites = substr($sites,0,-1);
	  }
	  if ($post['xpush_regions'] != '') {
		if ($post['xpush_regions']{strlen($post['xpush_regions'])-1} == ',') {
		    $post['xpush_regions'] = substr($post['xpush_regions'],0,-1);
		}
		$post['xpush_regions'] = str_replace(',',', ',$post['xpush_regions']);
	  }
	  if ($post['xpush_langs'] != '') {
		if ($post['xpush_langs']{strlen($post['xpush_langs'])-1} == ',') {
		    $post['xpush_langs'] = substr($post['xpush_langs'],0,-1);
		}
		$post['xpush_langs'] = str_replace(',',', ',$post['xpush_langs']);
	  }
	  if ($post['xpush_tags'] != '') {
		if ($post['xpush_tags']{strlen($post['xpush_tags'])-1} == ',') {
		    $post['xpush_tags'] = substr($post['xpush_tags'],0,-1);
		}
		$post['xpush_tags'] = str_replace(',',', ',$post['xpush_tags']);
	  }

	  $sites = str_replace(',',', ',$sites);

	  if (is_numeric($send)) {
		$content .= ' '.__( 'RecipientsF', '3xpush' ).': '.$send;
	  }
	  else {
		$content .= ' '.$send;
	  }
	  $content .= '</span>'.'</p>';
	  $content .= '<p><b>'. __( 'RecipientsN', '3xpush' ) .'</b></p>';
	  $content .= '<p><span class="option"><b>'. __( 'Sites', '3xpush' ).': </b>'.$sites. '</span><span class="option"><b>'. __('Regions', '3xpush' ).': </b>'.$post['xpush_regions']. '</span><span class="option"><b>'. __('Langs', '3xpush').': </b>'.$post['xpush_langs']. '</span><span class="option"><b>'. __( 'Tags', '3xpush' ).': </b>'.$post['xpush_tags']. '</span></p>';
	  $data = array(
		'id' => NULL,
		'p_date' => $params['send_time'].':00',
		'p_status' => $status,
		'p_recipients' => $recipients,
		'p_content' => $content
	  );
	  $wpdb->insert($wpdb->get_blog_prefix().'xpush_dispatch', $data);

    }

    protected function xpush_get_api_log(){
	  global $wpdb;
	  $query = $wpdb->get_results("SELECT * FROM ".$wpdb->get_blog_prefix()."xpush_dispatch ORDER BY p_date DESC", OBJECT);
	  return $query;
    }

    protected function xpush_trim_text($text, $count){
	  $text = strip_tags(trim($text));
	  $exp = mb_strlen($text, 'UTF-8');
	  if ($exp > $count) {
		$text = mb_substr($text, 0, $count, 'UTF-8');
	  }
	  return $text;
    }

    public function xpush_get_site_lang(){
	  $langArr = explode('_', get_locale());
	  return $langArr;
    }

    public function xpush_get_site_domen(){
	  $domenArr = explode('//', get_option('siteurl'));
	  return $domenArr[1];
    }

    protected function xpush_post_processing($post){
		$data = array();
		$result = array();
		unset($post['file']);
		foreach($post as $key => $val){
			$val = (!is_numeric($val)) ? htmlspecialchars(trim($val)) : intval($val);
			$data[$key] = $val;
		}
		if ($data['form-action'] == 'check-key' && strlen($data['api-key']) > 1){
			$check = $this->api->xpush_check_api_status($data['api-key']);
			if ($check == 'valid'){
				$result['message'] =  __( 'Api-key valid', '3xpush' );
				$result['class'] = 'notice-success';
			    delete_option('xpush_api_key');
			    update_option('xpush_api_key', $data['api-key']);
			}
			else {
				$result['message'] =  $check;
				$result['class'] = 'notice-error';
			}
		}
		else {
			unset($data['form-action']);
			delete_option('xpush_settings');
			delete_option('xpush_tuned');
			$sUpdate = update_option('xpush_settings', maybe_serialize($data));
			$tUpdate = update_option('xpush_tuned', '1');
			if ($sUpdate && $tUpdate){
				$result['message'] = __( 'Settings saved successfully', '3xpush' );
				$result['class'] = 'notice-success';
			}
			else {
				$result['message'] =  __( 'Error saving settings', '3xpush' );
				$result['class'] = 'notice-error';
			}
		}
		return $result;
	}

    public function xpush_set_api_cash(){
	  $key = get_option('xpush_api_key');

	  if (strlen($key) > 20) {
		foreach ($this->options as $item){
		    $cache = get_transient($item);
		    if( false === $cache){
			  $option = $this->api->xpush_get_api_options($item);
			  if ($option) {
				set_transient($item, $option, 1 * HOUR_IN_SECONDS );
			  }
		    }
		}
	  }
    }

    private function xpush_is_gutenberg_active() {

	  $gutenberg    = false;
	  $block_editor = false;

	  if ( has_filter( 'replace_editor', 'gutenberg_init' ) ) {
		$gutenberg = true;
	  }

	  if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' ) ) {
		$block_editor = true;
	  }

	  if ( ! $gutenberg && ! $block_editor ) {
		return false;
	  }

	  include_once ABSPATH . 'wp-admin/includes/plugin.php';

	  if ( ! is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
		return true;
	  }

	  $use_block_editor = ( get_option( 'classic-editor-replace' ) === 'no-replace' );

	  return $use_block_editor;
    }

    public function xpush_image_upload(){

	  if (isset($_GET['id']) ){
		$id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
		$image_url = wp_get_attachment_image_url($id, 'full', false);
		$image_info = pathinfo($image_url);
		$image_sizes = getimagesize($image_url);

		$data = array(
		    'url' => $image_url,
		    'ext' => $image_info['extension'],
		    'width' => $image_sizes[0],
		    'height' => $image_sizes[1]
		);

		wp_send_json_success( $data );
	  } else {
		wp_send_json_error();
	  }
    }

}
