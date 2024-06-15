=== Inactive User Deleter ===
Contributors: shra
Donate link: https://pay.cryptocloud.plus/pos/Oc9ieI6Eb5HWPptn
Tags: user management, inactive user, delete user, user deleter, user, managment, users managment, delete, multy removal, pack deletion, user cleaner
Requires at least: 3.1.0
Tested up to: 6.5
Stable tag: 1.65
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

If you wanna clean up a lot of fake or inactive user's registrations (usually made by spammers) by one operation - this tool will help you to do it.

== Description ==

When your project lives long time, site will get a lot of fake user's registrations (usually made by spammers).
This tool will help you to clean this mess up. It can filter, select and delete packs of users.

See more information on https://shra.ru/hobbies/plugins/wordpress-inactive-user-deleter/ - plugin home page.

== Installation ==

To install this plugin:

1. Download plugin
2. Extract and copy plugins files to /wp-content/plugins/inactive-user-deleter directory
3. Activate it (enter to /wp_admin, then choose plugins page, press activate plugin)
4. Go to /wp-admin/users.php?page=inactive-user-deleter.php and follow instructions there.
5. Enjoy, I hope :)

== Screenshots ==

1. Remember, my plugin will never delete user No 1 or users having role 'administrator', and will do deleting operations only after message like
that - &lt;last warning&gt;.
2. Filtering page
3. Trial users deleter tool
4. Miscellaneous page

== Frequently Asked Questions ==

Please feel free to ask here: https://wordpress.org/support/plugin/inactive-user-deleter/

== Changelog ==

= 1.65 = Fix for trial user feature (reported by @yuvrajsisodia).
Also I added flag: "user has name" into filter (requested by @mypersonalwebmaster).
= 1.64 = New search condition has been added - pending users (Requested by @tradenet).
= 1.63 = Compatibilty with plugin Disable User Login was added, and some cosmetic changes (thanks to @wbenterprises for ideas).
= 1.62 = Minor changes and fixes, testing with next core version of WP 6.3, and update discussed with @timmoser: now PHP max_input_vars limitation is not an issue.
= 1.61 = Continue security fixing started at 1.59 (thanks FearZzZz).
= 1.60 = Fix for PHP 8.0 Warning: Undefined array key "confirmPeriod" (requested by Hellnik).
= 1.59 = Updates to protect all form against Cross-Site Request Forgery (CSRF) (requested by Graham aka grl570810).
= 1.58 = No actual changes, just testing with next core version of WP 6.1.1
= 1.57 = New filter: Woocommerce anonymous order filter (visible if Woocommerce is active) (requested by Luboslives).
New action: publish posts of selected users (requested by Fran).
= 1.56 = Fixed bug in the disabled-users feature (reported by Fran).
= 1.55 = New filter by user name (login) (requested by Fran).
= 1.54 = New action: not to delete users, but change to draft status all their posts (requested by Fran).
= 1.53 = Integration with two following plugins 'user-login-history' and 'when-last-login' to receive last known login date.
= 1.52 = The User Level filter has been removed as deprecated. The User list can be exported into CSV file (requested by Fran).
= 1.51 = Optional ability to disable an account instead of deletion (requested by evillizard). The disabled user cannot log-in and has the blocked status. The user also can be unblocked back.
Filter by UserRole was added (feature was requested by sonfisher).
= 1.50 = Trial user deleter feature has been presented (requested by smallguy).
= 1.45 = User email is shown in the list (requested by Arthur Brogard).
= 1.44 = New filter by period after last login.
= 1.43 = Tested with new core version of Wordpress 5.0.
= 1.42 = Flags are rebuilded. Now you have new option - "no matter", if don't want use a condition. I fixed some code in template (for translation purposes).
= 1.41 = Hotfix for 'known date log-in' flag. Maybe it is not final :)
= 1.4 = There was added Classipress support by user (aka Manish) request.
= 1.31 = New MISC option - Email before delete. Thanks to KadGab for idea.
= 1.3 = Added some features and few filters. Thanks to Greg Ross for new ideas.
= 1.2 = Plugin resurrection. :) I have done some requested features, fixed bugs, and fully rewrote code.
= 1.1 = Plugin renovation. Bug fixing, some new options.
= 1.0 = It was an initial version. Everything.

== Upgrade Notice ==

No special notes is here for upgrade. Install and enjoy.
