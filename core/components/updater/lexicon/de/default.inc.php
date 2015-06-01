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

$_lang['setting_updater.debug'] = "Debug-Modus";
$_lang['setting_updater.debug_desc'] = "Schaltet den Debug-Modus an.";


/* Area widget */
$_lang['updater.widget'] = 'Update Status';
$_lang['updater.widget.desc'] = '<strong>Versionsanzeige im Dashboard</strong> Zeigt Ihnen verfügbare Updates für Ihr MODX-System und verfügbare Updates für installierte Extras an.';


/* Area Broadcast */
$_lang['setting_updater.show_broadcast_messages'] = "Zeigt Nachrichten über Updates im Manager an";
$_lang['setting_updater.show_broadcast_messages_desc'] = "Aktivieren Sie diese Option, wenn Sie auf <em>jeder</em> Manager-Seite über verfügbare Updates informiert werden wollen. Achtung: in der aktuellen Version ist das dauerhafte Deaktivieren einer solchen Nachricht noch nicht möglich!";


/* Area cache */
$_lang['setting_updater.cache_expires_core'] = "Cache-Ablaufzeit für Core Updates";
$_lang['setting_updater.cache_expires_core_desc'] = "Die Cache-Ablaufzeit in Sekunden. Standardmäßig sucht Updater nur einmal am Tag nach neuen Core-Updates. Bitte beachten: Werte kleiner als 86400 werden nicht aktzeptiert und durch den Default-Wert ersetzt, um die Abfragefrequenz auf Github zu schonen.<br/>Das Leeren des Caches reicht zum Zurücksetzen nicht aus, da Updater eine eigene Cache-Partition benutzt. Zum Zurücksetzen muss der Cache-Folder der Partition gelöscht werden.";

$_lang['setting_updater.github_timeout'] = "Github Netzwerk-Timout";
$_lang['setting_updater.github_timeout_desc'] = "Ein Timeoutwert für die Github-Abfragen in Millisekunden. Passen Sie diesen Wert ggf. an Ihre Netzwerkanbindung an.";

$_lang['setting_updater.modxcom_timeout'] = "MODX.com Netzwerk-Timeout";
$_lang['setting_updater.modxcom_timeout_desc'] = "Ein Timeoutwert für Anfragen bei MODX.com für die Suche nach ZIP-Paketen neuer Versionen. <em>Wird in dieser Version nicht genutzt.</em>";

$_lang['setting_updater.last_version_crosschecked'] = "Letzte getestete Systemversion";
$_lang['setting_updater.last_version_crosschecked_desc'] = "Dieser Wert hilft beim entdecken eines kürzlich erfolgten Updates. Wenn er sich von der aktuellen System-Version unterscheidet, dann leert Updater seinen Cache, um seine Benachrichtigungen aktualisieren zu können. <strong>Diesen Wert nicht manuell ändern!</strong>";



/* Area Core Notifications */
$_lang['setting_updater.core_notifications_mail'] = "SysAdmin E-Mail Adresse für Core-Nachrichten";
$_lang['setting_updater.core_notifications_mail_desc'] = "Geben Sie hier SysAdmin E-Mail-Adresse ein, an die Benachrichtigungen über Core-Updates geschickt werden sollen.";

$_lang['setting_updater.send_core_notifications'] = "Nachrichten über Core-Updates per E-Mail versenden?";
$_lang['setting_updater.send_core_notifications_desc'] = "Sollen Nachrichten über Core-Updates per E-Mail versendet werden?";

$_lang['setting_updater.send_core_notifications_user'] = "Nachrichten über Core-Updates an berechtigte Nutzer senden?";
$_lang['setting_updater.send_core_notifications_user_desc'] = "Geben Sie hier an, ob Nachrichten über Core-Updates auch an Nutzer mit der Berechtigung<em>receive_core_notifications</em> versendet werden sollen.";


/* Area Package Notifications */
$_lang['setting_updater.package_notifications_mail'] = "PackageAdmin E-Mail-Adresse für Package-Nachrichten";
$_lang['setting_updater.package_notifications_mail_desc'] = "Geben Sie hier die PackageAdmin E-Mail-Adresse an, an die Benachrichtigungen über Package-Updates geschickt werden sollen.";

