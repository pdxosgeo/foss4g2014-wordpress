=== Register Plus Redux ===
Contributors: radiok, skullbit
Donate link: http://radiok.info/donate/
Tags: registration, register, plus, redux, password, invitation, code, email, verification, disclaimer, license, agreement, privacy, policy, logo, moderation, user
Requires at least: 3.3
Tested up to: 3.5
Stable tag: 3.9.10

Enhances the user registration process with complete customization and additional administration options.

== Description ==

Register Plus Redux enables the user registration (or signup) process to be customized in many ways, big and small. Is there another field you want users to complete when registering? Do you want to change the message your users receive after they register? Do you want users to have to verify their email address is legitimate? Do you want to queue up new users to be approved or denied by an administrator? Register Plus Redux can do all that and more.

Enhancements to registration include:

* Replace WordPress logo with your own logo on registration and login page

* Verify new users Email Address following registration

* Keep new users in a queue to be verified by an Administrator

* Alter redirect of users following registration or signup

* Automatically logon users following registration or signup (functionality still in development)

* Option to use Email Address as Username

* Optionally require users to enter Email Address twice for accuracy

* Show and potentially require any profile fields on registration or signup form

* Allow users to specify their password (with optional password strength meter)

* Invitation code system (with dashboard widget to track invites)

* Add your own disclaimer, license agreement, or privacy policy to registration or signup page

* Add additional custom fields (textbox, select, checkboxes, radio buttons, textarea) to registration, signup, or profile __(textboxes can be validated against regex)__

* Customize message to new users

* Customize message to Administrators when users are registered or signup

* Specify CSS to be applied to registration or login page

Register Plus Redux was forked from Register Plus, developed by skullbit, after that plugin was abandoned in 2008. Register Plus Redux resolves many known bugs and added compatibility with WordPress 3.0+.

Available in the following translations:

* zh_CN Chinese
* nl_NL Dutch
* de_DE German
* fr_FR French
* ir_FA Persian
* it_IT Italian
* pl_PL Polish
* pt_BR Portuguese
* pt_BR Brazilian Portuguese
* ro_RO Romanian
* ru_RU Russian
* es_ES Spanish
* es_AR Argentinean Spanish
* tr_TR Turkish

== Installation ==

1. Upload the 'register-plus-redux' directory to the '/wp-content/plugins/' directory
2. When installed on WordPress with Multisite, you must Network Activate 'Register Plus Redux' from the 'Plugins' menu in Network Admin to have access to all functionality (refer to the FAQ for more information)
2. -OR- Simply Activate 'Register Plus Redux' from the site's 'Plugins' menu
3. Configure Register Plus Redux from the 'Register Plus Redux' page under Settings on individual sites

== Frequently Asked Questions ==

= How is Register Plus Redux related to Register Plus? =
Register Plus was abandoned by skullbit sometime after September, 2008 following the release of Register Plus 3.5.1. As of September, 2009 skullbit's website was undergoing maintenance. Several bugs had been reported to the Register Plus plugin forum since that time, to resolve these bugs and continue development radiok forked the project.

= What's New? or What's Coming Soon? =
Visit <http://radiok.info/blog/category/register-plus-redux/> for all information Register Plus Redux related

= Register, Signup, what's the difference? =
Historically, users registered for WordPress sites.  WordPress MU (**M**ulti**u**ser) introduced the signup process which is conceptually similar to registration, but also very different, especially from a coding perspective.  WordPress MU has since been merged into WordPress as the Multisite (a/k/a WordPress MS) feature.  The actions, filters and, overall request lifecycle are dramatically different, as is the presentation.  The registration and signup pages are different in every way, even though they have similar intentions.  As such, developers must make the distinction between registration and signup.

