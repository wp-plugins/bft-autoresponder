=== BFT Autoresponder ===
Contributors: Bobby Handzhiev, prasunsen
Tags: autoresponder, auto responder, mailing list, newsletter
Requires at least: 2.0.2
Tested up to: 3.5.1
Stable tag: trunk

This plugin allows scheduling of automated autoresponder messages and managing a mailing list.

== Description ==

[PRO](http://calendarscripts.info/bft-pro/ "Go Pro") |
[Support Forum](http://calendarscripts.info/forum/ "Support if you don't have a Wordpress acccount")


This plugin allows scheduling of automated autoresponder messages and managing a mailing list. You can add/edit/delete and import/export members. There is also a registration form which can be placed in any website or blog. You can schedule unlimited number of email messages.

From version 1.5 you can also send fixed date messages.

== Installation ==

1. Unzip the contents and upload the entire `bft-autoresponder` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the sender address and other options from the main page accessible from the newly appeared "BFT Autoresponder" menu.
4. Manage your email messages and mailing list.
5. In order to send automated sequential emails every day your blog should be visited at least once daily. If this is not happening, please set up a cron job to visit it.

== Changelog ==

= Changes in 1.9: =

1. Shortcodes get executed in messages. Be careful with this though as CSS and Javascript effects will not always work.
2. Optional notification when new user registers (and confirms their email, if double opt-in is selected)
3. Optional notification when user unsubscribes

= Changes in version 1.8: =
1. Sortable mailing list + visual improvements
2. Localization-friendly (pot file inside)
3. Of course various bug fixes as always

= Changes in version 1.7: =

1. Using wp_mail so now you can use any of the existing SMTP plugins
2. Rich text editor available to format the messages
3. Shortcode available for the signup form
4. Code fixes and bug fixes

== Frequently Asked Questions ==

= Can I send unlimited messages? =

Yes, there is no limitation. However you'd better not set more than one email to be sent at the same number of days after user registration.

= Is there unsubscribe link? =

Yes, unsubscribe link is automatically added to every outgoing message.