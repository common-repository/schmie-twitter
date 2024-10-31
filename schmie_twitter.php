<?php
/*
Plugin Name: schmie_twitter
Plugin URI: http://schmiddi.co.cc/wordpress_plugins/
Description: Twitter via the normal Service or Twittermail
Version: 1.4.2
Author: Schmitt Michael
Author URI:  http://schmiddi.co.cc/wordpress_plugins/
License: A "Slug" license name e.g. GPL2
*/

/*  Copyright 2010  Schmitt, Michael  (email : Schmiddim@gmx.at)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


*/
?>
<?php
/*
 *I18N 
 * i18n commands
 xgettext -L PHP -k --keyword=_e --keyword=__ --from-code=utf8 --default-domain=schmie_twitter --output=schmie_twitter.pot *.php
 msgfmt -o schmie_twitter-de_DE.mo schmie_twitterDE.po 
 */


$path= basename(dirname(__FILE__));
$domain='schmie_twitter';
load_plugin_textdomain('schmie_twitter', false, $path . '/lang');

require_once (dirname(__FILE__).'/classes/CTo_Twitter.php');
require_once (dirname(__FILE__).'/classes/debugger/DebuggerFactory.php');
require_once (dirname(__FILE__).'/classes/CFactoryShortener.php');
require_once (dirname(__FILE__).'/classes/CToIdentica.php');
require_once (dirname(__FILE__).'/classes/Cschmie_twitter.php');
require_once (dirname(__FILE__).'/classes/CToPingfm.php');
require_once (dirname(__FILE__).'/classes/oauth/OAuth.php');



$st = new Cschmie_twitter();//hey the handlerclass for all the shit
#$debug =DebuggerFactory::deliver(DebuggerFactory::D_ECHO); //handle for the debugger
/*********Post ***************************
 * the plugin can now override the default settings for message strings at the modify post site
 * 
 * 
 */
add_action('admin_menu', 'addSchmieTwitterToPost');
 

function addSchmieTwitterToPost(){
add_meta_box( 'schmie_twitterFields', __( 'schmie_twitter', 'schmie_twitter' ), 
                'schmieTwitter_inner_custom_box', 'post', 'normal','high' );	
}
function schmieTwitter_inner_custom_box() {
echo '<input type="checkbox" name="schmie_twitterOverride" value="enabled">';
_e('Publish this Message via schmie_twitter instead of the default message', 'schmie_twitter');
echo '<br></br><br></br>';
$value= 'something new on %blog: %title %url';
echo '<label for="schmie_twitter_new_field">' . __("Available Tags: %title,%blog,%url,%date", 'schmie_twitter' ) . '</label> ';
 echo '<input type="text" name="schmie_twitter_new_field" value="'.$value.'" size="50" />';
  
}

 

/****************************************/
function post_twit($post_ID) {
	global $st ;
	$get_post_info 	= get_post( $post_ID );

	$postdate = date('U', strtotime($get_post_info->post_date));
	$postmodified 	= date('U', strtotime($get_post_info->post_modified));

	//is it a new post or is it an old but updated post?	
	$new_post=true;
	$updated_post=false;
	if ($postdate !=$postmodified){
		$new_post=false;
		$updated_post=true;
	}
	//get some properties of the post	
	$blog =get_option('blogname');	
	$title = $get_post_info->post_title;
	$date = $get_post_info->post_date;	
	$postlink=get_option('siteurl').'?p='.$post_ID;


	//Short the Url
	
	$new_url = $st->short_url($postlink);

	//format the Strings	
	
	$format = get_option('schmie_tw_format_new');
	$new_post_string = $st->format_string($title, $blog, $date, $new_url, $format);

	$format = get_option('schmie_tw_format_update');
	$edit_post_string = $st->format_string($title, $blog, $date, $new_url, $format);

	/****Avoid shortener***/
	if (get_option('schmie_tw_select_dont_shorten') =='schmie_tw_select_dont_shorten'){
		
		$format = get_option('schmie_tw_format_update');
		$new=$st->format_string($title, $blog, $date, $postlink, $format);
		$lenNew= strlen($new);
		
		$format = get_option('schmie_tw_format_update');
		$edit=$st->format_string($title, $blog, $date,$postlink,  $format);
		$lenEdit= strlen($edit);
		
		if ($lenNew<=140)
			$new_post_string = $new;
		if($lenEdit<=140)
			$edit_post_string =$edit;
		
	}//fi
	/****Avoid shortener***/	
	/***custom message desired?**/
	
	$string="__";
	if($_POST['schmie_twitterOverride'] =='enabled' &&
		trim($_POST['schmie_twitter_new_field'])!=''){
			$format =  $_POST['schmie_twitter_new_field'];
			$string = $st->format_string($title, $blog, $date, $new_url, $format);		
			$new_post_string=$string;
			$edit_post_string=$string;
	}//fi
	/***custom message desired?**/	
#echo $edit_post_string;
	/************Now: twitter the message*/
	//twitter on new post
	if ($new_post==true) {
 
		if (get_option('schmie_tw_onnew')=='schmie_tw_onnew' ||get_option('schmie_tw_onnew')=='true')

		$st->publish_message($new_post_string);
	}//fi

	//twitter on update post
	if ($updated_post ==true  ){	
		if (get_option('schmie_tw_onupdate')=='schmie_tw_onupdate' ||get_option('schmie_tw_onupdate')=='true')
		$st->publish_message($edit_post_string);
	}//fi
}//function


add_action('publish_post','post_twit');


function schmie_twitter_init() {}//init
/**** Create the Menu ****/


/** @see schmie_twitter_options.php ***/
add_action('admin_menu', 'schmie_twitter_create_menu');
function schmie_twitter_create_menu() {
	add_options_page('schmie_Twitter Settings', 'schmie_twitter Settings', 'administrator', __FILE__, 		'schmie_twitter_settings_page', __FILE__);


}//function



/**** Save Settings **/
add_action( 'admin_init', 'register_mysettings' );
function register_mysettings() {
	global $st;
	$st->register_mysettings();



/********workaround for the true issue*/

$arr = array ( 'schmie_tw_onnew','schmie_tw_onupdate','schmie_tw_select_service_identica',
'schmie_tw_select_service_twitter');

foreach ($arr as $value) {
	if (strcmp(get_option ($value) ,'true')==0){
echo $value .  ': ' . get_option($value).'<p>';
		update_option($value, $value);
	}
}

/***workaround **********/
}//function





/**** Save Settings *************/

/****** Show the settings Page
simply  include the File with HTML CODE
***@see schmie_twitter_options.php
**/


function schmie_twitter_settings_page() {
	if ( file_exists ( dirname(__FILE__).'/schmie_twitter_options.php' )) {
		include( dirname(__FILE__).'/schmie_twitter_options.php' );
	} else {
		e( '<p>Couldn\'t locate the settings page.</p>', 'wp-to-twitter' );
	}//fi
}//function

?>
