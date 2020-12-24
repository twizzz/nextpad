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

namespace OCA\Nextpad\AppInfo;

use OCP\AppFramework\App;
use OCP\Util;

class Application extends App {

    public function __construct(array $urlParams = array()) {
        parent::__construct('nextpad', $urlParams);

        $container = $this->getContainer();
        $container->registerService('L10N', function($c) {
            return $c->query('ServerContainer')->getL10N($c->query('nextpad'));
        });
    }

    public function registerHooks() {
        $dispatcher = $this->getContainer()->getServer()->getEventDispatcher();

        $dispatcher->addListener(
            'OCA\Files::loadAdditionalScripts',
            function() {
                Util::addStyle('nextpad', 'nextpad');
                Util::addScript('nextpad', 'nextpad');
            });

        $dispatcher->addListener(
            'OCA\Files_Sharing::loadAdditionalScripts',
            function () {
                Util::addScript('nextpad', 'nextpad_public');
                Util::addStyle('nextpad', 'nextpad');
            });
    }
}
