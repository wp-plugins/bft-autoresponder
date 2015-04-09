=== Arigato Autoresponder and Newsletter ===
Contributors: prasunsen
Tags: autoresponder, auto responder, mailing list, newsletter, wpmu, contact form
Requires at least: 3.3
Tested up to: 4.1
Stable tag: trunk

This plugin allows scheduling of automated autoresponder messages, instant newsletters, and managing a mailing list.

== Description ==

[PRO](http://calendarscripts.info/bft-pro/ "Go Pro") |
[Email Support](http://calendarscripts.info/contact.html "Support for PRO queries or about the free plugin if you don't have a Wordpress acccount") 


This plugin allows scheduling of automated autoresponder messages and newsletters, and managing a mailing list. You can add/edit/delete and import/export members. There is also a registration form which can be placed in any website or blog. You can schedule unlimited number of email messages. Messages can be sent on defined number of days after user registration, or on a fixed date.

From version 1.5 you can also send fixed date messages.

From version 2.0 you can send immediate newsletters.

From version 2.2 you can add attachments to your autoresponder emails

**Built-in integration with [Contact Form 7](http://wordpress.org/plugins/contact-form-7/ "Contact Form 7")**
**Built-in integration with [Jetpack Contact Form](http://wordpress.org/plugins/jetpack/ "Jetpack")**

== Installation ==

1. Unzip the contents and upload the entire `bft-autoresponder` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the sender address and other options from the main page accessible from the newly appeared "Arigato Light" menu.
4. Manage your email messages and mailing list.
5. In order to send automated sequential emails every day your blog should be visited at least once daily. If this is not happening, please set up a cron job to visit it using the command shown in your Arigato Light Settings page.

== Changelog ==

= Changes in 2.2.6 =
1. Now keeps track of all the previous newsletters and lets you edit and re-send them
2. Added configurable field names for the Contact Form 7 Integration
3. Added option to use real cron job. This will help you to define what time of the day to send your emails by scheduling the cron job for that time.
4. Changed plugin name to Arigato
5. One more attempt to avoid the odd duplicate emails problem that some users experience
6. Added optional redirect URL after email confirmation

= Changes in 2.2 =
1. Added "Mass delete" option in the mailing list
2. The "{{name}}" mask can now be used also in the double optin email
3. Option to automatically subscribe users who register to the blog. Note that this happens when they first login to avoid bot subscriptions.
4. Built-in integration with Contact Form 7 lets you signup users when they fill your contact form
5. Added raw email log of all emails sent. This will help you know what emails have been sent on each day
6. Added option to automatically cleanup the raw email log after given number of days
7. Added built-in integration with Jetpack contact form
8. Improved the export format and made it download a file
9. Now you can select if you want to send HTML or text/plain emails
10. We have added attachments for your autoresponder emails

= Changes in 2.1 =
1. Added user's name and registration date in unsubscribe notification emails
2. Removed several deprecated usages of wpdb::escape()
3. Added basic validation for empty email on subscribe
4. Double opt-in message is now configurable
5. Created a help page (moved the manual out of the options page)
6. Added alerts when user unsubscribes or confirms their email address
7. Fixed for compatibility with WordPress 3.8
8. Added pagination on the mailing list page
9. You can now configure subscribe and unsubscribe notification messages
10. Fixed missing "unsubscribe" link in instant newsletters

= Changes in 2.0 =
1. Changed the cron job logic in attempt to avoid a multiple emails issue that some people complain about
2. Improved the cron job logic further to avoid simultaneous runnings of the same
3. When the sender's detail are left empty will use the default sender from your Wordpress Settings page
4. Many strings were missing in the .pot file, fixed this.
5. From version 2.0 you can send immediate newsletters. Do it with caution.
6. Other code fixes and code improvements

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

= What to do if it doesn't send emails? = 

Please install a plugin like WP Mail SMTP or Easy WP SMTP and try to send a test email. If the test email isn't received, it means the problem is not in the autoresponder and you should talk to your hosting support.

= Are there any limitsto how many emails can be sent? = 

The autoresponder itself does not impose any limits, but your hosting company probably does. If you plan to have large mailing list, you will need the pro version because it lets you fine-tune the number of emails sent to comply with your hosting company limitations.

== Screenshots ==

1. Main settings page. Get the signup form code, configure double opt-in, and more.
2. Manage your mailing list, add/edit/delete subscribers
3. Import and export contacts to/from CSV file
4. Create a new autoresponder message
5. Send instant newsletter to all active subscribers