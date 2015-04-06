<?php

// Language definitions for frequently used strings
return array(

// Text orientation and encoding
'lang_direction'			=>	'ltr',	// ltr (Left-To-Right) or rtl (Right-To-Left)
'lang_identifier'			=>	'en',

// Number formatting
'lang_decimal_point'		=>	'.',
'lang_thousands_sep'		=>	',',

// Notices
'Bad request'				=>	'Bad request. The link you followed is incorrect or outdated.',
'No view'					=>	'You do not have permission to view these forums.',
'No permission'				=>	'You do not have permission to access this page.',
'CSRF token mismatch'		=>	'Unable to confirm security token. A likely cause for this is that some time passed between when you first entered the page and when you submitted a form or clicked a link. If that is the case and you would like to continue with your action, please click the Confirm button. Otherwise, you should click the Cancel button to return to where you were.',
'No cookie'					=>	'You appear to have logged in successfully, however a cookie has not been set. Please check your settings and if applicable, enable cookies for this website.',


// Miscellaneous
'Forum index'				=>	'Forum index',
'Submit'					=>	'Submit',	// "name" of submit buttons
'Cancel'					=>	'Cancel', // "name" of cancel buttons
'Preview'					=>	'Preview',	// submit button to preview message
'Delete'					=>	'Delete',
'Split'						=>	'Split',
'Ban message'				=>	'You are banned from this forum.',
'Ban message 2'				=>	'The ban expires at the end of %s.',
'Ban message 3'				=>	'The administrator or moderator that banned you left the following message:',
'Ban message 4'				=>	'Please direct any inquiries to the forum administrator at %s.',
'Never'						=>	'Never',
'Today'						=>	'Today',
'Yesterday'					=>	'Yesterday',
'Forum message'				=>	'Forum message',
'Maintenance warning'		=>	'<strong>WARNING! %s Enabled.</strong> DO NOT LOGOUT as you will be unable to login again.',
'Maintenance mode'			=>	'Maintenance Mode',
'Redirecting'				=>	' Redirecting…', // With space!
'Forwarding info'			=>	'You should automatically be forwarded to a new page in %s %s.',
'second'					=>	'second',	// singular
'seconds'					=>	'seconds',	// plural
'Click redirect'			=>	'Click here if you do not want to wait any longer (or if your browser does not automatically forward you)',
'Invalid e-mail'			=>	'The email address you entered is invalid.',
'New posts'					=>	'New posts',	// the link that leads to the first new post
'New posts title'			=>	'Find topics containing posts made since your last visit.',	// the popup text for new posts links
'Active topics'				=>	'Active topics',
'Active topics title'		=>	'Find topics which contain recent posts.',
'Unanswered topics'			=>	'Unanswered topics',
'Unanswered topics title'	=>	'Find topics which have not been replied to.',
'Username'					=>	'Username',
'Registered'				=>	'Registered',
'Write message'				=>	'Write message',
'Forum'						=>	'Forum',
'Posts'						=>	'Posts',
'Pages'						=>	'Pages',
'Page'						=>	'Page',
'BBCode'					=>	'BBCode',	// You probably shouldn't change this
'Smilies'					=>	'Smilies',
'Images'					=>	'Images',
'You may use'				=>	'You may use: %s',
'and'						=>	'and',
'Image link'				=>	'image',	// This is displayed (i.e. <image>) instead of images when "Show images" is disabled in the profile
'wrote'						=>	'wrote',	// For [quote]'s (e.g., User wrote:)
'Code'						=>	'Code',		// For [code]'s
'Forum mailer'				=>	'%s Mailer',	// As in "MyForums Mailer" in the signature of outgoing e-mails
'Write message legend'		=>	'Compose your post',
'Required information'		=>	'Required information',
'Reqmark'					=>	'*',
'Required warn'				=>	'All fields with bold label must be completed before the form is submitted.',
'Crumb separator'			=>	' &rarr;&#160;', // The character or text that separates links in breadcrumbs
'Title separator'			=>	' — ',
'Page separator'			=>	'&#160;', //The character or text that separates page numbers
'Spacer'					=>	'…', // Ellipsis for paginate
'Paging separator'			=>	' ', //The character or text that separates page numbers for page navigation generally
'Previous'					=>	'Previous',
'Next'						=>	'Next',
'Cancel redirect'			=>	'Operation cancelled.',
'No confirm redirect'		=>	'No confirmation provided. Operation cancelled.',
'Please confirm'			=>	'Please confirm:',
'Help page'					=>	'Help with: %s',
'Re'						=>	'Re:',
'Page info'					=>	'(Page %1$s of %2$s)',
'Item info single'			=>	'%s: %s',
'Item info plural'			=>	'%s: %s to %s of %s', // e.g. Topics [ 10 to 20 of 30 ]
'Info separator'			=>	' ', // e.g. 1 Page | 10 Topics
'Powered by'				=>	'Powered by %s, supported by %s.',
'Maintenance'				=>	'Maintenance',
'Installed extension'		=>	'The %s official extension is installed. Copyright &copy; 2003&ndash;2012 <a href="http://punbb.informer.com/">PunBB</a>.',
'Installed extensions'		=>	'Currently installed <span id="extensions-used" title="%s">%s official extensions</span>. Copyright &copy; 2003&ndash;2012 <a href="http://punbb.informer.com/">PunBB</a>.',

// CSRF confirmation form
'Confirm'					=>	'Confirm',	// Button
'Confirm action'			=>	'Confirm action',
'Confirm action head'		=>	'Please confirm or cancel your last action',

// Title
'Title'						=>	'Title',
'Member'					=>	'Member',	// Default title
'Moderator'					=>	'Moderator',
'Administrator'				=>	'Administrator',
'Banned'					=>	'Banned',
'Guest'						=>	'Guest',

// Stuff for include/parser.php
'BBCode error 1'			=>	'[/%1$s] was found without a matching [%1$s]',
'BBCode error 2'			=>	'[%s] tag is empty',
'BBCode error 3'			=>	'[%1$s] was opened within [%2$s], this is not allowed',
'BBCode error 4'			=>	'[%s] was opened within itself, this is not allowed',
'BBCode error 5'			=>	'[%1$s] was found without a matching [/%1$s]',
'BBCode error 6'			=>	'[%s] tag had an empty attribute section',
'BBCode nested list'		=>	'[list] tags cannot be nested',
'BBCode code problem'		=>	'There is a problem with your [code] tags',

// Stuff for the navigator (top of every page)
'Index'						=>	'Index',
'User list'					=>	'User list',
'Rules'						=>	'Rules',
'Search'					=>	'Search',
'Register'					=>	'Register',
'register'					=>	'register',
'Login'						=>	'Login',
'login'						=>	'login',
'Not logged in'				=>	'You are not logged in.',
'Profile'					=>	'Profile',
'Logout'					=>	'Logout',
'Logged in as'				=>	'Logged in as %s.',
'Admin'						=>	'Administration',
'Last visit'				=>	'Last visit %s',
'Mark all as read'			=>	'Mark all topics as read',
'Login nag'					=>	'Please login or register.',
'New reports'				=>	'New reports',

// Alerts
'New alerts'				=>	'New Alerts',
'Maintenance alert'			=>	'<strong>Maintenance mode enabled.</strong> <em>DO NOT</em> logout, if you do you will not be able to login again.',
'Updates'					=>	'PunBB updates:',
'Updates failed'			=>	'The latest attempt at checking for updates against the punbb.informer.com updates service failed. This probably just means that the service is temporarily overloaded or out of order. However, if this alert does not disappear within a day or two, you should disable the automatic check for updates and check for updates manually in the future.',
'Updates version n hf'		=>	'A newer version of PunBB, version %s, is available for download at <a href="http://punbb.informer.com/">punbb.informer.com</a>. Furthermore, one or more hotfixes are available for install on the <a href="%s">Manage hotfixes</a> tab of the admin interface.',
'Updates version'			=>	'A newer version of PunBB, version %s, is available for download at <a href="http://punbb.informer.com/">punbb.informer.com</a>.',
'Updates hf'				=>	'One or more hotfixes are available for install on the <a href="%s">Manage hotfixes</a> tab of the admin interface.',
'Database mismatch'			=>	'Database version mismatch',
'Database mismatch alert'	=>	'Your PunBB database is meant to be used in conjunction with a newer version of the PunBB code. This mismatch can lead to your forum not working properly. It is suggested that you upgrade your forum to the newest version of PunBB.',

// Stuff for Jump Menu
'Go'						=>	'Go',		// submit button in forum jump
'Jump to'					=>	'Jump to forum:',

// For extern.php RSS feed
'RSS description'			=>	'The most recent topics at %s.',
'RSS description topic'		=>	'The most recent posts in %s.',
'RSS reply'					=>	'Re: ',	// The topic subject will be appended to this string (to signify a reply)

// Accessibility
'Skip to content'			=>	'Skip to forum content',

// Debug information
'Querytime'					=>	'Generated in %1$s seconds (%2$s%% PHP - %3$s%% DB) with %4$s queries',
'Debug table'				=>	'Debug information',
'Debug summary'				=>	'Database query performance information',
'Query times'				=>	'Time (s)',
'Query'						=>	'Query',
'Total query time'			=>	'Total query time',

// Error message
'Forum error header'		=> 'Sorry! The page could not be loaded.',
'Forum error description'	=> 'This is probably a temporary error. Just refresh the page and retry. If problem continues, please check back in 5-10 minutes.',
'Forum error location'		=> 'The error occurred on line %1$s in %2$s',
'Forum error db reported'	=> 'Database reported:',
'Forum error db query'		=> 'Failed query:',

);
