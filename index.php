<?php
/**
 * @author: lion
 * @link: http://lionsay.com/codetoany.html
 */

require 'config.php';
require 'library/Authorize.php';

$appId = $config['appid'];
$authorize = new lion\weixin\library\Authorize($appId);
$authorize->authorizeCodeToUrl($config['wx'], 'auk', false, $config['h5']);