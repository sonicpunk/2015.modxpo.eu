<?php
/**
 * User: inreti (updater@inreti.de)
 * Date: 17.02.15
 * Time: 13:16
 */

include_once "updater.tools.php";


class Updater {
    private $currentCoreVersion = "";

    private $downloadUrls = array (
        "traditional" => "http://modx.com/download/direct/",
        "git" => "https://api.github.com/repos/modxcms/revolution/",
        "advanced" => "",
        "tags" => "https://api.github.com/repos/modxcms/revolution/tags",
        "changelog" => "https://github.com/modxcms/revolution/blob/master/core/docs/changelog.txt",
    );

    private $outputCSS = "";
    private $outputHtmlHeader = "";
    private $outputInnerChunk = "";

    private $latestCoreVersion = "";
    private $latestCoreVersionSignature = "";
    private $latestCoreVersionExists;
    private $packagesResponse;
    private $packagesCounterInstallable;
    private $packagesCounterUpdateable;
    private $packagesUpdates;
    private $packagesAll;

    /* create a separate cache partition for our updater */
    private $cacheOptions = array( xPDO::OPT_CACHE_KEY => 'updater' );
    private $cacheExpires = 86400;       
    private $githubTimeout = 1500;       
    private $modxcomTimeout = 1000;       

    /**
     * @param modX $modx
     */
    function __construct( modX &$modx ) {
        $this->modx =&$modx;

        $this->modx->getService('lexicon','modLexicon');
        $this->modx->lexicon->load('widget:updater');

        $this->outputCSS = file_get_contents(MODX_ASSETS_PATH.'/components/updater/css/updater.widget.css');
        $this->outputInnerChunk = file_get_contents(MODX_CORE_PATH.'/components/updater/elements/widgets/updater.widget.container.tpl');

        $this->outputHtmlHeader = "<style type='text/css'>".$this->outputCSS."</style>";
        $this->currentCoreVersionSignature = $this->modx->getOption('settings_version');
        $this->currentCoreVersion = 'v'.$this->currentCoreVersionSignature;

        $this->cacheExpires = $this->modx->getOption('updater.cache_expires_core', null, $this->cacheExpires);
        /* do not accept values less than one day to save api calls to github. Add random amount 
         * to distribute api queries */
        if ($this->cacheExpires < 86400 && !$this->modx->getOption('updater.debug', null, false) ) {
            $this->cacheExpires = 86400 + rand(0,2000);
        }
        
        $this->githubTimeout = $this->modx->getOption('updater.github_timeout', null, $this->githubTimeout);
        $this->modxcomTimeout = $this->modx->getOption('updater.github_timeout', null, $this->modxcomTimeout);

        //$this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] refreshing cache after ".$this->cacheExpires." seconds.");

        $this->modx->getService('lexicon','modLexicon');
        $this->modx->lexicon->load('widget:updater');


        if ($this->refreshCoreVersion()) {
            $this->checkCoreDownload();
        } else {
            /* core update check failed */
        }
        if ($this->refreshPackageUpdates()) {
            $this->refreshPackageVersions();
        }
    }

    /** checks package updates
     */
    private function refreshPackageUpdates() {
        $packageCacheExpires = $this->modx->getOption('auto_check_pkg_updates_cache_expire',null,86400);
        
        $cachedPackagesResponse = $this->modx->cacheManager->get('packageResponse', $this->cacheOptions);
        if (isset($cachedPackagesResponse)) {
            $this->packagesResponse = $cachedPackagesResponse;
         //   $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] using cached packages response.");
           
        } else {
            /* save and set autoupdate package to true to force fresh updates */

            $setting_autoCheckPkgUpdates = $this->modx->getOption('auto_check_pkg_updates', null, false);
            // $setting_autoCheckPkgUpdatesCacheExpires = $this->modx->getOption('auto_check_pkg_updates_cache_expire', 15, false);
            $this->modx->setOption('auto_check_pkg_updates', true);

            $scriptProperties = array("limit" => 999);   /* TODO: is it possible to NOT limit the results? -1? */
            $response = $this->modx->runProcessor('workspace/packages/getlist', $scriptProperties);

            /* restore original settings */
            $this->modx->setOption('auto_check_pkg_updates', $setting_autoCheckPkgUpdates);

            if ($response->isError()) {
             //   $this->modx->log(modX::LOG_LEVEL_ERROR, "[Updater] error retrieving list of packages.");
                $this->packagesResponse = null;
                $this->modx->cacheManager->set( 'packageResponse', $this->packagesResponse, $packageCacheExpires, $this->cacheOptions);
                return false;
            } else {
                $this->packagesResponse = json_decode($response->getResponse(), true);
                $this->modx->cacheManager->set( 'packageResponse', $this->packagesResponse, $packageCacheExpires, $this->cacheOptions);

                return true;
            }
        }
        return true;
    }

