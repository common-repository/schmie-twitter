<?php 
require_once (dirname(__FILE__).'/classes/CTo_Twitter.php');
#require_once (dirname(__FILE__).'/classes/CUrlshortener.php');
require_once (dirname(__FILE__).'/classes/CFactoryShortener.php');
require_once (dirname(__FILE__).'/classes/CToIdentica.php');
require_once (dirname(__FILE__).'/classes/CToPingfm.php');
require_once (dirname(__FILE__).'/classes/Cschmie_twitter.php');
require_once (dirname(__FILE__).'/classes/oauth/OAuth.php');
#$tw = new To_Twitter("My Name is schmie_twitter and this is a test. HF^^");
$tc = new To_Identica("My Name is schmie_twitter and this is a test. HF^^");
$tp = new To_Pingfm("My Name is schmie_twitter and this is a test. HF^^");

/**
 * oauth stuff
 * 
 */


?>

<div class="wrap">
<!-- paypal button -->
<?php 
$directory = get_bloginfo( 'wpurl' ) . '/' . PLUGINDIR . '/' . dirname( plugin_basename(__FILE__) );
?>
<div class="resources">
<img src="<?php echo $directory; ?>/img/logo.png" alt="logo schmie_twitter"/>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="BFY68RCTU3TZJ">
<input type="image" src="https://www.paypal.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form>

</div>
<!-- -->
<?php
/***********************************
*GENERATOR FOR WORDPRESS FIELDS
*@see: also usable for own project
*
*************************************/
/**** Checkbox function ***
A superb function that generates a radio button incl. the options management

*/

function schmie_generate_checkbox ($option) {

	$checked="";

	if (get_option($option)==$option)
		$checked= 'checked="checked"';
	echo "<input type=\"checkbox\" name=\"$option\" id=\"$option\" value=\"$option\" $checked/>";

} 
 

/**** Select function ****
A superb function that generates a select field incl. the options management

*/
function schmie_generate_selecter($option, $values_arr){
$selected=get_option($option);
echo "<select name=\"$option\"=\"$selected\", size=\"1\">";
		echo "<option>$selected</option>";
		foreach($values_arr as $val) {
			if ($val!=$selected)
				echo "<option>$val</option>";
		

		}//each
echo "</select>";
}//function


function schmie_generate_textfield($description,$option,$password=false, $size=0) {
	$option_val=get_option($option);
	$type='text';
	$str_size='';
	if ($password)
		$type='password';
	if ($size>0)
		$str_size="size=\"$size\"";


echo "	<tr valign=\"top\">
        <th scope=\"row\">$description</th>
        <td><input type=\"$type\" name=\"$option\" value=\"$option_val\" $str_size /></td>
        </tr>";


}//function


/****************generators***********/
/**
 * Flush Twitter Settings
 
 * 
 */
	function flushSettings(){
	$file = dirname(__FILE__) . '/schmie_twitter.php';
	$signInAgain=__('Flush Settings - double click for verify ', 'schmie_twitter');
	$value='true';
	if ($_GET['flush_tw']=='true'){
		$value='false';
		delete_option('schmie_tw_oauth_screen_name');
		delete_option('schmie_tw_oauth_token');
		delete_option('schmie_tw_oauth_token_secret');	
		delete_option('schmie_tw_verifer');		
	#	oauthTwitter();
	}//fi 
	

	echo  "<tr><td></td><td><a href=\"options-general.php?page=$file&flush_tw=$value\">$signInAgain</a></td></tr>";
	
	#oauthTwitter();
	
	}//function


	function genAuthorizeUrl($oauthInfo){
		/*** twitter buttons http://twibs.com/oAuthButtons.php*/
			$directory = get_bloginfo( 'wpurl' ) . '/' . PLUGINDIR . '/' . dirname( plugin_basename(__FILE__) );	//for the image
			$authUrl= $oauthInfo->generateAuthorizeUrl();	
			$url= "<p><a  target=\"_blank\" href=\"$authUrl\">
			<img src=\"$directory/img/twitter_button_1_hi.gif\" alt=\"\sign in Twitter\"
		
			</a><p>";
			$msg =__('You have to sign in with twitter. Please press the Button and follow Instructions on Twitter', 'schmie_twitter');
			echo "<td>$msg</td>";
			echo "<td>$url</td>";						
			schmie_generate_textfield(__("Please Enter the Pin:", 'schmie_twitter'), 'schmie_tw_verifer');
			flushSettings(); 
		}
	
 	//name is set, connection works
	function showUser($oauthInfo){
		if ($oauthInfo->screen_name != '' ){
			$name=$oauthInfo->screen_name;
		 	$loggedAs=	__('logged in as', 'schmie_twitter');	 
		 	echo "<tr><td>$loggedAs</td><td><a href=\"http://twitter.com/$name\">$name</a></td></tr>";
			flushSettings();	
			#oauthTwitter();
		
		}//fi
	}//function
