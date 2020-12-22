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

namespace OCA\Nextpad\Controller;

use \OCP\IRequest;
use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Http;

use OCA\Nextpad\Service\NextpadService;
use OCA\Nextpad\Service\NextpadException;

class AjaxController extends Controller {

    /** @var NextpadService */
    private $service;

    public function __construct($appName, IRequest $request, NextpadService $service) {
        parent::__construct($appName, $request);
        $this->service = $service;
    }

    /**
     * @NoAdminRequired
     */
    public function getconfig() {
        $config = [];

        $appConfig = \OC::$server->getConfig();
        $config['nextpad_etherpad_enable'] = $appConfig->getAppValue('nextpad', 'nextpad_etherpad_enable', 'no');
        $config['nextpad_etherpad_public_enable'] = $appConfig->getAppValue('nextpad', 'nextpad_etherpad_public_enable', 'no');
        $config['nextpad_etherpad_useapi'] = $appConfig->getAppValue('nextpad', 'nextpad_etherpad_useapi', 'no');
        $config['nextpad_ethercalc_enable'] = $appConfig->getAppValue('nextpad', 'nextpad_ethercalc_enable', 'no');

        return new JSONResponse(["data" => $config]);
    }

    /**
     * @NoAdminRequired
     */
    public function newpad($dir, $padname, $type, $protected) {
        $dir = isset($dir) ? '/'.trim($dir, '/\\') : '';
        $padname = isset($padname) ? trim($padname, '/\\') : '';
        $type = isset($type) ? trim($type, '/\\') : '';
        $protected = isset($protected) && $protected === 'true' ? true : false;

        try {
            $data = $this->service->create($dir, $padname, $type, $protected);
            return new JSONResponse([
                'data' => $data,
                'status' => 'success',
            ]);
        }
        catch(NextpadException $e) {
            $message = [
                'data' => ['message' => $e->getMessage()],
                'status' => 'error',
            ];
            return new JSONResponse($message, Http::STATUS_NOT_FOUND);
        }
    }
}
