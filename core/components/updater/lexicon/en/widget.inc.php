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
$_lang['release_notes'] = 'Release Notes';

/* ************* Packages section *************/
$_lang['package_area'] = 'Extras';

$_lang['package_update_title.single'] = 'Package update available for download';
$_lang['package_update_title.multi'] = 'Package updates available for download';
$_lang['package_update_msg_default'] = '[[+count]] packages can be updated.';
$_lang['package_update_tooltip'] = 'You should update your packages with the installer.';

$_lang['package_install_title'] = 'Packages awaiting installation';
$_lang['package_install_msg.single'] = 'One package is not yet installed.';
$_lang['package_install_msg.multi'] = '[[+count]] packages are not yet installed.';
$_lang['package_install_tooltip'] = 'You have packages that are downloaded but not installed.';
$_lang['package_install_awaiting'] = ' awaiting installation...';

$_lang['package_uptodate_title'] = 'Packages';
$_lang['package_uptodate_msg.single'] = 'Your installed package is up to date.';
$_lang['package_uptodate_msg.multi'] = 'All [[+count]] installed packages are up to date.';
$_lang['package_uptodate_tooltip'] = '';

$_lang['package_buttontext'] = 'Installer';

/* *********** Core section ***************/
$_lang['core_area'] = 'Core';

$_lang['core_update_title'] = 'System update available!';
$_lang['core_update_tooltip'] = 'You should update your system immediately. Instructions can be found on modx.com.';

$_lang['core_uptodate_title'] = 'System';
$_lang['core_uptodate_msg'] = 'Your system is up to date ([[+version]]).';

$_lang['core_error_title'] = "Problems with update check";
$_lang['core_error_msg'] = "Can not determine latest version on github.<br/>Nevertheless, your current version is [[+version]]";

/* ************* error and network ***********/
$_lang['github_error_tooltip'] = "Github did not respond in time. Adjust the timeout system settings according to your connection.";