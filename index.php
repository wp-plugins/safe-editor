<?php
/*
Plugin Name: Safe Editor
Plugin URI: 	
Description: Safe Editor allows you to write custom CSS / Javascript to manipulate the appearance and behavior of themes / plugins on your website without worrying that your changes will be overwritten with the future theme / plugin updates. 
Version: 1.0
Author: Konrad Węgrzyniak
Author URI: http://forde.pl/
License: GPL2
*/

class se_options_page {
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action('init', array( $this, 'save_edit_init') );

		if(!wp_script_is('jquery')) wp_enqueue_script('jquery',false,array(),false, true);
    	wp_enqueue_style('codemirror-css', plugins_url('/codemirror/codemirror.css', __FILE__));
    	wp_enqueue_script('codemirror', plugins_url( '/codemirror/codemirror.js', __FILE__ ),array(),false, true);
    	wp_enqueue_script('codemirror-mode-css', plugins_url( '/codemirror/mode/css/css.js', __FILE__ ),array(),false, true);
    	wp_enqueue_script('codemirror-mode-js', plugins_url( '/codemirror/mode/javascript/javascript.js', __FILE__ ),array(),false, true);
    	wp_enqueue_style('safe-editor-css', plugins_url( '/css/safe_editor.css', __FILE__ ));
    	wp_enqueue_script('safe-editor-js', plugins_url( '/js/safe_editor.js', __FILE__ ),array(),false, true);
    	wp_localize_script( 'safe-editor-js', 'scriptsajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
	function admin_menu () {
		add_submenu_page( 'tools.php', 'Safe Editor', 'Safe Editor', 'manage_options', 'safe-editor', array( $this, 'settings_page' ) );
	}
	function se_tabs( $current = 'css' ) {
	    $tabs = array( 'css' => 'CSS Editor', 'js' => 'Javascript Editor');
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
	        echo "<a class='nav-tab$class' href='?page=safe-editor&tab=$tab'>$name</a>";
	    }
	    echo '</h2>';
	}
	function  settings_page () {
		?>
		<div class="wrap">
			<h2 class="se_settings_heading"><span class="dashicons-editor-code"></span> Safe Editor</h2>
			<p class="se_desc"><span>Safe Editor</span> allows you to write custom CSS / Javascript to manipulate the appearance and behavior of themes / plugins on your website without worrying that your changes will be overwritten with the future theme / plugin updates.</p>

			<?php ( isset($_GET['tab']) )? $this->se_tabs($_GET['tab']) : $this->se_tabs('css'); ?>
			<?php $tab = ( isset($_GET['tab']) )? $_GET['tab'] : false; ?>
			
			<div class="safe_editor_wrapper <?php if($tab == 'css' || !isset($_GET['tab'])) echo 'tab_vis' ?>">
				<?php 
					$css = get_option('safe_edit_css'); 
					$css = ($css)? stripcslashes($css) : ''; 
				?>
				<p class="se_tab_desc">Your custom css will be added to the <i>&lt;body&gt;</i> tag on your website "front-end" (wrapped in <i>&lt;style&gt;</i> tags).</p>
				<div id="safe_editor_tab">CSS Editor</div>
				<textarea name="code" id="safe_css_editor" class="safe_editor_css_textarea"><?php echo $css ?></textarea>
				<input type="submit" value="Save CSS editor changes" data-type="css" class="button button-primary">
			</div>

			<div class="safe_editor_wrapper <?php if($tab == 'js') echo 'tab_vis' ?>">
				<?php 
					$js = get_option('safe_edit_js'); 
					$js = ($js)? stripcslashes($js) : ''; 
				?>
				<p class="se_tab_desc">Your custom Javascript will be added to the footer of your website "front-end" (wrapped in <i>&lt;script&gt;</i> tags).</p>
				<div id="safe_editor_tab">Javascript Editor</div>
				<textarea name="code" id="safe_js_editor" class="safe_editor_js_textarea" ><?php echo $js ?></textarea>
				<input type="submit" value="Save Javascript editor changes" data-type="js" class="button button-primary">
			</div>

			<div class="donate">
				<p class="se_desc">You like the <span>Safe Editor</span>? <br>Feel free to show your appreciation.</p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="NJLNBNJ3DYNDL">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
			


			
			<script type="text/javascript">
				jQuery(document).ready(function( $ ) {					
					var css_editor = CodeMirror.fromTextArea(document.getElementById("safe_css_editor"), {
						mode:  "css",
						indentUnit: 4,
						lineNumbers: true
					});
					var js_editor = CodeMirror.fromTextArea(document.getElementById("safe_js_editor"), {
						mode:  "javascript",
			        	indentUnit: 4,
						lineNumbers: true
			      	});


			      	var saving_css = false,
						saving_js = false;

					$('.safe_editor_wrapper .button').on('click', function() {
						var t = $(this),
							textarea = null,
							value = '',
							type = t.attr('data-type');

						if(type == 'css' && saving_css) return;
						if(type == 'js' && saving_js) return;

						if(type == 'css') {
							textarea = $('.safe_editor_css_textarea');
							value = css_editor.getValue();
							saving_css = true;
						} else {
							textarea = $('.safe_editor_js_textarea');
							value = js_editor.getValue();
							saving_js = true;	
						}

						t.parent().find('.saving').remove();
						t.after('<span class="saving"></span>');

						$.ajax({
							type: 'POST',
							url: scriptsajax.ajaxurl,
							data: {
								action: 'se_save', 
								type: type,
								data: value
							},
							success: function(data, textStatus, XMLHttpRequest) {
								t.parent().after(data);
								t.parent().find('.saving').addClass('saved').fadeOut(4000, function() { $(this).remove() });
								if(type == 'css') saving_css = false; 
								if(type == 'js') saving_js = false;
							},
							error: function(MLHttpRequest, textStatus, errorThrown) {
								alert(errorThrown);
							}
						});
					});

				});
			</script>
		</div>
		<?php
	}
	function save_edit_init() {
		add_action('wp_footer', array($this, 'save_edit_js'), 100);
		add_action('wp_head', array($this, 'save_edit_css'));
	}
	function save_edit_css() {
		$css = get_option('safe_edit_css');
		if($css) {
			echo '<style id="save_edit_css" type="text/css">'.stripcslashes($css).'</style>';
		}
	}
	function save_edit_js() {
		$js = get_option('safe_edit_js');
		if($js) {
			echo '<script id="save_edit_js" type="text/javascript">'.stripcslashes($js).'</script>';
		}
	}
}
new se_options_page;




function se_save() {
	//echo "<pre>"; print_r($_POST); echo "</pre>"; 
	switch($_POST['type']) {
		case 'css' : 
			update_option( 'safe_edit_css', $_POST['data']);
			break;
		case 'js' : 
			update_option( 'safe_edit_js', $_POST['data']);
			break;
	}
    die();
}
add_action( 'wp_ajax_nopriv_se_save', 'se_save' );
add_action( 'wp_ajax_se_save', 'se_save' );





