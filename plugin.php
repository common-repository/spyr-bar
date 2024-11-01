<?php
/*
Plugin Name: (SPYR) Network Bar
Plugin URI: http://spyr.me
Description: Easily link together your network of sites with the (SPYR) Network Bar. 
Author: Spyr Media
Version: 1.0.1
Author URI: http://spyr.me
*/   

if (!class_exists('spyr_bar')) {
	class spyr_bar {
		var $options_name = 'spyr_bar_options';
		var $version = '1.0.1';
		var $url = '';
		var $path = '';
		var $options = array();
		var $admin_page_name = '';
		var $form_submitted = '';
		var $textures = array (
			array("name"=>"None","value"=>"none"),
			array("name"=>"Gradient","value"=>"gradient"),
			array("name"=>"Texture #1","value"=>"texture-1"),
			array("name"=>"Texture #2","value"=>"texture-2")
			);

		/*** Localization
		var $localizationDomain = "spyr_bar"; */

		function __construct(){
			$this->url = plugin_dir_url(__FILE__);
			/* $this->path = plugin_dir_path(__FILE__); */
			$this->get_options();

			if (!current_theme_supports('spyr-admin-menu')) { $this->admin_page_name = 'spyr_options'; }
			else { $this->admin_page_name = 'spyr_bar_options'; }

			add_theme_support('menus');
			register_nav_menu('spyr_bar','(SPYR) Network Bar');			

			add_action('admin_menu',array(&$this,'admin_menu'));
			add_action('admin_init',array(&$this,'register_admin_css'));
			add_action('admin_print_styles',array(&$this,'enqueue_admin_css'));
			
			add_action('wp_enqueue_scripts',array(&$this,'init_frontend'),99);
			add_action('wp_footer',array(&$this,'add_spyr_bar'));

		/*** Localization
			$locale = get_locale();
			$mo = dirname(__FILE__) . "/languages/" . $this->localizationDomain . "-".$locale.".mo";
			load_textdomain($this->localizationDomain,$mo); */

		/*** Widget Registration
			add_action('plugins_loaded',array(&$this,'register_widgets')); */
			}

		function init_frontend() {
			if ($this->options['spyr_bar_enable_font']) { wp_enqueue_style('spyr-bar-font','http://fonts.googleapis.com/css?family=Open+Sans:400,700',false,$this->version); }
			wp_enqueue_style('spyr-bar-style',$this->url . 'style.css',false,$this->version);
			if ($this->options['spyr_bar_stylesheet'] <> '') { wp_enqueue_style('spyr-bar-external-style',$this->options['spyr_bar_stylesheet'],false,$this->version); }
			wp_enqueue_script('spyr-bar-script',$this->url . 'js/init.js',array('jquery'));
			}

		function add_spyr_bar() {
			$spyr_bar_network_text = stripslashes($this->options['spyr_bar_network_text']);
			if ($spyr_bar_network_text == '') {
				$spyr_bar_network_text = 'Enter Your Network Text';
				$spyr_bar_url = get_bloginfo('wpurl')  . '/wp-admin/admin.php?page=' . $this->admin_page_name;
				}
			else { $spyr_bar_url = $this->options['spyr_bar_url']; } ?>
<div id="spyr_bar" class="sp_<?php echo $this->options['spyr_bar_texture']; ?>">
	<div id="spyr_bar_wrap">
		<dl class="spyr_bar_nav">
			<dt><a href="<?php echo $spyr_bar_url; ?>" id="spyr_bar_logo"><?php echo $spyr_bar_network_text; if(has_nav_menu('spyr_bar')) { ?> <span id="spyr_bar_arrow"></span><?php } ?></a></dt>
			<dd>
				<?php wp_nav_menu(array('theme_location' => 'spyr_bar','fallback_cb' => 'false')); ?>
				</dd>
			</dl>
		<div id="spyr_bar_message"><?php echo stripslashes($this->options['spyr_bar_message']); ?></div>
		</div>
	</div>
<?php
			}

		function get_options() {
			if (!$theOptions = get_option($this->options_name)) {
				$theOptions = array('default'=>'options');
				update_option($this->options_name,$theOptions);
				}
			$this->options = $theOptions;
			}

		function save_options() {
			return update_option($this->options_name,$this->options);
			}

		function admin_menu() {
			if (!current_theme_supports('spyr-admin-menu')) {
				add_theme_support('spyr-admin-menu');
				add_menu_page('Spyr Media','Spyr Media','edit_theme_options','spyr_options',array(&$this,'options_page'),$this->url . '/images/spyr_admin_icon.png','58.997');
				add_submenu_page('spyr_options','Network Bar','Network Bar','edit_theme_options','spyr_options',array(&$this,'options_page'));
				}
			else {
				add_submenu_page('spyr_options','Network Bar','Network Bar','edit_theme_options','spyr_bar_options',array(&$this,'options_page'));
				}
			add_filter('plugin_action_links_' . plugin_basename(__FILE__),array(&$this,'filter_plugin_actions'),10,2);
			}

		/*** Add Settings link to plugins page */
		function filter_plugin_actions($links,$file) {
			$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=' . $this->admin_page_name . '">' . __('Settings') . '</a>';
			array_unshift($links,$settings_link);
			return $links;
			}

		function register_admin_css() { wp_register_style('spyr_bar_admin',$this->url . 'style-admin.css',false,$this->version); }
		function enqueue_admin_css() { wp_enqueue_style('spyr_bar_admin'); }

		function options_page() { 
			$form_submitted = $_POST['spyr_bar_update_options'];
			if($form_submitted){
				if (! wp_verify_nonce($_POST['_wpnonce'],'spyr_bar_options_updated')) { die('Whoops! There was a problem with the data you posted. Please go back and try again.');  }
				$this->options['spyr_bar_network_text'] = $_POST['spyr_bar_network_text'];                   
				$this->options['spyr_bar_url'] = $_POST['spyr_bar_url'];
				$this->options['spyr_bar_message'] = $_POST['spyr_bar_message'];
				$this->options['spyr_bar_stylesheet'] = $_POST['spyr_bar_stylesheet'];
				$this->options['spyr_bar_enable_font'] = ($_POST['spyr_bar_enable_font']=='on')?true:false;
				//$this->options['spyr_bar_texture'] = $_POST['spyr_bar_texture'];
				$this->save_options();
?><div class="updated option_updated"><?php _e('<h4>Options Saved!</h4>But Our Princess Is In Another Castle!'); ?></div><?php
				} ?>
<div id="spyr_bar_options" class="wrap">
	<a href="http://spyr.me" class="option_heading"><img src="<?php echo $this->url; ?>/images/spyr_bar_admin_logo.png" title="WordPress Developer: Spyr Media" alt="WordPress Developer: Spyr Media" /></a>
	<form name="options-form" method="post">
		<?php wp_nonce_field('spyr_bar_options_updated'); ?>
		<div class="option_wrap">
			<div class="option_item">
				<h3 class="option_title">Network Text</h3>
				<p class="option_description">Enter the title of your network link.<br />ex. a Spyr Media site</p>
				<p><input type="text" name="spyr_bar_network_text" value="<?php echo htmlspecialchars(stripslashes($this->options['spyr_bar_network_text']),ENT_COMPAT); ?>" class="regular-text" /></p>
				<p class="checkbox"><input type="checkbox" id="spyr_bar_enable_font" name="spyr_bar_enable_font" <?=($this->options['spyr_bar_enable_font']==true)?'checked="checked"':''?> /> <label for="spyr_bar_enable_font">Enable Open Sans Font</label></p>
				<p class="option_description">Enable Open Sans as the default font styling of your Network Text.<br />If you're styling the Network Text manually then leave this unchecked.</p>
				</div>
			<div class="option_item">
				<h3 class="option_title">Network URL</h3>
				<p class="option_description">Enter the main URL of your network.<br />ex. http://spyr.me</p>
				<p><input type="text" name="spyr_bar_url" value="<?php echo htmlspecialchars(stripslashes($this->options['spyr_bar_url']),ENT_COMPAT); ?>" class="regular-text" /></p>
				</div>
			<div class="option_item">
				<h3 class="option_title">Additional Message</h3>
				<p class="option_description">Add a message to the right side of the (SPYR) Network Bar.<br />To disable the message simply clear the textbox.</p>
				<p><input type="text" name="spyr_bar_message" value="<?php echo htmlspecialchars(stripslashes($this->options['spyr_bar_message']),ENT_COMPAT); ?>" class="regular-text" /></p>
				</div>
			<div class="option_item">
				<h3 class="option_title">External Stylesheet</h3>
				<p class="option_description">Add an external stylesheet to consistently style all network sites.<br />(enter the URL of remote stylesheet)</p>
				<p><input type="text" name="spyr_bar_stylesheet" value="<?php echo htmlspecialchars(stripslashes($this->options['spyr_bar_stylesheet']),ENT_COMPAT); ?>" class="regular-text" /></p>
				</div>
			<div class="option_item">
				<h3 class="option_title"><a href="<?php get_bloginfo('wpurl'); ?>/wp-admin/nav-menus.php">Attach Your Menu</a></h3>
				<p class="option_description"><a href="<?php get_bloginfo('wpurl'); ?>/wp-admin/nav-menus.php">Attach your menu</a> to the (SPYR) Network Bar Theme Location<br />to enable dropdown navigation.</p>
				</div>
			</div>
		<p class="submit"><input type="submit" name="spyr_bar_update_options" value="<?php _e('Save the Options!') ?>" /></p>
		<h2 class="option_note_area">&nbsp;</h2>
		</form>
	</div><?php
			}
		}
	}

if (class_exists('spyr_bar')) { $spyr_bar_var = new spyr_bar(); }