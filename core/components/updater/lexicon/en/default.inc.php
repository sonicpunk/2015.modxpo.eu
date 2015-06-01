<?php

/**
 * Updater
 * 
 * Copyright 2015 inreti GmbH <info@inreti.de>
 * Author: Jens Külzer
 *
 * Updater is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Updater is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Updater; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 */

/**
 * Default Language file for Updater
 *
 * @package updater
 * @subpackage lexicon
 */

$_lang['setting_updater.debug'] = "Debug mode";
$_lang['setting_updater.debug_desc'] = "Turns on debug mode";


/* Area widget */
$_lang['updater.widget'] = 'Update status';
$_lang['updater.widget.desc'] = '<strong>Versions information widget for your dashboard!</strong> This widget shows you if there is an update for the MODX core available <em>and</em> if there are package updates available for download or install.';


/* Area Broadcast */
$_lang['setting_updater.show_broadcast_messages'] = "Show the updater broadcast messages";
$_lang['setting_updater.show_broadcast_messages_desc'] = "Enable if you want to be warned on every manager page and not only by the dashboard. Warning: Deletion of the message bar does not work persistently at the moment.";


/* Area cache */
$_lang['setting_updater.cache_expires_core'] = "Core update cache expiration time";
$_lang['setting_updater.cache_expires_core_desc'] = "Cache expiration time in seconds. Per default only search once a day for new core updates. Updater uses its own cache partition, clearing the cache in the manager has no effect. Please be aware that values less than one day aka 86400 seconds will not be accepted to safe github from massive api calls.";

$_lang['setting_updater.github_timeout'] = "Github tag lookup timeout";
$_lang['setting_updater.github_timeout_desc'] = "A timeout for looking up new version tags at github. You can adjust this according to your servers connection - keep as low as possible.";

$_lang['setting_updater.modxcom_timeout'] = "MODX.com lookup timeout";
$_lang['setting_updater.modxcom_timeout_desc'] = "Timeout in seconds for the lookup of the zipball of a new version at MODX.com. <em> Currently not used. This will enable automatic download to the server later.</em>";

$_lang['setting_updater.last_version_crosschecked'] = "Last crosschecked version";
$_lang['setting_updater.last_version_crosschecked_desc'] = "This value helps to detect that your system has been updated recently. If it differs from the system version setting, the updater will refresh its own cache partition. <strong>Do not change this value manually!</strong>";


/* Area Core Notifications */
$_lang['setting_updater.core_notifications_mail'] = "SysAdmin e-mail address for core notifications";
$_lang['setting_updater.core_notifications_mail_desc'] = "E-mail address to send notifications of core updates to.";

$_lang['setting_updater.send_core_notifications'] = "Send core notifications to SysAdmin";
$_lang['setting_updater.send_core_notifications_desc'] = "Should notification emails be send to the given email address?";

$_lang['setting_updater.send_core_notifications_user'] = "Send core notifications to users?";
$_lang['setting_updater.send_core_notifications_user_desc'] = "<strong>NOT IMPLEMENTED YET!</strong> Whether to send users with the permission <em>'receive_core_notifications'</em>, the core update mails.";


/* Area Package Notifications */
$_lang['setting_updater.package_notifications_mail'] = "PackageAdmin e-mail address for package notifications";
$_lang['setting_updater.package_notifications_mail_desc'] = "E-mail address to send notifications of package updates to.";

$_lang['setting_updater.send_package_notifications'] = "Send package notifications to PackageAdmin";
$_lang['setting_updater.send_package_notifications_desc'] = "Should notification emails be send to the given email address?";

$_lang['setting_updater.send_package_notifications_user'] = "Send package notifications to users?";
$_lang['setting_updater.send_package_notifications_user_desc'] = "<strong>NOT IMPLEMENTED YET!</strong> Whether to send users with the permission <em>'receive_package_notifications'</em> the package update mails.";


/* Area Digest Notifications */
$_lang['setting_updater.send_version_digest_user'] = "Send a version digest notification";
$_lang['setting_updater.send_version_digest_user_desc'] = "<strong>PARTIALLY IMPLEMENTED!</strong> Whether to send users with the permission <em>'receive_digests'</em> a version details email.";

$_lang['setting_updater.send_version_digest_hours'] = "Version digest notification time span";
$_lang['setting_updater.send_version_digest_hours_desc'] = "Minimum number of hours between version digest notifications. Default is 720 (one month).";


/* Area General Notifications */
$_lang['setting_updater.mail_format_html'] = "Use HTML as mail format";
$_lang['setting_updater.mail_format_html_desc'] = "Whether to use HTML in notifications. Default is No (text/plain). Attention: if you choose HTML you have a good chance that notifications are filtered out by spam filters like SpamAssasin (modx does not provide core mail functions to send a multipart message).";

$_lang['setting_updater.send_notification_hours'] = "Minimum hours between update notifications";
$_lang['setting_updater.send_notification_hours_desc'] = "The minimum hours between two notifications of the same type. <em>Note: type means (core||package) here.</em>.";

/* Area Persistance */
$_lang['setting_updater.last_send_core_notification'] = "Time of last send core notification";
$_lang['setting_updater.last_send_core_notification_desc'] = "<strong>DO NOT CHANGE!</strong> Settings is used automatically.";

$_lang['setting_updater.last_send_package_notification'] = "Time of last send package notification";
$_lang['setting_updater.last_send_package_notification_desc'] = "<strong>DO NOT CHANGE!</strong> Settings is used automatically.";

$_lang['setting_updater.last_send_version_digest'] = "Time of last send version digest";
$_lang['setting_updater.last_send_version_digest_desc'] = "<strong>DO NOT CHANGE!</strong> Settings is used automatically.";

$_lang['setting_updater.last_send_core'] = "Last send core data";
$_lang['setting_updater.last_send_core_desc'] = "<strong>DO NOT CHANGE!</strong> Settings is used automatically.";

$_lang['setting_updater.last_send_packages'] = "Last send packages data";
$_lang['setting_updater.last_send_packages_desc'] = "<strong>DO NOT CHANGE!</strong> Settings is used automatically.";


/*
$_lang['setting_updater.'] = "";
$_lang['setting_updater._desc'] = "";
*/