= Why should I Network Activate vs activating on individual blogs? =
This question is specific to WordPress with Networks, or WordPress Multisite, whichever terminology tickles your fancy. If you don't know what either means, you most likely don't need to concern yourself either way. After much trial and error, I learned that due to an odd executive decision in WordPress core, site plugins are not loaded during user or blog activation (see WordPress Trac [#18278](http://core.trac.wordpress.org/ticket/18278) or [#23197](http://core.trac.wordpress.org/ticket/23197)), however, network plugins, that is plugins "Network Activated" are loaded. This behavior prevents Register Plus Redux from restoring information stored after a user signs up when only activated at the site level. There's nothing forcing you to Network Activate, however, features involving adding additional fields to the signup page will not function properly. This mandate does create some odd situations in which you may have one site, or a subset sites, in which you wish to utilize Register Plus Redux, however all your sites will have access to its functionality. This is a decision Network Administrators must make.

= Didn't Register Plus have a CAPTCHA feature? How do I add a CAPTCHA to the registration form? =
Register Plus offered two different CAPTCHA methods, a simple random CAPTCHA and reCAPTCHA. The simple one randomly created a 5-character sequence on a background image with two random lines drawn across the image, this CAPTCHA would be very easy for any OCR program to decipher as the characters were not modified in anyway and contrast was high. reCAPTCHA is a great idea, but there is another plugin, [WP-reCAPTCHA](http://wordpress.org/extend/plugins/wp-recaptcha/) endorsed by the reCAPTCHA developers that can be used to add reCAPTCHA to the registration page. I endorse the use of that plugin for that purpose.

= Didn't Register Plus have a feature to allow duplicate e-mail addresses? =
Register Plus did have a feature that allowed multiple users to register with the same e-mail address. I'm not sure when that stopped working for Register Plus, but I can assure you, that method does not work in WordPress 3.0 and will not work in the foreseeable future. Register Plus' method was pretty simple, if the email_exists error was thrown, 'unthrow', or more accurately, unset it. That is still possible, however, when WordPress actually creates the user, it chokes up and unpleasant things happen, in my experience. I'll leave this feature to brighter minds than my own to implement.

= I do not want users to go to the Dashboard after logging in. How do I redirect users after they login? =
This isn't quite a registration issue, but I can see how the line blurs since A) Redux does have configuration options for the Login screen, and B) Redux has a configuration for redirect after registration. I briefly considering programming this feature, but [Peter's Login Redirect](http://wordpress.org/extend/plugins/peters-login-redirect/) does everything I could do and so much more. I endorse the use of that plugin for this purpose.

= Why does Register Plus Redux require WordPress 3.3+ =
Prior to WordPress 3.2, WordPress required PHP 4, version 3.2 bumped up the requirement to PHP 5 (specifically PHP 5.2).  Register Plus Redux has been built specifically against PHP 5, functions and features may not function properly in PHP 4 or older.  WordPress 3.3 included the full jQuery UI library, previously Register Plus Redux had to include its own copy for the Datepicker widget.

= Can you add a feature to change the width of the Registration Form? / How you change the width of the Registration Form? =
You can use the Custom Register CSS (found in Register Plus Redux's settings) to specify the width of the Registration form via CSS.  Specifically the code follows:
`#login { width: 500px; }`
This is a neat feature that could be expressed explicitly in a Register Plus Redux setting, but considering the simplicity of the solution I have determined that to be unnecessary.

= Things to Keep in Mind =
Really more for me than you, but who's nitpicking.

HTML attributes should go in the following order name -> id -> class

== Screenshots ==

1. A Modified Registration Page
2. Register Plus Settings
3. Invitation Tracking Dashboard Widget
4. Unverified User Management

== Changelog ==

= 3.9.10 =
May 14, 2013 by radiok

* Fixed bug, under Wordpress for Networks, super_admin users could not login
* Added pl_PL translation

= 3.9.9 =
May 8, 2013 by radiok

* Fixed bug, upon verification usermeta was purging when changing user role from unverified to default role
* Fixed regression from 3.9, show Datepicker
* Fixed bug, when both email and admin verification were enabled, admin verification was not obeyed after completing email verification
* Delete Wordpress option register_plus_redux_last_activated on deactivation or uninstall
* New Wordpress option, register_plus_redux_version to assist in debugging
* Moved scripts to footer of page to improve rendering

= 3.9.8 =
March 3, 2013 by radiok

* Regression, PHP 5.3+ required for static keywords, reverting to static methods
* Added sanity checks to allow default behavior if Redux activation fails
* New Wordpress option, register_plus_redux_last_activated to assist in debugging

= 3.9.7 =
March 2, 2013 by radiok

* Regression, PHP 5.3+ required for class constants, reverting to global constant

= 3.9.6 =
March 2, 2013 by radiok

* Added ability to disable user email verification on WordPress Multisite
* Significant re-factor of code base, specifically involving explicit conversions
* Improved Unverified Users page with consistent behavior and added functionality
* Added new 'rpr_unverified' (Unverified) user role
* Added new 'rpr_can_login' capability
* Converted 'unverified_*' users to Unverified user role
* Added activation/deactivation/uninstall functions, specifically for new role purposes
* Removed filter_login_message hack, use action to determine behavior following registration
* Use Default CSS now adds ID's to username and e-mail's label and paragraph element on registration form

= 3.9.5 =
February 19, 2013 by radiok

* Added user_id parameter to rpr_signup_complete action
* Fixed bug, could not delete users from unverified users page
* Fixed CSS on additional checkbox and radio fields on signup page
* Fixed bug with signup not validating due to bad $pagenow check
* Fixed bug, %user_login% was not replaced properly in messages following email verification

= 3.9.4 =
February 15, 2013 by radiok

* Created action, 'rpr_signup_complete' which occurs after any verification in place and after user data is committed but prior to messages being sent out
* Misc. bug fixes, nothing significant
* Improved CSS on various elements

= 3.9.3 =
February 7, 2013 by radiok

* Added new feature dynamic keywords for custom messages, %=keyword% will search user_meta for keyword and replace
* Fixed bug with custom admin messages

= 3.9.2 =
February 6, 2013 by radiok

* Improved initial 'meta_key' definition
* Fixed bug with Network Activation warning which prevented any activation on Wordpress Multisite
* Fixed some broken jQuery
* Fixed regression from v3.7.3 introduced in v3.9 in the way that additional select, checkbox, and radio field values were stored

= 3.9.1 =
February 5, 2013 by radiok

* Removed hack to filter random passwords in messages
* Fixed malformed labels for additional fields on signup page
* Fixed bug with additional checkbox fields on registration page
* Fixed bug with saving additional checkbox fields on profile page
* Fixed bug with clearing additional fields on profile page
* Updated javascript to be more compatible with jQuery 1.9

= 3.9 =
January 21, 2012 by radiok

* Converted custom_fields to redux_usermeta
* Can now specify database key for meta fields
* Remove hack to workaround non-english custom fields
* Added option for unique invitation codes
* Improved CSS of Checkbox and Radio fields on standard WordPress registration page
* Converted jQuery for Email Address as Username option to JavaScript DOM commands
* Added help feature (in progress) for meta fields
* Changed method of sanitizing user data to preserve percent signs
* Completely rewrote all form validation
* Apply KSES to HTML enabled form fields
* Added CSSTidy to validate CSS
* Broke out code across several php files

= 3.7.3 =
March 29, 2011 by radiok

* Regression, WordPress 3.1 does not resolve wp_enqueue_script problem, reverted code to 3.7.1

= 3.7.2 =
March 23, 2011 by radiok

* Added new custom field type, Static Text
* Added Registration Redirect option
* Added Email Address as Username option
* Text fields may now be validated against a regular expression if entered
* Additional fields are now visible to admin, regardless of visibility to other users
* Change registration error checking from action to filter for better compatibility with PHP versions before PHP5
* Fixed bug with asterisks and required fields
* Fixed bug with l18n only loading for admins
* Fixed bug with user set passwords still nagging, as reported by Jim
* Found and repaired additional untranslated strings
* Added de_DE translation

= 3.7.1 =
March 16, 2011 by radiok

* Moved load_plugin_textdomain from constructor to initialization

= 3.7.0 =
March 16, 2011 by radiok

* Major change, wp_new_user_notification is only created as necessary
* Added fr_FR, ro_RO, ru_RU, and tr_TR translations
* Fixed bug with auto-complete not filling in user_login and user_email, as reported by webakimbo
* Fixed invitation code tracking dashboard widget, as reported by Galyn

= 3.6.22 =
November 5, 2010 by radiok

* Fixed bug in custom checkbox fields, as reported by notquitewild 
* Added hack to workaround allow_url_fopen, for problem reported by shrikantjoshi
* Added hack to workaround non-english custom fields, for problem reported by Vrefr

= 3.6.21 =
November 3, 2010 by radiok

* Added ir_FA and it_IT translations
* Fixed multiline email or admin responses breaking jQuery
* Removed old responses from before jQuery that were now hidden
* Changed logo title to not include empty blog description
* Added %verification_link% keyword
* Fixed change to logo title to not include empty blog description
* Localized a few more strings
* Fixed bug in custom CSS that did not allow quotes as reported by webakimbo
* Fixed major bug that could automatically delete users other then unverified users
* Fixed bug with apostrophes in custom field name or options

= 3.6.20 =
October 21, 2010 by radiok

* Fixed jQuery datepicker for date custom fields
* Fixed jQuery on Settings Page only working with Firefox
* Added options to specify whether user must agree to Disclaimer, License, or Privacy Policy
* Added option to turn off WordPress standard CSS on registration page, as requested by jlsniu <http://wordpress.org/support/topic/plugin-register-plus-redux-css-and-tabindex>
* Added option to change or disable tabindex's on registration page, as requested by jlsniu <http://wordpress.org/support/topic/plugin-register-plus-redux-css-and-tabindex>
 
= 3.6.19 =
October 13, 2010 by radiok

* Reorganized Settings Page to better reflect order of fields on Registration Page
* Started adding jQuery to summarize when and what messages are going out
* Rewrote jQuery on Settings Page
* Added id tags to every field on Registration Page for better CSS use
* Added custom Email Verification and Admin Verification messages, as requested by Eric Bostrom <http://radiok.info/blog/administration-redux/>

= 3.6.18 =
October 12, 2010 by radiok

* Added code to move custom fields around, as requested by pantone204 for Pie Register <http://wordpress.org/support/topic/plugin-pie-register-adding-new-fields-different-order>
* Fixed bug in User Profile with invitation_code but no custom fields, as reported by ak <http://radiok.info/blog/administration-redux/>

= 3.6.17 =
October 11, 2010 by radiok

* Added buttons back to Unverified Users page
* Added ability to Edit or Delete users individually from Unverified Users page
* Added option to enforce minimum password length
* Added option to enforce case sensitive invitation codes
* Added new custom field type, URL Field, this field is sanitized as a URL, as requested by Shikant Joshi <http://radiok.info/blog/administration-redux/>
* Added invitation_code to User Profile page, as requested by janman for Pie Register <http://wordpress.org/support/topic/plugin-pie-register-invitation-code-in-user-profile-page>
* Fixed asterisks showing up on all predefined fields, not just required ones, as reported by pixelprophet <http://wordpress.org/support/topic/plugin-register-plus-redux-email-conflicts-with-another-plugin>
* Fixed loophole in Lost Password that would send an unverified user their temporary user login and allow them access using that login, as reported by AzzePis <http://wordpress.org/support/topic/plugin-register-plus-redux-user-can-register-without-confirmation-of-his-account>

= 3.6.16 =
October 9, 2010 by radiok

* Check subject for keywords, as mentioned by Shikant Joshi <http://wordpress.org/support/topic/plugin-register-plus-redux-call_user_func_array-error>
* Changed the order of usernames in Unverified username page

= 3.6.15 =
October 8, 2010 by radiok

* Fixed a little bug in custom admin messages having no from name or from email address.

= 3.6.14 =
October 8, 2010 by radiok

* Fixed issue with %user_password%, as reported by erbuc, and the.gamer <http://wordpress.org/support/topic/plugin-register-plus-redux-no-text-in-the-user-notification-email>
* Made verification message customizable, as suggested by Shikant Joshi <http://radiok.info/blog/administration-redux/>
* Added several options regarding when and when not to send messages, as discussed with Shikant Joshi <http://radiok.info/blog/administration-redux/>
* Added option to add asterisks to required fields, as suggested by pixelprophet <http://wordpress.org/support/topic/plugin-register-plus-redux-email-conflicts-with-another-plugin>
* Fixed issues with slashes in fields due to the way data is stored in MySQL, added stripslashes to applicable text fields, as reported by pixelprophet <http://wordpress.org/support/topic/plugin-register-plus-redux-email-conflicts-with-another-plugin>
* Added option to double check email addresses, as requested by MacItaly <http://wordpress.org/support/topic/plugin-register-plus-redux-double-check-email-address>
* Added %stored_user_login% keyword for messages, as discussed with richardmtl <http://wordpress.org/support/topic/plugin-register-plus-redux-call_user_func_array-error>

= 3.6.13 =
October 6, 2010 by radiok

* Fixed issue with custom user messages going out blank, as reported by kspec1212 <http://wordpress.org/support/topic/plugin-register-plus-redux-no-text-in-the-user-notification-email>
* Fixed issue with admin messages not going out, as reported by saury316 <http://wordpress.org/support/topic/plugin-register-plus-redux-admin-verification-issues>

= 3.6.12 =
October 5, 2010 by radiok

* Rewrote User Administration
* Fixed DeleteExpiredUsers
* Changed date/time format of email_verification_sent
* Added check to not allow users to register with a username already in queue to be authorized
* Added jQuery to disable invalid settings
* Added some variable checks to prevent undefined index warnings, I added a ton, but I'd need even more to eliminate all notices, as reported by overclockwork <http://wordpress.org/support/topic/plugin-register-plus-redux-settings-cleared-when-saving>
* Fixed bug with custom fields and CSS, was not appending to list of fields for CSS, as reported by saury316 <http://wordpress.org/support/topic/plugin-register-plus-redux-logo-and-other-issues>

= 3.6.11 =
September 30, 2010 by radiok

* Found errant show_about_field
* Fixed bug with replace_keywords as reported by Angelo Dicerni <http://radiok.info/blog/the-ethos-of-register-plus-redux/>
* Started working on adding localization back in

= 3.6.10 =
September 29, 2010 by radiok

* Reduced CSS written to wp-login header
* Rewrote all CSS written to wp-login header, completely theme-able now
* Fixed bug with checkbox type not be available for custom fields
* Fixed bug with select type custom fields, was using already in use variable name, as reported by shrikantjoshi <http://wordpress.org/support/topic/plugin-register-plus-redux-new-fields-problem>
* Fixed wp_delete_user as reported by saury316 <http://wordpress.org/support/topic/plugin-register-plus-redux-error-at-user-deletion>

= 3.6.9 =
September 28, 2010 by radiok

* Rewrote nearly every echo statement to be enclosed in quotations
* Rewrote function to purge unverified users exceeding grace period
* Rewrote code for password strength indicator, resolves issue reported by iq9 on Register Plus forum <http://wordpress.org/support/topic/plugin-register-plus-couple-bugs>
* Changed default user and admin messages to match WordPress defaults
* Renamed some of the replacement keys to match their true nature or name
* Renamed several variables
* Changed wp_update_user to $wpdb->query for updating user_login
* Removed function to create random string, use wp_generate_password instead
* Reorganized wp_new_user_notification more logically

= 3.6.8 =
September 25, 2010 by radiok

* Fixed custom logo feature not persisting as reported by saury316, and added feature to supply URL to custom logo 
<http://wordpress.org/support/topic/plugin-register-plus-redux-logo-and-other-issues> and <http://wordpress.org/support/topic/plugin-register-plus-custom-logo-help>
* Disabled Allow Duplicate Email Addresses, I'll have to figure out how to work that one out

= 3.6.7 =
September 24, 2010 by radiok

* Fixed custom logo feature
* Update registration page HTML to better match WordPress 3.0.1
* Changed add/remove buttons on settings page to not be links, no more jumping around the page
* Invitation codes are no longer stored in lowercase, making way for option to enforce case sensitivity

= 3.6.6 =
September 24, 2010 by radiok

* Introduce hooks for WPMU
* Cleaned up wp_new_user_notification
* Fixed custom fields, tested, tested, and retested text fields, more testing due for other field types

= 3.6.5 =
September 22, 2010 by radiok

* Added preview registration page buttons
* Fixed bug with saving custom fields from profile
* Fixed bug with saving settings as reported by mrpatulski, array check was missing <http://wordpress.org/support/topic/plugin-register-plus-redux-getting-fatal-error-when-activating>

= 3.6.4 =
September 21, 2010 by radiok

* Fixed dumb bug with get_user_meta returning arrays

= 3.6.3 =
September 21, 2010 by radiok

* Completed renaming of settings (hopefully)
* More redesign of settings page
* Rewrote all jQuery on settings page
* Fixed bug found by me.prosenjeet, this was due to some changes made to the jQuery previously used <http://wordpress.org/support/topic/plugin-register-plus-redux-new-fields-problem>
* Fixed bug found by craigbic, this was due to incomplete renaming of settings <http://wordpress.org/support/topic/plugin-register-plus-redux-form-cannot-accept-license-or-privacy-policy>

= 3.6.2 =
September 16, 2010 by radiok

* Fixed bug found by seanchk, shrikantjoshi, and ljmac, this was due to incomplete renaming of settings <http://wordpress.org/support/topic/plugin-register-plus-redux-settings-cleared-when-saving>
* Fixed jQuery datePicker as specified by DanoNH <http://wordpress.org/support/topic/register-plus-is-adding-s-to-all-quote-marks-in-registration-email>
* Redesigned settings page

= 3.6.1 =
September 13, 2010 by radiok

* Fixed two bugs found by Gene53 and markwadds, both typos <http://wordpress.org/support/topic/plugin-register-plus-redux-fatal-error>
* More renaming of settings

= 3.6 =
September 13, 2010 by radiok

* Cleaned up all code, spacing, tabs, formatting, etc.
* Updated stylesheet to match WordPress 3.0.1
* Removed Simple CAPTCHA and reCAPTCHA, the Simple CAPTCHA was easy to break two years ago, now it's a joke, BlaenkDenum has a very active reCAPTCHA plugin that can be used for registration, among other things <http://wordpress.org/extend/plugins/wp-recaptcha/>
* Rewrote UploadLogo as specified by nschmede <http://wordpress.org/support/topic/plugin-register-plus-register-plus-custom-logo-problems>
* Fixed SaveProfile as specified by bitkahuna <http://wordpress.org/support/topic/plugin-register-plus-does-registration-plus-work>
* Fixed Invitation Code Tracking dashboard widget as specified by robert.lang <http://wordpress.org/support/topic/plugin-register-plus-error-message-on-dashboard-panel-display>
* Fixed bug in Profile regarding website, user_url was being stored in wp_usermeta, when it should have been in wp_users, StrangeAttractor's code was most beneficial, but I made several other improvements along the way <http://wordpress.org/support/topic/plugin-register-plus-cant-update-website-in-user-profile>
* Added Settings action link to Plugins page
* Reduced use of $wpdb variable in favor of WordPress' helper functions
* Started renaming settings

= 3.5.1 =
July 29, 2008 by skullbit

* Added Logo link to login page

= 3.5 =
July 29, 2008 by skullbit

* Changed Logo to link to site home page instead of wordpress.org and set the Logo title to "blogname - blogdescription"
* Added Date Field ability for User Defined Fields - calendar pop-up on click with customization abilities

= 3.4.1 =
July 28, 2008 by skullbit

* Fixed admin verification error

= 3.4 =
July 25, 2008 by skullbit

* Fixed verification email sending errors
* Fixed Custom Fields Extra Options duplications
* Added Custom CSS option for login and register pages

= 3.3 =
July 23, 2008 by skullbit

* Updated conflict warning error to only appear on the RegPlus options page only.

= 3.2 =
July 22, 2008 by skullbit

* Fixed Custom Field Checkbox saving issue
* Additional field types available for Custom Fields.
* Password Meter is now optional and text is editable within options page

= 3.1 =
July 8, 2008 by skullbit

* Added Logo Removal Option
* Updated Email Validation text after registering
* Added User Sub-Panel for resending validation emails and automatic admin validation
* Added User Moderation Ability - new registrations must be approved by admin before becoming active.
* Fixed bad version control code

= 3.0.2 =
June 23, 2008 by skullbit

* Updated Email notifications to use a filter to replace the From Name and Email address

= 3.0.1 =
June 19, 2008 by skullbit

* Added more localization files
* Added documentation for auto-complete queries
* Fixed Admin notification email to now actually really go to the administrator

= 3.0 =
June 18, 2008 by skullbit

* Added localization to password strength text
* Added stripslashes to missing areas
* Added Login Redirect option for registration email url
* Added Ability to populate registration fields using URL GET statements
* Added Simple CAPTCHA Session check and warning if not enabled
* Added ability to email all user data in notification emails

= 2.9 =
June 10, 2008 by skullbit

* Fixed foreach error for custom invite codes
* Custom logos can now be any size
* Login fields are now hidden after registration if email verification is enabled.

= 2.8 =
June 9, 2008 by skullbit

* Fixed Fatal Error on Options Page

= 2.7 =
June 8, 2008 by skullbit

* Added full customization option to User Registration Email and Admin Email.
* Added ability to disable Admin notification email.
* Added style feature for required fields
* Added Custom Logo upload for replacing WP Logo on register & login pages

= 2.6 =
May 15, 2008 by skullbit

* Fixed error on ranpass function.

= 2.5 =
May 14, 2008 by skullbit

* Fixed registration password email to work when user set password is disabled

= 2.4 =
May 13, 2008 by skullbit

* Fixed localization issue
* Added License Agreement & Privacy Policy plus user defined titles and agree text for these and the Disclaimer
* Fixed Javascript error in IE

= 2.3 =
May 12, 2008 by skullbit

* Added reCAPTCHA support
* Fixed PHP short-code issue
* Added option to not require Invite Code but still show it on registration page
* Added ability to customize the registration email's From address, Subject and add your own message to the email body.

= 2.2 =
April 27, 2008 by skullbit

* Fixed About Us Slashes from showing with apostrophes
* Modified the Captcha code to hopefully fix some compatibility issues

= 2.1 =
April 26, 2008 by skullbit

* Fixed Admin Registration Password issue
* Added Dashboard Widget for showing invitation code tracking
* Added Email Verification for ensuring legitimate addresses are registered.
* Unvalidated registrations are unable to login and are deleted after a set grace period

= 2.0 =
April 20, 2008 by skullbit

* Added Profile Fields
* Added Multiple Invitation Codes
* Added Custom User Defined Fields with Profile integration
* Added ability to ignore duplicate email registrations

= 1.2 =
April 13, 2008 by skullbit

* Altered Options saving and retrievals for less database interactions
* Added Disclaimer Feature
* Allowed register fields to retain values on submission if there is an error.

= 1.1 =
April 10 2008 by skullbit

* Fixed Invitation Code from displaying when disabled.
* Added Captcha Feature

== Upgrade Notice ==

= 3.6.0 =
First stable release by radiok with bugfixes to issues found in 3.5.1

= 3.7.0 =
Major change to avoid conflicts

= 3.7.2 =
New features, can use email address as username and validate text fields with regex

= 3.9.0 =
Notice: Custom CSS on Checkbox and Radio fields may need to be reviewed

= 3.9.3 =
New feature, meta keywords for custom messages, %=keyword% will search user_meta for keyword and replace

= 3.9.5 =
WordPress Multisite users please update, signup pages were not validating, any information entered was accepted, also Email as Username was not functioning, same root cause

= 3.9.8 =
Added new role, Unverified, and new capability, rpr_can_login, unverified users now take advantage of these, change may be jarring for long time users