function oauthTwitter(){
	
	$schmie_twitter = new CSchmie_twitter();
	$oauthInfo= $schmie_twitter->getAdtOauthInfo('twitter');
	

	showUser($oauthInfo);
	$auth = new OAuth($oauthInfo);						
	if ($oauthInfo->oauth_token==''){
		
		$oauthInfo=$auth->requestToken();		
	 	update_option('schmie_tw_oauth_token', $oauthInfo->oauth_token);
		update_option('schmie_tw_oauth_token_secret', $oauthInfo->oauth_token_secret);
	
		}
		//Pin has to be entered
	if( $oauthInfo->oauth_verifier==''){		GenAuthorizeUrl($oauthInfo);	} 
	
	/***
	 * ACCESS
	 */
	
	if ($oauthInfo->oauth_token!='' && $oauthInfo->oauth_verifier!='' && $oauthInfo->screen_name==''){						
			$auth = new Oauth($oauthInfo); 		 
			$infoNew= $auth->requestAccessToken(); 
		
		
			if ($infoNew != null){
				update_option('schmie_tw_oauth_token', $infoNew->oauth_token);
				update_option('schmie_tw_oauth_token_secret', $infoNew->oauth_token_secret);
				update_option('schmie_tw_oauth_screen_name', $infoNew->screen_name);
				oauthTwitter();
			}else {
				$invalid=__('invalid PIN', 'schmie_twitter');
				echo "<tr><td><h3>$invalid</h3></td></tr>";
				update_option('schmie_tw_oauth_token','');
				update_option('schmie_tw_oauth_token_secret', '');
				update_option('schmie_tw_oauth_screen_name','');
				update_option('schmie_tw_verifer','');
				$oauthInfo->oauth_token='';	
				$oauthInfo->oauth_token_secret='';
				$oauthInfo->oauth_verifier='';
				genAuthorizeUrl($oauthInfo);
			}	//fi		
			
		}//fi
		
		
		
		

		
}//function




/*********************************************/
?>



<h2><?php _e('Schmie Twitter Settings', 'schmie_twitter')?></h2>
<h3><?php _e('Communication with Twitter', 'schmie_twitter')?></h3>

<form method="post" action="options.php">
    <?php settings_fields( 'schmie_settings_group' ); ?>
    
    
  
    <table class="form-table">
	<tr valign="top">
	<th scope="row"><h4><?php _e('Account Settings for Twitter', 'schmie_twitter');?></h4></th>
	</tr>
	
	<!--  sign in usw. -->
	<tr>
	<?php 
	oauthTwitter();
	?>
	</tr>

	<tr valign="top">
	<!-- TWITTERMAIL -->
	<th scope="row"><h4><?php _e("Account Settings for Twittermail", 'schmie_twitter')?></h4></th>
	</tr>

<!-- <tr valign="top"> -->	
<tr><td></td>
	<td>
	<?php _e('If your Hoster blocks Twitter, you can use Twittermail. You can send an email to your twittermail address and the service post it to twitter. <a href="http://twittercounter.com/pages/twittermail">Visit Site to set up your account</a></th>', 'schmie_twitter'); ?>
	</td></tr>
	
	<?php schmie_generate_textfield(__("Twittermail Name", 'schmie_twitter'), 'schmie_tw_twittermail'); ?>     
