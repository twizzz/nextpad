<?php
/**
 * Nextcloud - Nextpad
 *
 * This file is licensed under the Affero General Public License
 * version 3 or later. See the COPYING file.
 *
 * @author Haliax <info@haliax.dev>
 * @copyright Haliax <info@haliax.dev>, 2020
 */

namespace OCA\Nextpad\Appinfo;

/** @var $this \OC\Route\Router */

$this->create('nextpad_newpad', 'ajax/newpad.php')->actionInclude('nextpad/ajax/newpad.php');

return ['routes' => [
    ['name' => 'display#showPad', 'url' => '/', 'verb' => 'GET'],
    ['name' => 'display#showPublicPad', 'url' => '/ajax/v1.0/display', 'verb' => 'GET'],
    ['name' => 'ajax#getconfig', 'url' => '/ajax/v1.0/getconfig', 'verb' => 'GET'],
    ['name' => 'ajax#newpad', 'url' => '/ajax/v1.0/newpad', 'verb' => 'POST'],
]];
