<?php
/*
Plugin Name: Easy Gallery
Plugin URI: https://github.com/shankpaul/easygallery
Description: For easily create and manage photo gallery for wordpress webpages.
Author: Shan K Paul
Version: 1.0
Author URI: 
*/
define('EASY_GALLERY_PLUGIN_URL',  get_option('siteurl').'/wp-content/plugins/easy-gallery/');
global $wpdb;
/*
 * Insatll Easy Gallery pugin 
 * Code Works when Plugin Activated
 */
function install_easy_gallery()
{
    /*
     * install tables to database
     */    
    include('easy_config.php');
    add_option("EasyGalleryVersion",'2.0.0');    
    //Default Settings
    add_option("EasyGallery-thumb-height",180);
    add_option("EasyGallery-thumb-width",242);
    add_option("EasyGallery-album-cover-height",404);
    add_option("EasyGallery-album-cover-width",538);
    add_option("EasyGallery-album-cover-crop",true);
    add_option("EasyGallery-image-height",400);
    add_option("EasyGallery-image-width",500);
    
  }
/*
 * Uninstall Easy Gallery
 * Code Works when Plugin Deactivated
 */
function uninstall_easy_gallery()
{
	delete_option("EasyGalleryVersion");
  /*
 	* Thumnail settings 
  */
 	delete_option("EasyGallery-thumb-width");
 	delete_option("EasyGallery-thumb-width"); 
  /*
 	* Gallery image settings 
  */
 	delete_option("EasyGallery-image-width");
 	delete_option("EasyGallery-image-width");  
 }

 function add_stylesheets_easy() { 	
 /*
 * Easy gallery style
 */
 $css_path = EASY_GALLERY_PLUGIN_URL . 'css/easy-front-end.css';    
 wp_register_style( 'EasyGalleryStyles', $css_path ); 
 wp_enqueue_style( 'EasyGalleryStyles' );
 /*
 * Swipe-box Plugin styles
 */
 $css_path=EASY_GALLERY_PLUGIN_URL . 'thirdparty/swipe-box/source/swipebox.css';
 wp_register_style( 'EasyGalleryThirdPartyStyles', $css_path ); 
 wp_enqueue_style( 'EasyGalleryThirdPartyStyles' ); 
/*
 *Fancy-box Plugin styles
 */
$css_path=EASY_GALLERY_PLUGIN_URL . 'thirdparty/fancy-box/jquery.fancybox.css';
wp_register_style( 'EasyGalleryThirdPartyStylesFancy', $css_path ); 
wp_enqueue_style( 'EasyGalleryThirdPartyStylesFancy' );	 
}
function add_javascript_easy() {		
	/*
	 * Load Jquery
	 */	
	if(!get_option("DocManagerVersion") )
	//if(!wp_script_is('jquery')) 
	{
		$js_path = EASY_GALLERY_PLUGIN_URL . 'js/jquery.js';
		wp_register_script( 'EasyGalleryJquery', $js_path ); 
		wp_enqueue_script( 'EasyGalleryJquery' ); 
	}
	
	/*
	 * Load Swipe box plugin
	 */
	$js_path = EASY_GALLERY_PLUGIN_URL . 'thirdparty/swipe-box/source/jquery.swipebox.min.js';
	wp_register_script( 'EasyGalleryThirdPartyScript', $js_path ); 
	wp_enqueue_script( 'EasyGalleryThirdPartyScript',$js_path,array(),null,true ); 
	
	
	/*
	 * Fancy box script
	 */
	$js_path = EASY_GALLERY_PLUGIN_URL . 'thirdparty/fancy-box/jquery.fancybox.js';
	wp_register_script( 'EasyGalleryThirdPartyScriptFancy', $js_path ); 
	wp_enqueue_script( 'EasyGalleryThirdPartyScriptFancy',$js_path,array(),null,true  ); 
	/*
	 * Easy gallery script
	 */
	$js_path = EASY_GALLERY_PLUGIN_URL . 'js/easy-front-end.js';
	wp_register_script( 'EasyGalleryScript', $js_path ); 
	wp_enqueue_script( 'EasyGalleryScript',$js_path,array(),null,true ); 
	
}
if(!class_exists('pagination'))
	include_once ('pagination.class.php');
// if (!is_admin())
{
	include('easy-front-end.php');
	add_shortcode('easy_gallery', 'show_easy_gallery');
	add_action( 'wp_enqueue_scripts', 'add_stylesheets_easy' );
	add_action('wp_enqueue_scripts', "add_javascript_easy");
	
}

function easy_gallery_admin_options() 
{
	global $wpdb;
	$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
	switch($current_page)
	{
		case 'create':
		include('views/easy-gallery-create.php');
		break;
		case 'add':
		include('views/easy-gallery-add.php');
		break;
		case 'edit':
		include('views/easy-gallery-edit.php');
		break;
		case 'settings':
		include('views/easy-gallery-settings.php');
		break;
		case 'albumedit':
		include('views/easy-gallery-album-edit.php');
		break;
		default:
		include('views/easy-gallery-home.php');
		break;
	}
}
function easy_gallery_add_to_menu() 
{
        /*
         * call back function : easy_gallery_admin_options();
         */
        add_menu_page('Easy Gallery', 'Easy Gallery', 'edit_pages', 'easy-gallery-home', 'easy_gallery_admin_options',EASY_GALLERY_PLUGIN_URL.'images/icon.png',62.15 );

      }

//add_action('admin_menu', 'easy_gallery_add_to_menu');

      if (is_admin()) 
      {
      	add_action('admin_menu', 'easy_gallery_add_to_menu');
      	include('ajax/handle-easy-upload.php');
      	add_action('wp_ajax_easyupload', 'handle_easy_upload');
      }

      function get_easy_albums(){
      	global $wpdb;
      	$sql = "SELECT * FROM `easy_album`  where `disabled`=0";
      	$results=$wpdb->get_results($sql);
      	return $results;
      }

      function get_easy_album($album_id){
      	global $wpdb;
      	$sql = "SELECT * FROM `easy_album` where album_id = $album_id";
      	$results=$wpdb->get_results($sql);
      	return $results[0];
      }
      register_activation_hook(__FILE__, 'install_easy_gallery');
      register_deactivation_hook( __FILE__, 'uninstall_easy_gallery' );
?>