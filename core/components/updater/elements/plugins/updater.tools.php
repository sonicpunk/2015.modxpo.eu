<?php
/**
 * Created by PhpStorm.
 * User: jens
 * Date: 26.02.15
 * Time: 16:27
 */


/* set the keyed system setting to current time */
function resetTimedSetting( $key, &$modx ) {
    $digestSetting = $modx->getObject( 'modSystemSetting', $key);
    $digestSetting->set('value', strtotime('now'));
    $digestSetting->save();
    $cacheRefreshOptions =  array( 'system_settings' => array() );
    $modx->cacheManager-> refresh($cacheRefreshOptions);
    $modx->log(modX::LOG_LEVEL_DEBUG, "[cron] set time key '".$key."': " . $modx->getOption($key, null, '???'));
    return;
}