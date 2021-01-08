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

namespace OCA\Nextpad\Service;

use Exception;

class NextpadService {

    public function create($dir, $padname, $type, $protected) {
        // Generate a random pad name
        $token = \OC::$server->getSecureRandom()->generate(16, \OCP\Security\ISecureRandom::CHAR_LOWER.\OCP\Security\ISecureRandom::CHAR_DIGITS);

        $l10n = \OC::$server->getL10N('nextpad');
        $l10n_files = \OC::$server->getL10N('files');

        $result = ['success' => false,
                   'data' => NULL];

        if($type === "ethercalc") {
            $ext = "calc";
            $host = \OC::$server->getConfig()->getAppValue('nextpad', 'nextpad_ethercalc_host', false);

            /*
             * Prepend the calc’s name with a `=` to enable multisheet
             * support.
             *
             * More info:
             *   – https://github.com/audreyt/ethercalc/issues/138
             *   – https://github.com/otetard/ownpad/issues/26
             */
            $url = sprintf("%s/=%s", rtrim($host, "/"), $token);
        }
        elseif($type === "etherpad") {
            $padID = $token;

            $config = \OC::$server->getConfig();
            if($config->getAppValue('nextpad', 'nextpad_etherpad_enable', 'no') !== 'no' AND $config->getAppValue('nextpad', 'nextpad_etherpad_useapi', 'no') !== 'no') {
                try {
                    $eplHost = $config->getAppValue('nextpad', 'nextpad_etherpad_host', '');
                    $eplApiKey = $config->getAppValue('nextpad', 'nextpad_etherpad_apikey', '');
                    $eplInstance = new \EtherpadLite\Client($eplApiKey, $eplHost . "/api");

                    if($protected === true) {
                        // Create a protected (group) pad via API
                        $group = $eplInstance->createGroup();
                        $groupPad = $eplInstance->createGroupPad($group->groupID, $token);
                        $padID = $groupPad->padID;
                        $roPadID = $eplInstance->getReadOnlyID($padID)->readOnlyID;
                    }
                    else {
                        // Create a public pad via API
                        $createPadResult = $eplInstance->createPad($token);
                        $padID = $createPadResult->padID;
                        $roPadID = $eplInstance->getReadOnlyID($padID)->readOnlyID;
                    }
                }
                catch(Exception $e) {
                    throw new NextpadException($e);
                }
            }

            $ext = "pad";
            $host = \OC::$server->getConfig()->getAppValue('nextpad', 'nextpad_etherpad_host', false);
            $url = sprintf("%s/p/%s", rtrim($host, "/"), $roPadID);
        }

        if($padname === '' || $padname === '.' || $padname === '..') {
            throw new NextpadException($l10n->t('Incorrect padname.'));
        }

        try {
            $view = new \OC\Files\View();
            $view->verifyPath($dir, $padname);
        }
        catch(\OCP\Files\InvalidPathException $ex) {
            throw new NextpadException($l10n_files->t("Invalid name, '\\', '/', '<', '>', ':', '\"', '|', '?' and '*' are not allowed."));
        }

        if(!\OC\Files\Filesystem::file_exists($dir . '/')) {
            throw new NextpadException($l10n_files->t('The target folder has been moved or deleted.'));
        }

        // Add the extension only if padname doesn’t contain it
        if(substr($padname, -strlen(".$ext")) !== ".$ext") {
            $filename = "$padname.$ext";
        }
        else {
            $filename = $padname;
        }

        $target = $dir . "/" . $filename;

        if(\OC\Files\Filesystem::file_exists($target)) {
            throw new NextpadException($l10n_files->t('The name %s is already used in the folder %s. Please choose a different name.', [$filename, $dir]));
        }

        $content = sprintf("[InternetShortcut]\nURL=%s", $url);

        if(\OC\Files\Filesystem::file_put_contents($target, $content)) {
            $meta = \OC\Files\Filesystem::getFileInfo($target);
            return \OCA\Files\Helper::formatFileInfo($meta);
        }

        throw new NextpadException($l10n_files->t('Error when creating the file'));
    }
}
