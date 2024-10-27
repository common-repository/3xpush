<?php

class xPush_Public_Class
{

    private $plugin_name;

    private $version;

    public function __construct( $plugin_name, $version ) {

	  $this->plugin_name = $plugin_name;
	  $this->version = $version;
    }

    public function xpush_enqueue_styles() {}

    public function xpush_enqueue_scripts() {

	  $settings = maybe_unserialize(get_option('xpush_settings'));
	  $this_page = get_queried_object();

	  if ($settings['psx_tag'] == 'category') {

		$categories = wp_get_post_categories($this_page->ID, array('fields' => 'all'));

		if (count($categories) > 0) {

		    $tag = $categories[0]->name;
		}
	  }

	  else {

		$tag = '';
	  }

	  $settings['psx_time'] = round($settings['psx_time'] * 1000);
	  $blocksite = ($settings['blocksite'] == 'check') ? 1 : 0;
	  $hasBlockCross = ($settings['hasBlockCross'] == 'check') ? 1 : 0;

	  ?>
	  <script>
		psx_host = '3xpush.com';
		psx_site_id = <?php echo $settings['psx_site_id']; ?>;
		psx_sub_id = '';
		psx_tag = '<?php echo $tag; ?>';
		psx_time = <?php echo $settings['psx_time']; ?>;
		blocksite = <?php echo $blocksite; ?>;
		hasBlockCross = <?php echo $hasBlockCross; ?>;
		blockText = '<?php echo $settings['blockText']; ?>';
		(function(d){let s=d.createElement('script');s.async=true;s.src='https://'+psx_host+'/new.js';d.head.appendChild(s);})(document);
	  </script>
	  <?php
    }
}
