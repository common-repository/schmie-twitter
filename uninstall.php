<?php

if(!defined('WP_UNINSTALL_PLUGIN') )
    exit();
delete_option( 'schmie_tw_user' );
delete_option( 'schmie_tw_pass' );
delete_option( 'schmie_tw_twittermail' );
delete_option( 'schmie_tw_select' );
delete_option('schmie_tw_shortener_select');
delete_option('schmie_tw_bitly_api');
delete_option('schmie_tw_bitly_usr');
delete_option('schmie_tw_onnew');
delete_option('schmie_tw_format_new');
delete_option('schmie_tw_onupdate');
delete_option('schmie_tw_format_update');
delete_option('schmie_tw_identica_usr');
delete_option('schmie_tw_identica_pwd');
delete_option('schmie_tw_select_service_identica');
delete_option('schmie_tw_select_service_twitter');


?>
