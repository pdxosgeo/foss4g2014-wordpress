=== Ninja Forms ===
Contributors: kstover, jameslaws, daveshine, mordauk, bftrick, helgatheviking
Tags: form, forms, contact form, custom form, form builder, form creator, form manager, form creation, contact forms, custom forms, forms builder, forms creator, forms manager, forms creation, form administration,
Requires at least: 3.6
Tested up to: 3.9
Stable tag: 2.6.5
License: GPLv2 or later

Forms created with a simple drag and drop interface. Contact forms, Email collection forms, or any other form you want on your WordPress site.

== Description ==
Ninja Forms is the ultimate FREE form creation solution for WordPress. Build WordPress Forms within minutes using Ninja Forms powerful forms creation page. It allows you to easily and quickly design complex forms through a drag and drop interface and absolutely no code. But for you developers it has a ton of hooks and filters so you can do absolutely anything with this powerful form building framework.

The Ninja Forms framework is now on [GitHub](https://github.com/wpninjas/ninja-forms)! If you're a developer and want to help make Ninja Forms better, check out our [GitHub repo](https://github.com/wpninjas/ninja-forms). It's also a great resource for developers who are interested in creating premium extensions for our online store.

This review of Ninja Forms was done by Pippin Williamson:

[youtube http://www.youtube.com/watch?v=hVfPmKzqYpk]

Here are just a few of the things you will find in Ninja Forms:

* Custom input masks allow you to restrict user input in your forms for things like phone numbers, currency, and dates.
* Manage, Edit, and Export form user submissions.
* Save fields as favorites and re-use them in multiple forms.
* Export and Import forms and favorite fields.
* Set required fields.
* Add a datepicker to text fields.
* Email form results to any number of pre-determined email addresses, as well as specific messages to the user filling out the form.
* Customize form emails with the WordPress editor.
* Anti-Spam field.
* Extremely developer friendly.
* Submit your form by reloading the page or asynchronously with AJAX.
* Please note that if you are using a version of PHP lower than 5.3, you may experience some problems using AJAX Submissions. These can be minimized by using simple success/error messages without any quotes or special characters.

= Demo Site =
Please feel free to visit our [demo site](http://demo.wpninjas.com/ninja-forms/) where you can test the features of Ninja Forms and all of our extensions. http://demo.wpninjas.com/ninja-forms/

= In addition to these features, extensions are available at our website: =

* [PayPal Express](http://ninjaforms.com/downloads/paypal-express/) - Accept payments using PayPal Express and Ninja Forms!
* [Front-End Editor](http://ninjaforms.com/downloads/front-end-editor/) - Give your users the ability to create, edit, or delete posts, pages, or any custom post type and allow your users to edit their Ninja Forms submissions all from the front-end. Also included is front-end profile editing, custom registration forms, login and password resetting, all without needing to see the default, WordPress branded login page.
* [File Uploads](http://ninjaforms.com/downloads/file-uploads/) - Allow users to upload files and store those files within a searchable database.
* [Multi-Part Forms](http://ninjaforms.com/downloads/multi-part-forms/) - Break up those long, complex forms into multiple pages.
* [Save User Progress](http://ninjaforms.com/downloads/save-user-progress/) - Let your users save their progress and return later to finish filling out the form.
* [Conditional Logic](http://ninjaforms.com/downloads/conditional-logic/) - Create "smart" forms that show or hide fields based upon user input. Even add a value to a dropdown list when a user selects a specific value from another list.
* [Front-End Posting](http://ninjaforms.com/downloads/front-end-posting/) - Use Ninja Forms to create posts from the front-end. These can be added to any post type, including custom post types, and users can select categories and tags.
* [Layout & Styles](http://ninjaforms.com/downloads/layout-styles/) - Use Ninja Forms to create amazing form layouts and styles right from your WordPress admin.
* [MailChimp](http://ninjaforms.com/downloads/mail-chimp/) - The MailChimp extension allows you to quickly create newsletter signup forms for your MailChimp account using the power and flexibility that Ninja Forms provides.
* [Campaign Monitor](http://ninjaforms.com/downloads/campaign-monitor/) - The Campaign Monitor extension allows you to quickly create newsletter signup forms for your Campaign Monitor account using the power and flexibility that Ninja Forms provides.
* [User Analytics](http://ninjaforms.com/downloads/user-analytics/) - The User Analytics extension will help website owners understand how hot a lead is based on extra data automatically collected about the user.
* [Constant Contact](http://ninjaforms.com/downloads/constant-contact/) - The Constant Contact extension allows you to quickly create newsletter signup forms for your Constant Contact account using the power and flexibility that Ninja Forms provides.
* [Pushover](http://ninjaforms.com/downloads/pushover/) - When email and SMS notifications just do not cut it, send yourself push notifications of form submissions with Pushover. Pushover makes it easy to send real-time notifications to your Android and iOS devices.
* [Freshbooks](http://ninjaforms.com/downloads/freshbooks/) - Automatically insert Ninja Forms data right into Freshbooks when your potential clients submit forms. Go from lead to invoice that much quicker by having the information already in your favorite invoicing application.

We have several other extensions in the works.

If you're a developer and would like to talk about creating some premium extensions for Ninja Forms, send us an email: info@wpninjas.com.

== Screenshots ==

To see up to date screenshots, visit [Ninja Forms.com](http://ninjaforms.com/).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `ninja-forms` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit the 'Forms' menu item in your admin sidebar

Shortcodes have been re-implemented. They are used like so: [ninja_forms_display_form id=3] where 3 is the ID number of the form you want to display.

== Use ==

For help and video tutorials, please visit our website: [Ninja Forms Documentation](http://ninjaforms.com/documentation/intro/)

== Upgrade Notice ==

= 2.6.5 (06/10/2014) =

*Changes:*

* Added a filter for setting which field types are shown the input limiter.
* Changed the header( 'Location....') php function with wp_redirect().

*Bugs:*

* Fixed a bug that could cause the required symbol to be added twice if the label position was set inside.
* Wrapped the $post global in an if check to prevent errors on some WP pages.

== Requested Features ==

If you have any feature requests, please feel free to visit [ninjaforms.com](http://ninjaforms.com) and let us know about it.

== Changelog ==

= 2.6.5 (06/10/2014) =

*Changes:*

* Added a filter for setting which field types are shown the input limiter.
* Changed the header( 'Location....') php function with wp_redirect().

*Bugs:*

* Fixed a bug that could cause the required symbol to be added twice if the label position was set inside.
* Wrapped the $post global in an if check to prevent errors on some WP pages.

= 2.6.4 (05/15/2014) =

*Changes:*

* Modified CSS styling slightly for default contact forms.

= 2.6.3 (05/15/2014) =

*Bugs:*

* Removed CSS padding issues for some themes.
* Fixed a bug with short codes on default values.
* Fixed a PHP Notice related to WordPress version 3.9 and $wpdb->prepare().

*Changes:*

* Added a Finnish Translation (Thanks to ottok).
* Fixed a bug with short codes on default values.

= 2.6.2 (04/22/2014) =

*Changes:*

* Added some CSS to make forms more responsive and cross browser compatible.

= 2.6.1 (04/17/2014) =

*Changes:*

* Modified the call to wp_editor on the Field Settings tab so that it is compatible with WordPress 3.9.
* Added more security to prevent forms from being spammed.
* Added additional items to the system status page.

*Bugs:*

* Fixed a php notice that could occur when using the inside label position.
* Calc fields that use advanced equations should now function properly when referencing other advanced calculation fields.

= 2.6 =

*Changes:*

* The "Processing Message" above the form has been removed in favour of changing the text of the submit button and disabling it.
* This change in processing notification will make it easier for users to see that a submission is being processed and prevent them from clicking the "submit" button again.
* Updated the label of the "Processing" label to make it clearer.
* Added a credit card field that can be enabled by extensions that need to use it.

*Bugs:*

* Fixed a bug introduced in version 2.5.3 which prevented Sessions from starting properly on some server setups.
* Fixed some PHP warnings/notices.
* List items should now properly escape html when displayed.

= 2.5.3 =

*Bugs:*

* Fixed a bug that could prevent AJAX submissions from working properly.

= 2.5.2 =

*Bugs:*

* Fixed various bugs with the default label position. It should now always default to above.
* Fixed a bug that could cause Admin emails to appear to have attachments when they didn't.
* Required fields can now have a value of 0.
* Fixed a bug with list fields and calculations that could cause them not to pre-populate properly.
* Fixed a bug that could cause incomplete submissions to show in the dashboard for those using the Save Progress extension or the Multi-Part Forms extension.

= 2.5.1 =

*Bugs:*

* Fixed a major bug that removed the "Number" field.

= 2.5 =

*Features:*

* Submission Limiting - Allows users to limit the number of submissions a form can receive.
* Character Counts - Added a "Character Count" option to textboxes and textareas.

*Bugs:*

* Fixed a bug that prevented the pagination from working on view submission pages.
* Fixed a bug with the default value filter.
* Fixed a bug that prevented forms from showing when they were on the landing page of another form.
* Fixed a bug that caused required fields to go unprocessed when extensions were deactivated.
* Fixed a bug that caused list fields to pre-populate incorrectly in some instances.
* Removed some console logs that could cause crashes in Internet Explorer.
* Fixed a bug that caused forms to improperly clear when submitted.
* Fixed a bug that prevented empty success messages from being saved.

*Changes:*

* Add some CSS to help prevent theme from breaking left and right labels when they add display:block to the label element.
* All settings are now fetched via a proxy function.
* Added a wrapper for the entire form display (and everything that goes with it).

= 2.4.2 =

*Bugs:*

* Fixed a bug that caused memory issues when a form has a large number of submissions.
* Fixed a bug with the label inside position that generated a “call to member function…” error.
* Fixed a bug that prevented tax totals from working properly with numbers with thousands separators.
* Fixed a bug that prevented list importing from working with newly created list fields.
* Fixed a bug with list fields that prevented list options from being re-ordered immediately after being added to the form.

*Changes:*

* Required fields that use the “inside” label position will now show the required symbol inside the field with the label.
* Changed the default value filter to respect fields that shouldn’t be processed.
* Removed code from the field class function that was specific to Conditional Logic. This code should be housed in the extension rather than the core plugin.

= 2.4.1 =

*Bugs:*

* Fixed a bug that prevented the Inside label position from working properly on form load.
* Fixed an issue that could cause the conditional logic extension to function improperly on load.

= 2.4 =

*Features:*

* A new method for loading Ninja Forms for display has been created to decrease loading times. Users with extremely large forms (100+ fields) should see a marked improvement in loading speeds. If you use any Ninja Forms extensions, make sure that they are updated to the newest versions to take advantage of this new loading system. Developers can look for the $ninja_forms_loading class for more information about using this powerful new feature.
* Added a new Number field that requires users to enter a number. (Work is currently being done to make this requirement work with non-HTML5 compliant browers.)

*Bugs:*

* Fixed a bug that prevented Custom default values from working with hidden fields.
* Encoded HTML entities in the help hover so that double quotes don’t break anything.

*Changes:*

* Default label position is now above the element.
* Added filter 'ninja_forms_export_sub_pre_value' when exporting subs to access user_value before it is processed (stripslashes and if array implode_r).

= 2.3.8 =

*Bugs:*

* Fixed a bug that caused the Ninja Forms option on post/page edit to not save properly.

*Changes:*

* Simplified some CSS for the star rating field.
* Fixed some 3.8 styling on modal boxes.
* Fixed a few typos.
* Added a default success message to newly created forms.
* Address fields should now how a specific class that can be targeted in JS.

= 2.3.7 =

*Bugs:*

* Input masks should now work without causing Javascript errors.
* "Back to submissions" should now work in IE.

= 2.3.6 =

*Bugs:*

* Fixed a bug in calc filters that could cause infinite loops.
* "Back to submissions" should now work in IE.

*Changes:*

* Add some CSS for 3.8 compatability.
* Removed the "Save Subs" option. Now all submissions are saved by default. This can be changed by using a WordPress filter.
* Moved the "Forms" admin menu up to the same section as posts and custom post types.
* Changed some CSS selector weights ot make overriding a little easier.
* Changed the mask definition to be stored in a data-attribute rather than the field title.
* Changed the currency mask to use the WordPress defined thousands separator and decimal point.

= 2.3.5 =

*Bugs:*

* Fixed a bug that prevented calculations from working properly in Mulit-Part Forms.
* Fixed a bug that could prevent success messages from showing properly in modal forms.

= 2.3.4 =

*Bugs:*

* Fixed up the extend page. Added some extensions and cleaned up CSS.
* Added url encoding to the form settings saved message so it was no longer without spaces.
* Fixed the get_subs function so that it works properly with dates in formats different than m/d/Y.
* Fixed a bug that caused response message fields to be output if multiple forms were on the same page.
* Fixed a bug that caused hidden fields to not show up on the submission edit screen.

*Changes:*

* Adding styling to admin metabox textareas so that they have a minimum size.
* Saving a post/page should no longer create an empty Ninja Forms custom post meta.
* Changed the call to sys_get_temp_dir() to the WordPress get_temp_dir() when trying to create a CSV attachment.
* Added a new filter ninja_forms_email_all_fields_array to give users a way to remove unwanted data from being sent in emails.
* Users can now add Calc values to their List Options import CSV.

= 2.3.3 =

*Bugs:*

* Fixed a bug that prevented sidebars from saving properly on the Field Settings tab and producing a PHP warning.
* Forms should now update their “Date Updated” status when a change is made.

*Changes:*

* Added a default, basic contact form that will be automatically created upon plugin activation.
* Optimized images to make load times faster.
* Changed from underscores to dashes for proper class name of the required fields wrapper.
* Added a new default value for textboxes: Today's Date.
* Increased the number of submissions viewable at once to 300 and 500.
* Minor CSS adjustments for WP 3.8 compatibility.

= 2.3.2 =

*Bugs:*

* Fixed a bug that prevented forms from populated correctly after they had been submitted. i.e. after missing required fields.

*Changes:*

* Changed the way that Ninja Forms figures out what URL a form lives on.
* The "From Address" used in admin emails can now be filtered using ninja_forms_admin_email_from.
* The delimiter, new line marker, and byte order mark can now all be filtered. See includes/admin/export-subs.php for more information. (Thanks to Jörn Lund!)
* Changed button colors on the submission editing page to be inline with WordPress standards.

= 2.3.1 =

*Bugs:*

* Fixed a bug that prevented "From Email" and "From Name" from saving properly on the Form Settings page.
* Star Ratings fields can now be set to required. The user will be forced to enter at least one star.
* Removed some PHP notices that could appear when using the widget.

*Changes:*

* The datepicker used in the Ninja Forms admin section should now respect the date settings on the Plugin Settings page.

= 2.3 =

*Bugs:*

* Fixed several minor bugs related to saving form fields.

*Changes:*

* Added a new submit feature called "Timed Submit." This new submit button will force users to wait a certain number of seconds before they can submit your form. Thanks helgatheviking!
* Improved the Form Settings UI by adding a much-requested "Email From Name" field setting. You can use this to more easily define the name that emails appear to be from.
* Added an easier way for developers to troubleshoot JS errors.
* The "Use this as the From Email for Administrators..." setting has been removed in favour of one labelled "Set this as the reply-to email" for the form. We think that this presents a clearer interface and is easier to understand.

= 2.2.56 =

*Bugs:*

* Fixed a bug that prevented bulk actions from working on submissions.
* Duplicating a form should now properly change ninja_forms_field references in the admin and user email subjects.

*Changes:*

* Added a honeypot field for catching spam bots. Thanks helgatheviking!
* The date input mask should now reflect the settings for date format.
* Added a "button" option type to the admin metabox output.
* Changed the way that Ninja Forms attempts to get a form's URL to the wp_guess_url() function.
* Added the option to give a field type a default label and default value when registering a field type.

= 2.2.55 =

*Bugs:*

* Fixed a security-related bug that prevented pluagin and form settings from saving properly.

*Changes:*

* Add smtp option output to System Status to assist in approved email issue troubleshooting.

= 2.2.54 =

*Bugs:*

* Fixed a security-related bug that prevented new forms from being created.

*Changes:*

* Added better input escaping to improve form security.

= 2.2.53 =

*Bugs:*

* Fixed some minor bugs related to default values for hidden and textbox fields.

*Changes:*

* Added a new resource that will allow users to quickly and easily add their pertinent system information when requesting support for Ninja Forms. The new option can be found underneath the Forms menu: System Status.

= 2.2.52 =

*Changes:*

* Added new options to the default value setting: Post ID, Post Title, and Post URL.
* Changed the date format in ninja_forms_get_subs() to allow 24 hour date selection.
* Improved performance with the MP6 plugin.

= 2.2.51 =

*Bugs:*

* Fixed a bug that prevented advanced calculations from working properly.
* Fixed some bugs with the field descripition.

*Changes:*

* Added a message to the license tab if no extensions are installed.
* Improved the structure of metaboxes.

= 2.2.50 =

*Bugs:*

* Fixed a major bug that could prevent calculation fields from working properly when more than one calculation referenced the same field.
* Fixed a bug that caused calculation fields to work incorrectly with the Conditional Logic extension.

*Changes:*

* Added the "visible" HTML5 data attribute to fields.
* Removed functions from core that were specific to the Conditional Logic extension.

= 2.2.49 =

*Bugs:*

* Duplicated or imported forms that use the [ninja_forms_field] shortcode should now have their references automatically changed after duplication/import.
* Fixed a bug that caused the Total calculation field to work incorrectly with Tax and Subtotals.
* Fixed a major bug that could cause calculation fields to not work when using the currencty input mask.
* Fixed a bug that caused special characters to appear incorrectly in List fields.

= 2.2.48 =

*Bugs:*

* Fixed a bug that could prevent forms from displaying properly.

*Changes:*

* Added new extensions to the add-ons page.

= 2.2.47 =

*Bugs:*

* Fixed several bugs that could prevent calculation fields from working properly.
* Fixed several bugs that could cause calculation fields to show incorrect totals upon page load.

*Changes:*

* Added files to core to make updating extensions easier for developers.

= 2.2.46 =

*Bugs:*

* Fixed a bug that could prevent the ninja_forms_field shortcode from being processed properly.
* Fixed a bug that could prevent the list of fields from being attached to both user and admin emails.

= 2.2.45 =

*Features:*

* Added a new fields description option. Using the rich text editor add text or images to your common field types and display the before or after the field output or before or after the label.

*Changes:*

* Ninja Forms form field values will no longer be available in a $_SESSION variable. Instead, a form's success page may use the global $ninja_forms_processing object to access submitted values.

= 2.2.44 =

*Changes:*

* Fixed many previously untranslated strings. Big thanks to daveshine for the hard work in making Ninja Forms translation friendly.

= 2.2.43 =

*Changes:*

* Changed all references to wpninjas.com to the new ninjaforms.com.

= 2.2.42 =

*Features:*

* Added the option to duplicate a form from the "All Forms" list; simply select "duplicate" and a clone of your form will be added to the list.

*Changes:*

* Added the ability to search the submission database by 'action.'
* Fixed several misspellings.
* Added an extra check to required fields to make them more stable.

*Bugs:*

* Calculation fields should now work properly with currency masks.
* Error messages should no longer appear on fields that shouldn't have errors.
* Cleaned up the CSS.

= 2.2.41 =

*Features:*

* Added the option to disable the "add media" button on the Textarea field.

*Changes:*

* Improved the back-end processing of Calculation fields.
* Added a new filter for the field wrapper: ninja_forms_display_field_wrap_class.

*Bugs:*

* Fixed a bug that prevented Calculation fields from displaying properly in emails.

= 2.2.40 =

*Bugs:*

* Fixed a bug with list fields and calculations that caused the item label to be used rather than the calc value.
* Importing forms with calculation fields should now work properly.
* Fixed a few PHP notices. Thanks @mpeshev!

= 2.2.38 =

*Bugs:*

* Fixed a bug that prevented Calculation fields from working properly with the Conditional Logic extension.
* The invalid Email message label should now properly populate upon plugin activation.
* Fixed a bug that caused the CSV files attached to admin emails to only contain the date.

*Changes:*

* Added an "alt" tag to the loading ajax image.

= 2.2.37 =

*Changes:*

* Changed the method by which JS events could be added before and after form submission. Previously, a proprietary function registration system was used. In this version, however, this system has been dropped in favor of the jQuery event method. Ninja Forms will now fire a custom event of "beforeSubmit" before a form is submitted. Returning false in a function handler on this event will cause submission to be cancelled. "submitResponse" is the custom event fired by Ninja Forms when a form has been submitted. See includes/js/dev/ninja-forms-display.js for details in the comments.
* Added a credit card field that can be enabled by payment gateway developers.
* Added the ability to group user information fields by billing, shipping, or a custom group name. These are available with the get_current_info() method.
* Changed the email field so that validation works even if the field is not required.

= 2.2.36 =

*Bugs:*

* Email validation should now work properly.

= 2.2.35 =

*Changes:*

* Preparing for the release of WordPress 3.6, changed the instances of .live() in Ninja Forms Javascript to .on().

= 2.2.34 =

*Changes:*

* Added the ability to make Country fields required.
* Added the ability to add a custom first option to the Country field.

= 2.2.33 =

*Bugs:*

* Fixed a bug that prevented submissions from being edited if the label position was set to inside.
* Fixed a bug that generated a PHP Notice when adding new fields.
* Fixed a bug that prevented ninja_forms_get_subs() from working properly when a field => value pair was sent.
* Code cleanup.

= 2.2.32 =

*Changes:*

* Added translation ability to many, many previously untranslatable strings.
* Added an improved German translation file.
* Improved the way that Ninja Forms handles translations so that translation files may be placed in the WP_LANG_DIR/ninja-forms/ directory rather than the main plugin directory. This will make them safe from plugin updates.
* Thank you very much to David Decker for these changes!
* Moved the Forms Setting metabox registration from 'init' to 'admin_init' so that it is only called on the admin-side.

= 2.2.31 =

*Features:*

* Add Danish translation files for Ninja Forms.

*Changes:*

* Sidebars and individual field settings should now remember their state (open or closed) upon page reload.

= 2.2.30 =

*Features:*

* Added the new Calculation field. This field allows you to perform simple to complex mathematic operations, as well as collect payment data from the user.
* Added the new Sub Total field. This calculation field allows you to collect payment data from the user.
* Added the new Total field. This calculation field allows you to collect payment data from the user.
* Added the new Tax field. This calculation field allows you to collect payment data from the user.
* Added a new sidebar to the field settings tab to hold the new Payment Fields.
* Added several new fields for capturing a user's personal information: First Name, Last Name, Address, City, State, Country, Postal Code, Email Address, Phone.
* Added a new sidebar to the field settings tab to hold the new Personal Information fields.

*Changes:*

* Removed the Defined Fields sidebar from the Field Settings tab.
* Submit and Anti-Spam fields have been moved into the Template Fields sidebar.

*Bugs:*

* Fixed a bug that prevented Text Layout Elements from showing properly on Multi-Part Forms.

= 2.2.29 =

*Changes:*

* Added a filter to the edit field LI label on the Field Settings tab.
* Added a field label to the layout text element. This label will not be displayed on the front-end, but will allow users to organize their forms better by naming these sections so that they are not all labelled "Text."

*Bugs:*

* Corrected several strings that weren't setup for translation.
* Other minor bugfixes.

= 2.2.28 =

*Bugs:*

* Fixed a major bug that caused a php warning to appear when downloading CSV files and submitting forms.

= 2.2.27 =

*Changes:*

* Now includes a German translation. [Thanks to wp-buddy @ GitHub!]

*Bugs:*

* CSV files should now contain HTML rather than encoded HTML entities.
* Fixed a major bug that prevented translated strings from actually being translated. [Thanks to wp-buddy @ GitHub!]
* Corrected several strings that weren't setup for translation.
* Other minor bugfixes.

= 2.2.26 =

*Bugs:*

* Fixed a bug that prevented List Fields from working properly with Selected Defaults.
* Other minor bugfixes.

= 2.2.25 =

* If you are having issues with your Text fields displaying HTML code, please update to this version.

*Bugs:*

* Fixed a widespread bug that caused the fields using Rich Text Editors to save encoded HTML rather than HTML.
* Fixed a bug that prevented Multiselect and Checkbox list fields from re-populating properly when the page reloaded.

*Changes:*

* Added two new filters that are ran when a submission is exported to CSV. ninja_forms_export_sub_label and ninja_forms_export_sub_value.
* Moved the location of the ninja_forms_email_user_value filter to be more effective.

= 2.2.24 =

*Bugs:*

* Fixed a bug that caused the form settings tab to reload when a new form is created.

= 2.2.23 =

*Bugs:*

* Fixed a fairly serious bug that could keep settings pages from saving properly.

= 2.2.22 =

*Bugs:*

* Fixed a bug that prevented the submissions page from working properly.

*Changes:*

* The submissions page will now show the first form's submissions by default.
* Removed the "Inside" label position for checkboxes and other elements that can't use this location.
* The Favorite Field export will now display a message if no favorite fields are found.

= 2.2.21 =

*Features:*

* Added the option to give a hidden field or textbox the default value of the logged-in user's ID.

*Bugs:*

* Fixed a bug that caused options to be shown and hidden improperly on the Form Settings page.

*Changes:*

* If you do not setup an email "From Address" or an "Admin Mailto Address," Ninja Forms will automatically use the default WordPress admin email address.

= 2.2.20 =

*Bugs:*

* Fixed a bug with required fields.

= 2.2.19 =

*Bugs:*

* Fixed a bug that was causing wpautop() to be applied to non-HTML emails. (Special thanks to Just-Ghali via GitHub)

= 2.2.18 =

*Bugs:*

* Fixed a bug that was preventing the field settings page from working properly with lower screen resolutions.
* Removed several php notices.

= 2.2.17 =

*Changes:*

* Added a new div that wraps all the fields when a form is displayed.

*Bugs:*

* Fixed a bug that was causing two error messages to display below the Spam field if it were left empty.
* Fixed a bub with the new bulk list option CSV import.

= 2.2.16 =

*Changes:*

* Added the ability to create list options with a CSV. This will make entering very long dropdowns quite a bit easier. View list field settings for more information.

= 2.2.15 =

*Changes:*

* Added a filter to the submission data table within the wp-admin.

*Bugs:*

* Fixed a bug that was causing some user's success pages to display incorrectly.

= 2.2.14 =

*Changes:*

* Added additional default styling rules to improve general form output includng inline list items when selecting left or right labels.

*Bugs:*

* Fixed the label and field output for the password field to make styling easier.

= 2.2.13 =

*Changes:*

* Added a filter to the individual user-submitted values that are emailed when "Include a list of fields?" is checked - ninja_forms_email_user_value.
* Added a filter to the label of the default field list that is sent if the "Include a list of fields?" is checked - ninja_forms_email_field_label.
* Added a filter to the table of defalut fields that is ssent if the "Include a list of fields?" is checked - ninja_forms_email_field_list.
* Added the option to hide a setting using the admin metabox registration.
* Cleaned up the Basic Settings metabox to make it more inuitive.

= 2.2.12 =

*Bugs:*

* Fixed a bug that could cause administrator emails to not send properly.

*Changes:*

* Moved some options around in the Form Settings tab to make them more usable.

= 2.2.11 =

*Bugs:*

* Fixed a bug that caused forms submitted with errors to not re-fill properly when the form re-loaded.

= 2.2.10 =

*Changes:*

* Added User Analytics to the extensions page.

*Bugs:*

* Fixed a bug that could cause checkboxes not to validate properly when set as required.

= 2.2.9 =

*Changes:*

* Pressing the enter key on the Field Settings tab will no longer submit the field settings.
* Pressing the enter key on the Field Settings tab while typing a list option label or value will now add another option.

= 2.2.8 =

*Features:*

* Added a Star Rating field that will allow users to select from 1 to X stars. The X can be changed to whatever you'd like.

*Changes:*

* Cleaned up portions of the code so that it better fits WordPress standards.

= 2.2.7 =

*Changes:*

* Fixed the transparency on the help text icon
* Added a new filter for form classes. The filter ninja_forms_form_class can be used to add or remove classes to forms. It is passed the current form class and the form ID.
* Reformatted several files to make them cleaner and easier to read.

= 2.2.6 =

*Changes:*

* Improved the way that list fields work slightly.
* Added four action hooks that are ran whenever an admin page is saved. ninja_forms_save_admin_metabox_option, ninja_forms_save_admin_metabox, ninja_forms_save_admin_sidebar, ninja_forms_save_admin_tab.

= 2.2.5 =

*Changes:*

* Added the new Campaign Monitor Extension to the extension list and the add-ons page.

*Bugs:*

* Fixed a typo that redirecte the "More Plugin Information" link to a bad url.
* Fixed another bug that prevented AJAX submissions form working properly with PHP versions below 5.3.
* Fixed a bug that could cause CSV files to not be emailed to administrators.
* Fixed an "undefined variable" notice with the list field type.

= 2.2.4 =

*Changes:*

* Added an admin metabox option type of "multi_select."

*Bugs:*

* Fixed a bug that prevented AJAX submissions form working properly with PHP versions below 5.3.

= 2.2.3 =

*Changes:*

* Ninja Forms should now properly enqueue scripts for masking, datepicker, and other jQuery elements only if they are in your form.
* Added a new filter for the classess output for a field: ninja_forms_display_field_class.

*Bugs:*

* Fixed a bug that was causing list fields to work improperly with the Conditionals Extension.

= 2.2.2 =

*Features:*

* Added an option to email a CSV file of the user's submission to the administrator email addresses upon form submission.

*Changes:*

* Submission emailing has been moved to the ninja_forms_post_process hook from the ninja_forms_process hook.
* The function used to export submissions now accepts a second parameter $return. Setting this to true will cause the function to return the CSV string rather than output the file for download.

= 2.2.1 =

*Changes:*

* Added a new action hook that runs if a form is set to "Require Logged-In", but the user is not logged in. It is named: ninja_forms_display_user_not_logged_in.
* Added new filters for the arguments sent to the wp_editor() function when a rich text editor is rendered. They are:	ninja_forms_textarea_rte (Front-End Textarea), ninja_forms_admin_metabox_rte (Ninja Forms admin settings areas), ninja_forms_edit_field_rte (Field settings).

= 2.2.0 =

*Features:*

* Added a new form setting to prevent non-logged-in users from viewing or submitting the form.
* Added a new filter named: ninja_forms_display_show_form. It is passed $display, a bool() true or false, along with the form_id. It expects a bool() true or false return. If false, the form will not be output to the screen. This is different than the behavior of the ninja_forms_display_form_visibility filter.

*Bugs:*

* Fixed a bug that could cause the success message to not show up properly when using AJAX.

= 2.1.9 =

*Bugs:*

* Fixed a bug that could cause the success message to not show up properly when using AJAX.
* Please note that if you are using a version of PHP lower than 5.3, you may experience some problems using AJAX Submissions. These can be minimized by using simple success/error messages without any quotes or special characters.

= 2.1.8 =

*Bugs:*

* Fixed a major bug with displaying success messages via AJAX.

= 2.1.7 =

*Bugs:*

* Fixed a success message display bug.
* Other minor bugfixes.

= 2.1.6 =

*Bugs:*

* Fixed an email formatting bug that was prevenitng HTML emails from displaying properly for some users.
* Fixed a bug that prevented the "inside" label position from working properly with textareas.
* Fixed a bug that could cause the 'ninja_forms_display_form' shortcode to work incorrectly.

= 2.1.5 =

*Bugs:*

* Fixed a bug with adding administrator email addresses incorrectly.
* Fixed a bug that caused success messages to show in the wrong form when adding multiple forms to a single page.

= 2.1.4 =

*Bugs:*

* Fixed a bug that prevented some installations from being able to use AJAX submissions.
* Fixed a problem with appending a form to a page or using a shortcode that sometimes caused extra breaks

= 2.1.3 =

*Bugs:*

* Fixed a bug that caused some users issues with using AJAX submissions.
* Fixed a bug that caused extensions to show as "installed" even if they were not.

= 2.1.2 =

*Features:*

* AJAX submissions have been re-added. Visit the "Basic Settings" metabox underneath the "Form Settings" tab to enable this feature.
* In the near future, there will be more options and documentation related to this feature.

*Changes:*

* Submissions are now listed from most recent to oldest. A sortable table is in the works.
* Changed the output of the ninja_forms_get_form_id() JS function to just an ID number.

= 2.1.1 =

*Bugs:*

* Hidden Fields should now always properly assign values if set to "Username," "Firstname," etc.

*Changes:*

* Changed the way that the password field works so that it can be used for both registration and normal password entry.
* Added a scheduled action hook (ninja_forms_daily_action) that is executed on a daily basis.

= 2.1.0 =

*Features:*

* Added an option to the "textbox" field type so that the "From Name" of a form submission email can be set as well as the "From Email."

*Changes:*

* $ninja_forms_processing->get_form_setting('sub_id') will now populate with the ID of the submission after it has been inserted into the database.

= 2.0.9 =

*Bugs:*

* Fixed a bug in the redirect script that was causing some users to crash instead of changing sites. Ninja Forms now sets a $_SESSION variable, $_SESSION['ninja_forms_values'], equal to an array that looks like: array( 'field_id' => 32, 'user_value' => 'whatever the user submitted' ) just before it redirects. The target page can access these variables for processing or display.

* Fixed a bug with the ninja_forms_register_tab_metabox_options() function

= 2.0.8 =

*Bugs:*

* Fixed a bug that was causing odd behaviour when two or more forms were on the same page.

*Changes:*

* Modified the way that Ninja Forms outputs admin metaboxes.
* Modified the way that Ninja Forms outputs errors and success messages.

= 2.0.7 =

*Features:*

* Added a new field type of "Password." This field offers the ability to add a password input textbox to your forms, including both password and re-enter password boxes. Ninja Forms will ensure that the text entered into these boxes matches. Please note that using this field within your forms to do things like logins, registrations, etc. will require custom coding.

*Bugs:*

* Modified the way that Ninja Forms created admin metaboxes. Metabox options should now properly output in the order in which they are registered.
* Added a new serializeToArray function to the admin.js to fix a bug some users encountered with popup boxes.

*Changes:*

* Removed unused files.

= 2.0.6 =

*Bugs:*

* Fixed a JS bug that prevented some users from adding new form fields.

= 2.0.5 =

*Features:*

* Added the option to set an email address as the “From” address within the field settings.
* Added basic email validation. This does NOT validate that the email address exists, but only that it is semantically correct.
* Added a label in the plugin settings “Label” tab for an invalid email address.
* Added Shortcode and Template Function output to the Form List. You can now copy and paste the shortcode from there.

*Changes:*

* Admin metaboxes will now remember whether or not they were closed or open.

*Bugs:*

* Fixed a bug that prevented some users from adding new fields.

= 2.0.4 =

*Features:*

* Added a label field to the plugin settings page that will allow users to set the message displayed above the form when there is a required field error.
* Setting a text or textarea’s label position to ‘inside’ will now cause the label to be auto-removed when the input receives focus. If a user leaves the field blank, the label will return.

*Changes:*

* Increased the max file size for form imports.
* Added a filter: ninja_forms_field_wrap_class. This filter is sent a string that contains the wrap class for each field along with its field id.
* The CSS class of the list field wrapping div has been modified slightly. It has been changed from .list to .list-dropdown where dropdown is the list type.
* Added a new shortcode for inserting the date into the success message and emails: [ninja_forms_sub_date format="m/d/Y"]. It follows the php.net date() string formats and defaults to m/d/Y.

*Bugs:*

* Fixed a bug that prevented Help Text from displayin properly when the label was set to the “inside” position.

= 2.0.3 =

*Features:*

* Added a checkbox to allow the appending of field values to administrator emails.
* Save form settings is now checked by default when creating a form.
* Hide form after successful submission is now checked by default when creating a new form.
* Changed the [label] system to [ninja_forms_field id=3] where 3 is the field ID that you want to insert.

*Changes:*

*Changed the way that "settings saved" messages appear. When creating a save function on the admin-backend, you can now return the update message you wish users to see.

*Bugs:*

* Fixed a bug that caused design elements, especially text fields, from showing on multi-part forms.
* Fixed a bug in the backend admin system that was causing problems with the Uploads Browser/Upload Settings tabs.
* To prevent conflicts with other plugins, we removed the position declaration from the admin menu hook. This means that the Forms link will now float to the bottom of the admin menu.
* Fixed a bug that was causing help text to be repeated underneath sections of the Form Settings Tab.
* Fixed a bug that prevented list-based checkboxes from showing up properly when editing submissions.
* Fixed a bug that was causing required checkboxes not to validate properly.

= 2.0.2 =
* Fixed several bugs including:
* A bug that caused multiple forms to a single page broke some JS
* Various export bugs. Exporting submissions should work properly.
* Various activation bugs.
* Varous bugs on the admin/back-end.

* Added widget functionality. You can now add Ninja Forms to your sidebars via a widget.

= 2.0.1 =
* Lots of bug fixes.
* Filter User and Admin email subject line for [bracketed] labels.
* Add an action hook to User and Email sending.
* Add an option to the "user email" section for attaching user submitted values.
* "Settings saved" now appears properly when saving plugin settings.
* Fixed a bug with including display js and css for core and extensions.
* Removed the label "Error message shown when all fields are empty." This wasn't used anyway.
* Added Shortcodes. They can be used like: [ninja_forms_display_form id=3].
* Fixed a bug where saving plugin settings would break upon HTML entry.
* Replaced isset( $ninja_forms_processing with is_object( $ninja_forms_processing.
* Fixed several activation bugs.

= 2.0 =
* Version 2.0 is a major leap forward for Ninja Forms. It is much more stable and developer friendly than previous versions of the plugin.