$_lang['setting_updater.send_package_notifications'] = "Nachrichten über Package-Updates per E-Mail versenden?";
$_lang['setting_updater.send_package_notifications_desc'] = "Sollen Nachrichten über Package-Updates per E-Mail versendet werden?";

$_lang['setting_updater.send_package_notifications_user'] = "Nachrichten über Package-Updates an berechtigte Nutzer senden?";
$_lang['setting_updater.send_package_notifications_user_desc'] = "Geben Sie hier an, ob Nachrichten über Package-Updates auch an Nutzer mit der Berechtigung <em>receive_package_notifications</em> versendet werden sollen.";


/* Area Digest Notifications */
$_lang['setting_updater.send_version_digest_user'] = "Nachrichten mit Versionszusammenfassung per E-Mail versenden?";
$_lang['setting_updater.send_version_digest_user_desc'] = "Geben Sie hier an, ob Nachrichten mit den aktuellen Versionsständen an Nutzer mit der Berechtigung <em>receive_digest</em> gesendet werden sollen.";

$_lang['setting_updater.send_version_digest_hours'] = "Zeitspanne zwischen Versionszusammenfassungen";
$_lang['setting_updater.send_version_digest_hours_desc'] = "Die Mindestanzahl an Stunden, die zwischen zwei Versionszusammenfassungen liegen soll. Default ist 720 (entspricht 1 Monat).";


/* Area General Notifications */
$_lang['setting_updater.mail_format_html'] = "Nutze HTML als E-Mail-Format";
$_lang['setting_updater.mail_format_html_desc'] = "Ob die E-Mails im HTML-Format verschickt werden sollen. Standard ist <em>Nein</em> (text/plain).
<strong>Achtung: </strong>wenn Sie HTML Format wählen, dann stellen Sie sicher, dass die E-Mails nicht in Ihrem Spam-Filter landen. Die verwendete MODX Mailfunktion erlaubt leider keine multipart Nachrichten, so dass reine HTML-E-Mails von Spamfiltern wie spamassassin kritisch bewertet werden.";

$_lang['setting_updater.send_notification_hours'] = "Minimale Stundenzahl zwischen Update-Benachrichtigungen";
$_lang['setting_updater.send_notification_hours_desc'] = "Die minimale Stundenzahl zwischen zwei Benachrichtigungen desselben Typs. Hinweis: Typ heißt hier entweder <em>core</em> oder <em>package</em>.";


/* Area Persistance */
$_lang['setting_updater.last_send_core_notification'] = ">Zeitpunkt letzte Core-Nachricht";
$_lang['setting_updater.last_send_core_notification_desc'] = "<strong>NICHT ÄNDERN!</strong> Einstellung wird automatisch vom System verwaltet.";

$_lang['setting_updater.last_send_package_notification'] = "Zeitpuntk letzte Package-Nachricht";
$_lang['setting_updater.last_send_package_notification_desc'] = "<strong>NICHT ÄNDERN!</strong> Einstellung wird automatisch vom System verwaltet.";

$_lang['setting_updater.last_send_version_digest'] = "Zeitpunkt letzte Zusammenfassung";
$_lang['setting_updater.last_send_version_digest_desc'] = "<strong>NICHT ÄNDERN!</strong> Einstellung wird automatisch vom System verwaltet.";

$_lang['setting_updater.last_send_core'] = "Zuletzt gesendete Core-Daten";
$_lang['setting_updater.last_send_core_desc'] = "<strong>NICHT ÄNDERN!</strong> Einstellung wird automatisch vom System verwaltet.";

$_lang['setting_updater.last_send_packages'] = "Zuletzt gesendete Package-Daten";
$_lang['setting_updater.last_send_packages_desc'] = "<strong>NICHT ÄNDERN!</strong> Einstellung wird automatisch vom System verwaltet.";


/*
$_lang['setting_updater.'] = "";
$_lang['setting_updater._desc'] = "";
*/



/*
$_lang['setting_updater.'] = "";
$_lang['setting_updater._desc'] = "";
*/