<!--	<?php schmie_generate_textfield("Yor Emailaddress on the Server", 'schmie_tw_mail'); ?>     -->
  
  <tr valign="top">
        <th scope="row"><?php echo __('Use', 'schmie_twitter');?> </th>
	<td>	
		<?php
		/**** Make your Choice: Twitter or Twittermail */
		$values = array('Twitter', 'Twittermail');
		schmie_generate_selecter('schmie_tw_select', $values);		
		?>
	</td>
        </tr>

		
<!----------IDENTICA - -->
<tr valign="top">
<td><h3><?php _e('Communication with Identica', 'schmie_twitter')?></h3></td>
</tr>
<?php schmie_generate_textfield(__("Identi.ca Username", 'schmie_twitter'), 'schmie_tw_identica_usr'); ?>     
<?php schmie_generate_textfield(__("Identi.ca Password", 'schmie_twitter'), 'schmie_tw_identica_pwd',true); ?>     
<?php 	


if (get_option('schmie_tw_identica_pwd')!="") {
	
	echo "<tr valign=\"top\">
        	<th scope=\"row\"></th>

        	<td><a href=\"options-general.php?page=$file&test_id=true\">Test Identica</a></td>
        	</tr>"
	;

	 }


	
	/** Check if Identica is reachable **/


	if ($_GET['test_id']=='true'){
		$usr= get_option('schmie_tw_identica_usr');
		$pwd= get_option('schmie_tw_identica_pwd');

		$retval=$tw->is_twitter_reachable();
		if ($retval==true){
			$tc->talk_via_curl($usr,$pwd);
			$string = __("I can reach Identica. Have a look at your Profile", 'schmie_twitter');
		}else{
			$string = __("Unable to reach Identi.ca. Contact your Hoster", 'schmie_twitter');
		}
		echo "
	
	<tr valign=\"top\">
		<td><h5>$string</h5></td>

	</tr>";
	}//fi
?>


<!----------Ping FM - -->
<tr valign="top">
<td><h3><?php _e('Communication with Ping.fm', 'schmie_twitter')?></h3></td>
</tr>

<tr valign="top"><td></td>
<td><?php _e('Ping.fm is a simple service that can update your status on several social networks. If you use this, Schmie_twitter pushes a message on this site and they will forward it to every social network you are connected with. All you need is the User Api Key. You can get it <a href="http://ping.fm/key/">here</a>', 'schmie_twitter');?>

</td>
</tr>
<?php schmie_generate_textfield(__("Desktop / Web Key", 'schmie_twitter'), 'schmie_tw_ping_fm_user_api','', $size=55); ?>     

<?php schmie_generate_textfield(__("API Key", 'schmie_twitter'), 'schmie_tw_ping_fm_api_key','', $size=55); ?>   


<tr valign="top">
	<td></td>
	<td>
	<?php _e('If you got issues with the Connection to Ping.fm visit','schmie_twitter');?>
	<a href="http://schmiddi.co.cc/wordpress_plugins/">schmiddi.co.cc/wordpress_plugins/</a>
	</td>
</tr>


<!-- URL  Shortener -->
<tr valign="top">
<td><h3><?php _e('Url Shortener');?></h3></td>
</tr>


<tr valign="top"><td><?php _e('At the Moment are these services supported:', 'schmie_twitter'); ?><br></br>
<a href="http://is.gd">is.gd</a>	
<a href="http://bit.ly/">bit.ly</a>	
<a href="http://i2h.de">i2h</a>	
<a href="http://tinyurl.com/">tinyurl</a>

</tr>
	<tr valign="top">
	<th scope="row"><?php _e('Use', 'schmie_twitter');?> </th>
	<td>
	<?php
	/*** which shortener should be used ??*/
	$values=array('is.gd', 'bit.ly', 'i2h.de','tinyurl.com');
	schmie_generate_selecter('schmie_tw_shortener_select',$values);
	?>
	</td>
        </tr>