    /**
     * Refresh package versions of updateable packages
     * this is not necessary to check if there are updates at all, but we need to run this to find the version signatures
     * of possible updates and - in the near future - to find out if a package includes a security fix.
     */
    private function refreshPackageVersions() {
        $cachedPackagesUpdates = $this->modx->cacheManager->get('packagesUpdates', $this->cacheOptions);

        if (isset($cachedPackagesUpdates)) {
            $this->packagesUpdates = $cachedPackagesUpdates;
            $this->packagesAll = $this->modx->cacheManager->get('packagesAll', $this->cacheOptions);
            $this->packagesCounterInstallable = $this->modx->cacheManager->get('packagesCounterInstallable', $this->cacheOptions);
            $this->packagesCounterUpdateable = $this->modx->cacheManager->get('packagesCounterUpdateable', $this->cacheOptions);
            //$this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] using cached packages updates. Counter = ".$this->packagesCounterInstallable);
        } else {
            $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] no cached package updates. refreshing...");
            /* check the results */
            if (!is_null($this->packagesResponse)) {
                $res = $this->packagesResponse;
                $results = $res['results'];
                $packageCacheExpires = 60 * $this->modx->getOption('auto_check_pkg_updates_cache_expire',null,86400);

                $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] * package auto cache ".$packageCacheExpires." seconds.");


                $this->modx->cacheManager->set( 'packagesAll', $results, $packageCacheExpires, $this->cacheOptions);
                    
                if (sizeof($results) > 0) {
                    $updates = array(); //hold the possible download candidates
                    $installsCounter = 0;
                    foreach ($res['results'] as $p) {
                    //    $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] * package: ".$p['name']." u: ".$p['updateable']);
        
                        if ($p['updateable'] === true) {
                            $updateResponse = $this->modx->runProcessor('workspace/packages/update-remote', array('signature' => $p['signature']));
                            $updateObject = $updateResponse->getObject();
                        //    $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] updateResponse: ".json_encode($updateResponse->getObject()));
        
                            if (!is_null($updateObject) && sizeof($updateObject) > 0) {
                                $updateObject[0]['installed'] = $p['signature'];
                                $updateObject[0]['name'] = $p['name'];
                                $updates[$p['signature']] = $updateObject;
                            } else {
                                $updates[$p['signature']] = "";
                            }
                        }
                        if ($p['installed'] == "") {
                            $installsCounter++;
                        }
                    }

                    $updatesCounter = sizeof($updates);

                    $this->packagesCounterInstallable = $installsCounter;
                    $this->packagesCounterUpdateable = $updatesCounter;
                    $this->packagesUpdates = $updates;
                    $this->packagesAll = $results;

                    $this->modx->cacheManager->set( 'packagesAll', $results, $packageCacheExpires, $this->cacheOptions);
                    $this->modx->cacheManager->set( 'packagesUpdates', $updates, $packageCacheExpires, $this->cacheOptions);
                    $this->modx->cacheManager->set( 'packagesCounterInstallable', $installsCounter, $packageCacheExpires, $this->cacheOptions);
                    $this->modx->cacheManager->set( 'packagesCounterUpdateable', $updatesCounter, $packageCacheExpires, $this->cacheOptions);

                } else {
                    $this->packagesCounterInstallable = 0;
                    $this->packagesCounterUpdateable = 0;
                    $this->packagesUpdates = null;
                    $this->packagesAll = null;
                }
                
            /*    $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] total number of packages: ".sizeof($this->packagesAll));
                $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] total number of installable packages: ".$this->packagesCounterInstallable);
                $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] total number of updateable packages: ".$this->packagesCounterUpdateable);
             */
                
            }
        }
    }



    /** checks if the detected core download can be downloaded at modx.com
     * @param $type - the type of core zipball in question. can be advanced or traditional.
     * @todo check the url for sdk downloads
     * @return boolean - true if the download zipball exists
     */
    protected function checkCoreDownload( $type='traditional' ) {

        $cachedCoreDownloadable = $this->modx->cacheManager->get('latestCoreVersionDownloadable-'.$type, $this->cacheOptions);
        if (isset($cachedCoreDownloadable)) {
            $this->latestCoreVersionExists = $cachedCoreDownloadable;
            //$this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] using cached core download check.");
        } else {
            $this->latestCoreVersionExists = $this->remote_file_exists($this->constructCoreDownloadUrl($type));
            $this->modx->cacheManager->set( 'latestCoreVersionDownloadable-'.$type, $this->latestCoreVersionExists, $this->cacheExpires, $this->cacheOptions);
        }
        return $this->latestCoreVersionExists;
    }
    public function isCoreDownloadable() {
        return $this->latestCoreVersionExists;
    }
    public function constructCoreDownloadUrl($type='traditional' ) {
        return $this->downloadUrls[$type]."modx-".$this->latestCoreVersionSignature.".zip";
    }

    /**
     * @param $url
     * @return bool
     */
    private function remote_file_exists($url){
        $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] checking zipball at: ".$url);

        $curl = curl_init($url);
        //don't fetch the actual page, you only want to check the connection is ok
        //$this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] curl init");
        
        curl_setopt($curl, CURLOPT_NOBODY, true);
        
        // only do a very quick check here
        // TODO: remove to processor
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, $this->modxcomTimeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, 2);

        //do request
        //$this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] curl do exec");
        $result = curl_exec($curl);
        $ret = false;
        //if request did not fail
        if ($result !== false) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            if ($statusCode == 200 || $statusCode == 301 || $statusCode == 302) {
                $ret = true;
            } else {
          //      $this->modx->log(modX::LOG_LEVEL_WARN, "[Updater] checking zipball statusCode: ".$statusCode);
            }
        } else {
           // $this->modx->log(modX::LOG_LEVEL_ERROR, "[Updater] checking zipball request failed. Timeout.");
        }

        curl_close($curl);
        return $ret;
    }

    /**
     * Refreshes information about core version from github tags, if cache expired
     * @TODO: cache the results
     * @return boolean - true if version could be refreshed
     */
    protected function refreshCoreVersion() {
        $debug = $this->modx->getOption('updater.debug', null, '');

        $cachedVersion = $this->modx->cacheManager->get('latestCoreVersion', $this->cacheOptions);
        if (isset($cachedVersion)) {
        //    $this->modx->log(modX::LOG_LEVEL_INFO, "[Updater] using latest core version from cache: ".$cachedVersion);
            $this->setLatestCoreVersion($cachedVersion);
            return true;
        } else {
            $this->modx->log(modX::LOG_LEVEL_INFO, "[Updater] checking latest core version at github.");
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->downloadUrls['tags']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, "revolution");
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->githubTimeout);
            $content = curl_exec($ch);

            if (!curl_errno($ch)) {
                $versionArr = json_decode($content);
                curl_close($ch);

                /* check if the github api returned a message or returned the tags
                if (array_key_exists('message', $versionArr)) {
                }

                $versionTags = array();
                foreach ($versionArr as $v) {
                    $versionTags[] = $v->name;
                }

                $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] version tags on github: ".json_encode($versionTags));
                $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] version tags on github: ".json_encode($versionArr));

                /* check if the latest version on github differs from current Version */
                // TODO: this won't work with github checkouts!!! Need a more sophisticated comparison

                /* let's regex the name */

                $v = $versionArr[0]->name;
                if ($debug) $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] version patch: ".$this->versionSlice($v,'patch'));

                if ($versionArr[0]->name != $this->currentCoreVersion) {
                    //$versionsC = count($versionArr);
                    $latest = $versionArr[0]->name;
                    $this->setLatestCoreVersion($latest);
                //    $this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] latest version on github: ".$latest);
            
                    $this->modx->cacheManager->set( 'latestCoreVersion', $latest, $this->cacheExpires, $this->cacheOptions);
            
                } else {
                    $this->setLatestCoreVersion($versionArr[0]->name);
                    $latest = $versionArr[0]->name;
                    $this->modx->cacheManager->set( 'latestCoreVersion', $latest, $this->cacheExpires, $this->cacheOptions);
                }
                return true;
            } else {
                curl_close($ch);
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[Updater] cannot retrieve latest version from github tags.");
                $this->setLatestCoreVersion(null);
                $this->modx->cacheManager->set( 'latestCoreVersion', $latest, $this->cacheExpires, $this->cacheOptions);
                return false;
            }
        }
    }
    private function versionSlice($version, $part) {
        $matches = array();
        preg_match_all( '/(?<prefix>v)?(?<major>\d*)\.(?<minor>\d*)\.(?<patch>\d*)\-(?<release>[\w\d]*)/s', $version, $matches,PREG_SET_ORDER);
        $v = $matches[0];
        //$this->modx->log(modX::LOG_LEVEL_DEBUG, "[Updater] found: ".$v['major'].",".$v['minor'].",".$v['patch'].",".$v['release']);
        if ($part == "") {
            return array($v['major'],$v['minor'],$v['patch'],$v['release']);
        }
        return $v[$part];
    }

    public function getLatestCoreVersion() {
        return $this->latestCoreVersion;
    }
    private function setLatestCoreVersion( $v ) {
        $this->latestCoreVersion = $v;
        /* also set the signature */
        $this->latestCoreVersionSignature = substr($v,1);
        return;
    }
    public function getCurrentCoreVersion() {
        return $this->currentCoreVersion;
    }
    public function isCoreUpdateable() {
        return (!is_null($this->latestCoreVersion) && $this->currentCoreVersion !== $this->latestCoreVersion);
    }

    /**
     * @return string - HTML output for the widget
     */
    public function generateWidget() {
        $output = $this->outputHtmlHeader;
        $output .= "<div class='updater-container'>";

        /* make a dry run to initialize lexicons? */
        /* this seems to be necessary to initialize the lexicon... must be a BUG */
        $void = $this->outputInnerChunk;
        $this->modx->getParser()->processElementTags('', $void, true);

        $output .= $this->generateWidgetCore();
        $output .= $this->generateWidgetPackages();

        $output .= "</div>";

        return $output;
    }
    private function generateWidgetPackages() {
        $importance = "";
        $message = "";

        if ($this->packagesCounterUpdateable > 0) {
            $title = $this->modx->lexicon('package_update_title.multi');
            switch ($this->packagesCounterUpdateable) {
                case 1:
                    $title = $this->modx->lexicon('package_update_title.single');
                    $message = implode(", ", array_keys($this->packagesUpdates));
                    break;
                case 2:
                    $message = implode(", ", array_keys($this->packagesUpdates));
                    break;
                default:
                    $message = $this->modx->lexicon('package_update_msg_default', array("count"=>$this->packagesCounterUpdateable));
            }

            if ($this->packagesCounterInstallable > 0) {
                $message .= "<br/>" . $this->packagesCounterInstallable . $this->modx->lexicon('package_install_awaiting');
            }
            $tooltip = $this->modx->lexicon('package_update_tooltip');

        } else {
            if ($this->packagesCounterInstallable > 0) {
                $title = $this->modx->lexicon('package_install_title');
                $message .= $this->modx->lexicon('package_install_msg.'.(($this->packagesCounterInstallable>1)?'multi':'single'),
                    array(
                        'count'     => $this->packagesCounterInstallable, //sizeof($this->packagesResponse['results'])
                    )
                );
                $tooltip = $this->modx->lexicon('package_install_tooltip');
            } else {
                $title = $this->modx->lexicon('package_uptodate_title');
                $message = $this->modx->lexicon('package_uptodate_msg.'.((sizeof($this->packagesResponse['results'])>1)?'multi':'single'),
                    array('count' => sizeof($this->packagesResponse['results']))
                );
                $tooltip = $this->modx->lexicon('package_uptodate_tooltip'); //"All your packages are up to date.";
            }
        }

        $state = "";
        if ($this->packagesCounterUpdateable==0 && $this->packagesCounterInstallable==0) {
            $state = "up-to-date";
        } else if ($this->packagesCounterUpdateable!=0) {
            $state = "updateable";
        }
        
        $placeholders = array(
            "updater.area" => $this->modx->lexicon('package_area'),
            "updater.title" => $title,
            "updater.tooltip" => $tooltip,
            "updater.current" => "",
            "updater.icon" => 'cubes',
            "updater.url" => '?a=workspaces',
            "updater.state" => $state,
            "updater.notes" => '',
            "updater.message" => $message,
            "updater.buttontext" => ($state!="up-to-date") ? "Installer":"",
            "updater.isImportant" => $importance,
        );

       //return $this->modx->getChunk( 'updater.widget.tpl', $placeholders );
        $this->modx->setPlaceholders( $placeholders );

        $chunk = $this->outputInnerChunk;
        $this->modx->getParser()->processElementTags('', $chunk, true);
        return $chunk;

    }
    private function generateWidgetCore() {
        //$this->modx->log(modX::LOG_LEVEL_INFO, "[Updater] core version: ".$this->getLatestCoreVersion());
            
        if (!is_null($this->getLatestCoreVersion())) {

            $placeholders = array(
                "updater.area" => $this->modx->lexicon('core_area'),
                "updater.current" => $this->getCurrentCoreVersion(),
                "updater.update" => $this->getLatestCoreVersion(),
                "updater.icon" => 'gears',
                "updater.url" => $this->constructCoreDownloadUrl(),
                "updater.message" => "",
                "updater.state" => ($this->isCoreUpdateable())? 'updateable':'up-to-date',
                "updater.notes" => 'https://github.com/modxcms/revolution/blob/master/core/docs/changelog.txt',
            );
            
            if ($this->isCoreUpdateable()) {
                $placeholders = array_merge($placeholders, array(
                    "updater.title" => $this->modx->lexicon('core_update_title'),
                    "updater.tooltip" => $this->modx->lexicon('core_update_tooltip'),
                    "updater.buttontext" => "",
                    "updater.isImportant" => 'important'
                    ));
            } else {
                    $placeholders = array_merge($placeholders, array(
                        "updater.title" => $this->modx->lexicon('core_uptodate_title'),
                        "updater.message" => $this->modx->lexicon('core_uptodate_msg', array( 'version' => $this->getCurrentCoreVersion() ) ),
                        "updater.buttontext" => "",
                        "updater.isImportant" => ""
                    ));
            }
        } else {
        //    $this->modx->log(modX::LOG_LEVEL_INFO, "[Updater] show update error on widget");
            $placeholders = array(
                "updater.area" => $this->modx->lexicon('core_area'),
                "updater.current" => $this->getCurrentCoreVersion(),
                "updater.update" => "",
                "updater.icon" => 'gears',
                "updater.url" => "",
                "updater.tooltip" => $this->modx->lexicon('github_error_tooltip'),
                "updater.title" => $this->modx->lexicon('core_error_title'),
                "updater.message" => $this->modx->lexicon('core_error_msg', array('version' => $this->getCurrentCoreVersion() )),
                "updater.state" => 'error',
                "updater.buttontext" => "",
                "updater.notes" => 'https://github.com/modxcms/revolution/blob/master/core/docs/changelog.txt',
            );
        }

        $this->modx->setPlaceholders( $placeholders );
        $chunk = $this->outputInnerChunk;
        $this->modx->getParser()->processElementTags('', $chunk, true);
        return $chunk;
    }

    /*
     * returns packages
     */
    public function getPackagesList() {
        return $this->packagesAll;
    }
    public function getPackagesUpdateList() {
        return $this->packagesUpdates;
    }

}
