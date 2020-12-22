<?php
/**
 * Nextcloud - Nextpad
 *
 * This file is licensed under the Affero General Public License
 * version 3 or later. See the COPYING file.
 *
 * @author Olivier Tétard <olivier.tetard@miskin.fr>
 * @copyright Olivier Tétard <olivier.tetard@miskin.fr>, 2017
 */

namespace OCA\Nextpad\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings {

    /** @var string */
    protected $appName;

    /** @var IConfig */
    protected $config;

    /**
     * @param string $appName
     * @param IConfig $config
     */
    public function __construct($appName, IConfig $config) {
        $this->appName = $appName;
        $this->config = $config;
    }

    /**
     * @return TemplateResponse
     */
    public function getForm() {
        $nextpad_mimetype_ep_configured = "no";
        $nextpad_mimetype_ec_configured = "no";

        if(\OC::$server->getMimeTypeDetector()->detectPath("test.pad") === 'application/x-nextpad') {
            $nextpad_mimetype_ep_configured = "yes";
        }

        if(\OC::$server->getMimeTypeDetector()->detectPath("test.calc") === 'application/x-nextpad') {
            $nextpad_mimetype_ec_configured = "yes";
        }

        return new TemplateResponse($this->appName, 'settings', [
            'nextpad_etherpad_enable' => $this->config->getAppValue('nextpad', 'nextpad_etherpad_enable', 'no'),
            'nextpad_etherpad_host' => $this->config->getAppValue('nextpad', 'nextpad_etherpad_host', ''),
            'nextpad_etherpad_useapi' => $this->config->getAppValue('nextpad', 'nextpad_etherpad_useapi', 'no'),
            'nextpad_etherpad_public_enable' => $this->config->getAppValue('nextpad', 'nextpad_etherpad_public_enable', 'no'),
            'nextpad_etherpad_apikey' => $this->config->getAppValue('nextpad', 'nextpad_etherpad_apikey', ''),
            'nextpad_etherpad_cookie_domain' => $this->config->getAppValue('nextpad', 'nextpad_etherpad_cookie_domain', ''),
            'nextpad_ethercalc_enable' => $this->config->getAppValue('nextpad', 'nextpad_ethercalc_enable', 'no'),
            'nextpad_ethercalc_host' => $this->config->getAppValue('nextpad', 'nextpad_ethercalc_host', ''),
            'nextpad_mimetype_ep_configured' => $nextpad_mimetype_ep_configured,
            'nextpad_mimetype_ec_configured' => $nextpad_mimetype_ec_configured,
        ], 'blank');
    }


    /**
     * @return string the section ID, e.g. 'sharing'
     */
    public function getSection() {
        return 'additional';
    }

    /**
     * @return int whether the form should be rather on the top or bottom of
     * the admin section. The forms are arranged in ascending order of the
     * priority values. It is required to return a value between 0 and 100.
     *
     * E.g.: 70
     */
    public function getPriority() {
        return 30;
    }
}