<!-- for bit.ly -->
	<?php schmie_generate_textfield(__("Your Bit.ly Username", 'schmie_twitter'), 'schmie_tw_bitly_usr'); ?>     
	<?php schmie_generate_textfield(__("Your Bit.ly API Key", 'schmie_twitter'), 'schmie_tw_bitly_api'); ?>     
	<?php 	if (get_option('schmie_tw_bitly_usr')!="") {?>

		<tr valign="top">
	        <th scope="row"></th>
        <td><a href="options-general.php?page=schmie_twitter/schmie_twitter.php&testbitly=true">Test Bit.ly</a></td>

        </tr>
<?php } ?>
<?php 
	if ($_GET['testbitly']=='true'){
		$u = new UrlShortener('http://www.google.de');
		$login= get_option('schmie_tw_bitly_usr');
		$api=get_option('schmie_tw_bitly_api');
		$u->getShortUrlBitly($login, $api);
		
	        if( !$u->has_errors()) {
			$string = __("I can reach bit.ly.)", 'schmie_twitter');
		} else {
			$string=__("Please check your Username and API Key.<p>Error message was:", 'schmie_twitter').$u->get_errors();
		}	

		echo "<tr valign=\"top\"><td><h5>$string</h5></td></tr>";
	}//fi
?>

<tr valign="top">
<th scope="row">
<?php schmie_generate_checkbox("schmie_tw_select_dont_shorten",""); 
_e("Don't short the Url if text has less than 140 characters" , 'schmie_twitter'); ?>
</th></tr>
<!--- format the twitter string -->
<!--
%title		post title
%blog		blog titel
%date		post date
%url		post url


-->
<!--  title -->
<tr valign="top"><td><h3><?php _e('Message Format', 'schmie_twitter');?></h3></td></tr>
<tr valign="top">
<th scope="row"><?php _e('Hey there are some tags available for formatting your Tweets
Example: Hey there\'s a new Post on %blog: %title. Visit %url', 'schmie_twitter');?></th>
<td>
<table>
<tr><td>%title</td><td><?php _e('Title of your Post', 'schmie_twitter');?></td>
<td>%blog</td><td><?php _e('Name of the Blog', 'schmie_twitter');?></td></tr>
<tr><td>%date</td><td><?php _e('Date of Post', 'schmie_twitter'); ?></td>
<!--<td>%category</td><td><?php _e('1st Category of Post', 'schmie_twitter')?></td></tr>-->
<td>%url</td><td><?php _e('Url of the Post', 'schmie_twitter');?></td></tr>
</table>

</td>

</tr>


<!--entry -->
<tr valign="top">
<th scope="row">
<?php schmie_generate_checkbox("schmie_tw_onnew",""); _e('Tweet on new post', 'schmie_twitter');?> </th>

<td><input type="text" name="schmie_tw_format_new" value="<?php echo get_option('schmie_tw_format_new');?>" size="55"/></td>
</tr>


<tr valign="top">
<th scope="row">
<?php schmie_generate_checkbox("schmie_tw_onupdate",""); _e('Tweet on update post', 'schmie_twitter'); ?></th>

<td><input type="text" name="schmie_tw_format_update" value="<?php echo get_option('schmie_tw_format_update');?>" size="55"/></td>
</tr>

<!--  USE THESE SERVICES-->
<tr valign="top"><td><h3><?php _e('Use these Services', 'schmie_twitter');?></h3></td></tr>
<tr valign="top">
<th scope="row">
<?php schmie_generate_checkbox("schmie_tw_select_service_twitter",""); 
_e('Publish Messages on Twitter', 'schmie_twitter'); ?>
</th>
</tr>

<?php 
?>

<tr valign="top">
<th scope="row">
<?php schmie_generate_checkbox("schmie_tw_select_service_identica",""); 
_e('Publish Messages on Identica', 'schmie_twitter'); ?>
</th></tr>
<tr valign="top">
<th scope="row">
<?php schmie_generate_checkbox("schmie_tw_select_service_pingfm",""); 
_e('Publish Messages on Ping.fm', 'schmie_twitter'); ?>
</th>
</tr>

    </table>
   <!-- ********************************submit *************************-->
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'schmie_twitter') ?>" />
    </p>
		

</form>
</div>
