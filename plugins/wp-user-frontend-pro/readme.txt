=== WP User Frontend ===

For Installations and other instructions, visit the online documentation:
http://docs.wedevs.com/wp-user-frontend-pro/




== Changelog ==

= version 2.1.9 =

date: 9 January, 2014

 * [fix] PayPal payment problem fixed.
 * [updated] New version of Settings API class

= version 2.1.8 =

date: 18 September, 2013

 * [new] associate uploaded images to post area
 * [new] tags autocomplete 
 * [new] default post form assignment
 * [new] taxonomy exclude/include/child_of feature
 * [new] free subscription option
 * [new] new edit post status "No Change"
 * [new] delete transaction option
 * [new] signup page override redirection
 * [new] insert photo: image size selection
 * [new] insert photo: type of image selection
 * [new] "pending" post edit enable/disable option

= version 2.1.7 =

date: 12 July, 2013

* [fix] accidental input field on checkbox removed
* [improve] shortcode for map in post
* [improve] login checking on edit pages
* [improve] inline google maps script removed
* [new] scripts loading option added
* [new] pagination style in dashboard
* [improve] flash runtime removed from plupload runtime

= version 2.1.6 =

date: 22 June, 2013

* [fix] rich textarea post draft bug fix
* [fix] checkbox help text was left out
* [fix] help text for radio field
* [improve] map short code updated and separated to user and post map functions
* [new] non hierarchical taxonomy update support
* [new] theme my login custom email notification action hook fire
* [new] wp cli compatibility
* [new] hidden custom field
* [new] custom taxonomy text input field type added

= version 2.1.5 =

date: 22 May, 2013

* [fix] Comment form fix in dashboard
* [fix] Google map fix in admin edit post area
* [fix] insufficient arguments in admin profile area
* [fix] feature image delete bug in edit post area
* [improve] multisite license notice fix
* [new] dashboard unauthorized message
* [new] not logged in message in user profile form
* [improve] updated language file


= version 2.1.4 =

date: 27 April, 2013

* [bugfix] edit post permission checking
* [bugfix] WYSIWYG Text cut off after using "&"
* [improve] user avatar image url changed from relative to full url. fixes multisite bug
* [improve] `wpuf_can_post` filter gets more parameters
* [improve] repeatable fields separator changed from comma(,) to pipe(|)
* [improve] featured image in dashboard is now linked to posts
* [new] google map autocomplete address feature
* [new] file links added in admin panel post edit custom fields area
* [new] payment gateway bank added
* [new] validation filter added on new/edit post: `wpuf_update_post_validate`, `wpuf_add_post_validate`
* [new] private post status added on dashboard query
* [new] dashboard table hooks added: wpuf_dashboard_head_col, wpuf_dashboard_row_col
* [new] post draft option added. posts now can set to draft for later usage
* [new] default post category option
* [new] dashboard query filter added: `wpuf_dashboard_query`
* [new] teeny rich textarea added

= version 2.1.3 =

date: 18 April, 2013

* [bugfix] comment issue fixed
* [bugfix] date issue fixed
* [bugfix] post author changed
* [new] taxonomy ORDER option
* [new] taxonomy ORDER BY option.
* [new] post format support
* [new] user registration filter
* [new] user registration after filter
* [update] file upload size changed to KiloByte

= version 2.1.2 =

date: 5 April, 2013

* [bugfix] post edit area captcha fix
* [bugifx] Featured default image path fix
* [new] Show post status filter added
* [new] Dashboard query filter added
* [improve] *From* typo fix in editor
* [improve] It won't slow down your site now.

= version 2.1.1 =

date: 23 March, 2013

* [bugfix] License check bug fix

= version 2.1 =

date: 22 March, 2013

* Subscription feature put back
* Auto update feature

= version 2.0 =

* Multiple form added
* Registration builder
* Profile builder
* New Codebase

= version 1.1 =

* warning for multisite fix
* allow category bug fix
* fix ajaxurl in ajaxified category
* custom post type dropdown fix in admin
* post date bug fix
* category dropdown fix

= version 1.0 =

* Admin panel converted to settings API
* Ajax featured Image uploader added (using plupload)
* Ajax attachment uploader added (using plupload)
* Rich/full/normal text editor mode
* Editor button fix on twentyelven theme
* Massive Code rewrite and cleanup
* Dashboard replaced with WordPress loop
* Output buffering added for header already sent warning
* Redirect user on deleting a post
* Category checklist added
* Post publish date fix and post expirator changed from hours to day
* Subscription and payment rewrite. Extra payment gateways can be added as plugin
* Other payment currency added

= version 0.7 =

* admin ui improved
* updated new post notification mail template
* custom fields and attachment show/hide in posts
* post edit link override option
* ajax "posting..." changed
* attachment fields restriction in edit page
* localized ajaxurl and posting message
* improved action hooks and filter hooks

= version 0.6 =
---------------

* fixed error on attachment delete
* added styles on dashboard too
* fixed custom field default dropdown
* fixed output buffering for add_post/edit_post/dashboard/profile pages
* admin panel scripts are added wp_enqueue_script instead of echo
* fixed admin panel block logic
* filter hook added on edit post for post args

= version 0.5 =

* filters on add posting page for blocking the post capa
* subscription pack id added on user meta upon purchase
* filters on add posting page for blocking the post capa
* option for force pack purchase on add post. dropdown p
* subscription info on profile edit page
* post direction fix after payment
* filter added on form builder


= version 0.4 =

* missing custom meta field added on edit post form
* jQuery validation added on edit post form

= version 0.3 =

* rich/plain text on/off fixed
* ajax chained category added on add post form
* missing action added on edit post form
* stripslashes on admin/frontend meta field
* 404 error fix on add post

= version 0.2 =

* Admin settings page has been improved
* Header already sent warning messages has been fixed
* Now you can add custom post meta from the settings page
* A new pay per post and subscription based posting options has been introduced (Only paypal is supported now)
* You can upload attachment with post
* WYSIWYG editor has been added
* You can add and manage your users from frontend now (only having the capability to edit_users )
* Some action and filters has been added for developers to add their custom form elements and validation
* Pagination added in post dashboard
* You can use the form to accept "custom post type" posts. e.g: [wpuf_addpost post_type="event"]. It also applies for showing post on dashboard like "[wpuf_dashboard post_type="event"]"
* Changing the form labels of the add post form is now possible from admin panel.
* The edit post page setting is changed from URL to page select dropdown.
* You can lock certain users from posting from their edit profile page.

== Upgrade Notice ==

Nothing to say
