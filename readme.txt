=== schmie_twitter ===
Contributors: schmiddim
Donate link: http://schmiddi.co.cc/wordpress_plugins/
Tags: twitter, twittermail, twitter without access to twitter,ping.fm, deutsch
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag:  1.4.2


== Installation ==
Download the zip file in your plugindirectory (wp-content/plugins/) and extract it. Activate it and configure it in the adminpanel (Settings->schmie_twitter Settings)


== Description ==
Schmie_twitter is really awesome! When you update or publish a new post on your Wordpress Blog, 
it will notifiy your followers on Ping.fm Twitter and / or  Identica!!!.  
You can define default messages that are pushed to your Message services or create custom Messages for 
every Post. 
If you're hoster blocks Twitter (like my Hoster), you can use Twittermail. 
You only need an emailadress on your webspace. 
The plugin supports several Url-shortening-services (is.gd,bit.ly,i2h and tinyurl).
 If your favortive service is missing, write me an email (schmiddim@gmx.at).
It is possible now to use Ping.fm. All you need is of course an account there, 
The User Api Key and an Api Key.


== Frequently Asked Questions ==
= The Twittermail function does not work = 
You need an Email service on your Webspace. 
It won't work with Mailservices like
Googlemail oder Gmx.  

= It takes some time until the message arrives on twitter =
Thats a problem with your Webhoster. For example on byethost, it takes up to
30 minutes until your twitter status is updated. 

= Whats the difference between Ping.fm API Key and Ping.fm User Api Key? =
I am still waiting for the plugins approval trough Ping.fm. If they approve it, you only need your 
User Api Key. Until then, you must request a developer Key.  
You can get your User Api key here: http://ping.fm/key/. 
The additional  API Key is required to enable the communication between Schmie-twitter and Ping.fm. 
You can make the request here: http://ping.fm/developers/. 
I made some Screenshots of this procedure. 
Have a look at my page: http://schmiddi.co.cc/wordpress_plugins/. 

== Screenshots ==
1. Have a look at the adminpanel
2. Connection to Ping.fm 
3. Setup the Message
4. Embedded in the Create Post Site
== Changelog ==

= 1.0 =
* initial version
= 1.0.1 = 
* removed the uninstall script, some small issues, but nothing dangerous.
= 1.0.2 =
* started with i18n 
= 1.2 = 
* Identica Support! The Plugin speaks partial deutsch 
= 1.2.2 = 
* Uninstall Script, default values
= 1.2.3 = 
* Improved german language^^
= 1.2.5 =
* Checkbox Bug fixed!
= 1.3 = 
* Hey, ping.fm support!
= 1.3.5 = 
* german language improved
= 1.3.8 = 
* Hey you got now a last chance to modify your shortmessage at the Add new Post Site! 
== Upgrade Notice ==
= 1.0 =
* feel free to upgrade. 
= 1.1.0 = 
* the plugin speaks deutsch. Feel free to translate it to other languages
= 1.2.0 = 
* Support for Identica
= 1.4 =
* OAuth Support for Twitter
* You can tweet with the original url, if there's enough space left (thanks to Luciano http://cbasites.net/ for the idea:)
= 1.4.1 = 
* Implemented some functions for error handling. Added a "Twitter Connect" Button. HF^^