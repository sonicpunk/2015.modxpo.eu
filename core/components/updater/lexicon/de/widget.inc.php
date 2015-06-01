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
 * Widget Language file for Updater
 *
 * @package updater
 * @subpackage lexicon
 */

/* ************ Common section ****************/
$_lang['release_notes'] = 'Hinweise zur Veröffentlichung';

/* ************* Packages section *************/
$_lang['package_area'] = 'Extras';

$_lang['package_update_title.single'] = 'Paket-Update zum Download verfügbar';
$_lang['package_update_title.multi'] = 'Paket-Updates zum Download verfügbar';
$_lang['package_update_msg_default'] = '[[+count]] Pakete können aktualisiert werden.';
$_lang['package_update_tooltip'] = 'Sie sollten die Pakete im Installer jetzt aktualisieren.';

$_lang['package_install_title'] = 'Pakete noch nicht installiert';
$_lang['package_install_msg.single'] = 'Ein Paket ist noch nicht installiert worden.';
$_lang['package_install_msg.multi'] = '[[+count]] Pakete wurden noch nicht installiet.';
$_lang['package_install_tooltip'] = 'Sie haben Pakete, die heruntergeladen aber noch nicht installiert sind.';
$_lang['package_install_awaiting'] = ' warten auf Installation...';

$_lang['package_uptodate_title'] = 'Pakete';
$_lang['package_uptodate_msg.single'] = 'Das installierte Paket ist aktuell.';
$_lang['package_uptodate_msg.multi'] = 'Alle [[+count]] installierten Pakete sind aktuell.';
$_lang['package_uptodate_tooltip'] = '';

$_lang['package_buttontext'] = 'Installer';

/* *********** Core section ***************/
$_lang['core_area'] = 'Core';

$_lang['core_update_title'] = 'Systemupdate verfügbar!';
$_lang['core_update_tooltip'] = 'Aktualisieren Sie Ihr System jetzt! Hinweise und Hilfe finden Sie auf modx.com.';

$_lang['core_uptodate_title'] = 'System';
$_lang['core_uptodate_msg'] = 'Installation ist aktuell ([[+version]]).';

$_lang['core_error_title'] = "Probleme beim Prüfen der Updates";
$_lang['core_error_msg'] = "Die Versionen konnten nicht auf GitHub ermittelt werden.<br/>Ihre aktuelle Version ist [[+version]].";

/* ************* error and network ***********/
$_lang['github_error_tooltip'] = "Github antwortet nicht. Passen Sie ggf. die Timeout-Einstellungen bei den Systemeinstellungen an Ihre Verbindung an.";
