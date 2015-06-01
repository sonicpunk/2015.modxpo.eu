<?php
/* set the keyed system setting to current time */
if (!function_exists('resetTimedSetting')) {
    function resetTimedSetting($key, &$modx) {
        $digestSetting = $modx->getObject('modSystemSetting', $key);
        $digestSetting->set('value', strtotime('now'));
        $digestSetting->save();
        $cacheRefreshOptions = array('system_settings' => array());
        $modx->cacheManager->refresh($cacheRefreshOptions);
        //$modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] set time key '" . $key . "': " . $modx->getOption($key, null, '???'));
        return;
    }
}
/* set the keyed system setting to current time */
if (!function_exists('resetLastSendSetting')) {
    function resetLastSendSetting($key, $value, &$modx) {
        $digestSetting = $modx->getObject('modSystemSetting', $key);
        $digestSetting->set('value', $value);
        $digestSetting->save();
        $cacheRefreshOptions = array('system_settings' => array());
        $modx->cacheManager->refresh($cacheRefreshOptions);
        //$modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] set lastsend key '" . $key . "': " . $modx->getOption($key, null, '???'));
        return;
    }
}

$eventName = $modx->event->name;
switch($eventName) {
    case 'OnWebPageInit':
        $debug = $modx->getOption('updater.debug', null, '');

        //if ($debug) $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] Updater request called");

        /* this event works as a semi-functional cron simulator */
        /* it is fired for any web or manager request */
        /* now we need the most restrictive and quickest filter first */

        /* Send core notifications */
        if ($modx->getOption('updater.send_core_notifications',null,false)==="1") {

            $timestamp = $modx->getOption('updater.last_send_core_notification', null, '');

            $last_sendcore_json = $modx->getOption('updater.last_send_core', null, '');
            if ($debug) $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] notify min every : " . $modx->getOption('updater.send_notification_hours', null, 24) . " hours");

            if (isset($timestamp) && !is_null($timestamp)) {
                $elapsedHours = ((strtotime('now') - $timestamp) / 3600);
                if ($debug) $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] elapsed hours since last core send check: " . round($elapsedHours, 2));

                /* check if time is over */
                if ($elapsedHours >= $modx->getOption('updater.send_notification_hours', null, 24)) {
                    resetTimedSetting('updater.last_send_core_notification', $modx);

                    if (!$modx->loadClass('Updater', MODX_CORE_PATH . 'components/updater/model/', true, true)) {
                        return;
                    }
                    $updater = new Updater($modx, array());

                    /* check if the core is updateable */
                    if ($updater->isCoreUpdateable()) {

                        $last_send_core = $modx->getOption('updater.last_send_core', null, $updater->getCurrentCoreVersion());
                        $new_core = $updater->getLatestCoreVersion();

                        if ( isset($last_send_core) && ($last_send_core==="" || $last_send_core!==$new_core)) {
                            resetLastSendSetting('updater.last_send_core', $new_core, $modx);

                            if ($modx->getOption('updater.mail_format_html', null, false) === "1") {
                                $chunk = file_get_contents(MODX_CORE_PATH . 'components/updater/elements/tpl/notification_mail_coreinfo_html.tpl');
                            } else {
                                $chunk = file_get_contents(MODX_CORE_PATH . 'components/updater/elements/tpl/notification_mail_coreinfo.tpl');
                            }

                            //$logodata = file_get_contents(MODX_MANAGER_PATH . 'templates/default/images/modx-icon-color.svg');
                            $logodata = file_get_contents(MODX_ASSETS_PATH."components/updater/img/modx-icon-color.svg");
                            $placeholders = array(
                                'logodata' => base64_encode($logodata),
                                'core_update' => $new_core,
                                'core_current' => $updater->getCurrentCoreVersion(),
                                'changelog_url' => "https://github.com/modxcms/revolution/blob/master/core/docs/changelog.txt"
                            );
                            if ($debug) $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] " . print_r($updater->getLatestCoreVersion(), true));

                            $modx->setPlaceholders($placeholders);
                            $modx->getParser()->processElementTags('', $chunk, true);

                            $subject = "Core update available for MODX site '" . $modx->getOption('site_name') . "'";
                            $from = "modx_noreply@" . $_SERVER['SERVER_NAME'];
                            $fromName = "MODX System Notifier (Core)";

                            $modx->getService('mail', 'mail.modPHPMailer');
                            $modx->mail->set(modMail::MAIL_BODY, $chunk);
                            $modx->mail->set(modMail::MAIL_FROM, $from);
                            $modx->mail->set(modMail::MAIL_FROM_NAME, $fromName);
                            $modx->mail->set(modMail::MAIL_SUBJECT, $subject);

                            // TODO: walk through users here
                            $modx->mail->address('to', $modx->getOption('updater.core_notifications_mail'));
                            $modx->mail->setHTML($modx->getOption('updater.mail_format_html', null, false) === "1");

                            if (!$modx->mail->send()) {
                                $modx->log(modX::LOG_LEVEL_ERROR, '[updater-notifier] An error occurred while trying to send the email: ' . $modx->mail->mailer->ErrorInfo);
                            } else {
                                $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] Successfully send core data to " . $modx->getOption('updater.core_notifications_mail'));
                            }

                            $modx->mail->reset();
                        } else {
                            $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] do not send new core data.");
                        }
                    }
                }
            }
        }

        /* Send package notifications */
        if ($modx->getOption('updater.send_package_notifications',null,false)==="1") {

            $timestamp = $modx->getOption('updater.last_send_package_notification', null, '');

            $last_sendpackages_json = $modx->getOption('updater.last_send_packages', null, '');
            if ($debug) $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] notify min every : ".$modx->getOption('updater.send_notification_hours',null,24)." hours");

            if (isset($timestamp) && !is_null($timestamp)) {
                $elapsedHours = ((strtotime('now') - $timestamp) / 3600);
                if ($debug) $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] elapsed hours since last check: ".round($elapsedHours,2));

                /* check if time is over */
                if ($elapsedHours >= $modx->getOption('updater.send_notification_hours',null,24)) {
                    $time = microtime(true);

                    if (!$modx->loadClass('Updater', MODX_CORE_PATH . 'components/updater/model/', true, true)) {
                        return;
                    }
                    $updater = new Updater($modx, array());

                    /*
                        until now this strange reformatting is necessary because package controller
                        returns nonsense and we did not touch it in updater class
                    */
                    $package_names_update = array();
                    $tmp = $updater->getPackagesUpdateList();
                    if (isset($tmp) && !is_null($tmp)) {
                        $packages_names_update  = array_map(
                            function($v) {
                                return ($v[0]['name']." > ".$v[0]['signature']);
                            }, $tmp
                        );
                    } else {
                        return;
                    }

                    $packages_names_install = array();
                    $tmp = $updater->getPackagesList();
                    if (isset($tmp) && !is_null($tmp)) {
                        $packages_names_install = array_map(
                            function ($v) {
                                return ($v['package_name'] . ": " . $v['signature']);
                            },
                            array_filter(
                                $tmp,
                                function ($v) {
                                    return $v['installed'] == "";
                                }
                            )
                        );
                    }

                    if (sizeof($packages_names_install)+sizeof($packages_names_update) > 0) {
                        if ($debug) $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] there are updates to notify!");
                        resetTimedSetting('updater.last_send_package_notification', $modx);

                        $store_send_packages = array_merge(
                            array_map(
                                function($v) {
                                    return array( 'name' => $v[0]['name'], 'installed'=>$v[0]['installed'], 'update' => $v[0]['signature']);
                                },
                                $updater->getPackagesUpdateList()
                            ),
                            array_map(
                                function($v) {
                                    return array( 'name' => $v['name'], 'installed'=>'previous', 'update' => $v['signature']);
                                },
                                array_filter($updater->getPackagesList(), function($v) {  return $v['installed']==""; })
                            )
                        );
                        if ($debug)  $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] data: ".json_encode($store_send_packages));
                        if ($debug)  $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] packages_names_update: ".json_encode($packages_names_update));
                        if ($debug)  $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] packages_names_install: ".json_encode($packages_names_install));

                        $logodata = file_get_contents(MODX_ASSETS_PATH."components/updater/img/modx-icon-color.svg");

                        $placeholders = array(
                            'logodata' => base64_encode($logodata),
                            'logoformat' => $logoformat,
                            'updater.packages_names_install' => implode(", ",$packages_names_install),
                            'updater.packages_names_update' => implode(", ",$packages_names_update),
                        );

                        $store_send_packages_json = json_encode(array_values($store_send_packages));

                        /* only send mail if the packages list differs from last send packages list */
                        if ($store_send_packages_json !== $last_sendpackages_json || $debug) {
                            resetLastSendSetting('updater.last_send_packages', $store_send_packages_json, $modx);

                            if ($modx->getOption('updater.mail_format_html',null,false)==="1") {
                                $chunk = file_get_contents(MODX_CORE_PATH . 'components/updater/elements/tpl/notification_mail_packageinfo_html.tpl');
                            } else {
                                $chunk = file_get_contents(MODX_CORE_PATH . 'components/updater/elements/tpl/notification_mail_packageinfo.tpl');
                            }

                            $modx->setPlaceholders($placeholders);
                            $modx->getParser()->processElementTags('', $chunk, true);

                            $subject = "Package update notification for MODX site '" . $modx->getOption('site_name') . "'";
                            $from = "modx_noreply@" . $_SERVER['SERVER_NAME'];
                            $fromName = "MODX System Notifier (Packages)";

                            $modx->getService('mail', 'mail.modPHPMailer');
                            $modx->mail->set(modMail::MAIL_BODY, $chunk);
                            $modx->mail->set(modMail::MAIL_FROM, $from);
                            $modx->mail->set(modMail::MAIL_FROM_NAME, $fromName);
                            $modx->mail->set(modMail::MAIL_SUBJECT, $subject);
                            // TODO: walk through users here
                            $modx->mail->address('to', $modx->getOption('updater.package_notifications_mail'));
                            $modx->mail->setHTML($modx->getOption('updater.mail_format_html',null,false)==="1");

                            if (!$modx->mail->send()) {
                                $modx->log(modX::LOG_LEVEL_ERROR, '[updater-notifier] An error occurred while trying to send the email: ' . $modx->mail->mailer->ErrorInfo);
                            } else {
                                $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] Successfully send package data to ".$modx->getOption('updater.package_notifications_mail'));
                            }

                            $modx->mail->reset();


                        } else {
                            //$modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] There are updates available, but this notification was already send: ".$store_send_packages_json);
                        }

                    }
                }
            } else {
                resetTimedSetting('updater.last_send_package_notification', $modx);
            }
        }

        /* Send digest */
        if ($modx->getOption('updater.send_version_digest_user',null,false)==="1") {
            $timestamp = $modx->getOption('updater.last_send_version_digest', null, '');
            if (isset($timestamp) && !is_null($timestamp)) {
                $elapsedHours = ((strtotime('now') - $timestamp) / 3600);
                //$modx->log(modX::LOG_LEVEL_INFO, "[updater-notifier] time elapsed: " . $elapsedHours . " h");

                /* check if digest is over */
                if ($elapsedHours > $modx->getOption('updater.send_version_digest_hours')) {
                    // reset the timestamp here to prevent multiple mails
                    resetTimedSetting('updater.last_send_version_digest', $modx);

                    if ($debug) $modx->log(modX::LOG_LEVEL_DEBUG, "[updater-notifier] SEND DIGEST.");

                    if (!$modx->loadClass('Updater', MODX_CORE_PATH . 'components/updater/model/', true, true)) {
                        return;
                    }
                    $updater = new Updater($modx, array());

                    $packageList = array();
                    $packageList = $updater->getPackagesList();

                    $coreMessage = "You are up to date with version " . $updater->getCurrentCoreVersion();
                    $coreMessageHTML = "<div style='color:green'>" . $coreMessage . "</div>";
                    if ($updater->isCoreUpdateable()) {
                        $coreMessage = "An UPDATE to version " . $updater->getLatestCoreVersion() . " is available!";
                        $coreMessageHTML = "<div style='color:red'>" . $coreMessage . "</div>";
                        $coreMessage = "*** " . $coreMessage . " ***";
                    }

                    $packages_list = str_pad('Package', 32) . " | " . str_pad('Version signature', 40) . " | " . "update?\n";
                    $packages_list .= str_pad("", strlen($packages_list), '-') . "\n";
                    $updates_list = $updater->getPackagesUpdateList();
                    $updates = 0;
                    $installed = 0;
                    foreach ($updater->getPackagesList() as $p) {
                        $packages_list .= str_pad($p['name'], 32) . " | " . str_pad($p['signature'], 40);
                        if ($p['updateable'] === true) {
                            $packages_list .= " | UPDATE AVAILABLE TO " . $updates_list[$p['signature']][0]['version'];

                            $updates++;
                        } else {
                            if ($p['installed'] == "") {
                                $packages_list .= " | Installation pending";
                                $installed++;
                            } else {
                                $packages_list .= " |";
                            }
                        }
                        $packages_list .= "\n";
                    }

                    $logodata = file_get_contents(MODX_ASSETS_PATH."components/updater/img/modx-icon-color.svg");
                    //$logodata = file_get_contents(MODX_MANAGER_PATH . 'templates/default/images/modx-icon-color.svg');

                    $placeholders = array(
                        'logodata' => base64_encode($logodata),
                        'core_message' => $coreMessage,
                        'packages_list' => $packages_list,
                        //'packages_updates' => json_encode($updates_list),
                        'update_core' => ($updater->getLatestCoreVersion() !== $updater->getCurrentCoreVersion()) ? $updater->getLatestCoreVersion() : '',
                    );

                    if ($updates > 0) {
                        $placeholders['packages_message'] = "" . $updates . " packages can be updated!!!\n";
                    } else {
                        $placeholders['packages_message'] = "All packages are up to date.\n";
                    }
                    if ($installed > 0) {
                        $placeholders['packages_message'] .= "" . $installed . " packages are not installed yet.";
                    }

                    // send the digest
                    //$message = $modx->getChunk('updater.mail_version_digest.tpl', $placeholders);
                    $chunk = file_get_contents(MODX_CORE_PATH . 'components/updater/elements/tpl/notification_mail_text.tpl');
                    $modx->setPlaceholders($placeholders);
                    $modx->getParser()->processElementTags('', $chunk, true);
                    $message = $chunk;

                    $subject = "System notification for MODX site '" . $modx->getOption('site_name') . "'";
                    $from = "modx_noreply@" . $_SERVER['SERVER_NAME'];
                    $fromName = "MODX System Notifier";

                    $modx->getService('mail', 'mail.modPHPMailer');
                    $modx->mail->set(modMail::MAIL_BODY, $message);
                    $modx->mail->set(modMail::MAIL_FROM, $from);
                    $modx->mail->set(modMail::MAIL_FROM_NAME, $fromName);
                    $modx->mail->set(modMail::MAIL_SUBJECT, $subject);
                    // TODO: walk through users here
                    $modx->mail->address('to', $modx->getOption('updater.core_notifications_mail'));
                    $modx->mail->setHTML(false);

                    if (!$modx->mail->send()) {
                        $modx->log(modX::LOG_LEVEL_ERROR, '[updater-notifier] An error occurred while trying to send the email: ' . $modx->mail->mailer->ErrorInfo);
                    } else {
                        if ($debug) $modx->log(modX::LOG_LEVEL_DEBUG, '[updater-notifier] Successfully send digest to '.$modx->getOption('updater.core_notifications_mail'));
                    }

                    $modx->mail->reset();
                }
            } else {
                /* no timestamp in system settings? */
                if ($debug) $modx->log(modX::LOG_LEVEL_DEBUG, '[updater-notifier] no time stamp in system settings found.');
                resetTimedSetting('updater.last_send_version_digest', $modx);
            }
        }
        break;

}
return;