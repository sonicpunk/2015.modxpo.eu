<?php

/* immediately return if the user does not have sudo rights
   or the right to perform maintenance tasks. Results in showing no widget */
if (!$modx->user->get('sudo') && !$modx->hasPermission('system_perform_maintenance_tasks')) {
    return;
}

/* try to get a fresh Updater */
if (!$modx->loadClass('Updater', MODX_CORE_PATH . 'components/updater/model/', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[Updater] Could not load  class.');
    return;
}

$updater = new Updater($modx, array());

return $updater->generateWidget();
