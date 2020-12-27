<?php
/** @var array $_ */
/** @var OCP\IURLGenerator $urlGenerator */
$urlGenerator = $_['urlGenerator'];
$version = \OCP\App::getAppVersion('nextpad');
$url = $_['url'];
$title = $_['title'];
?>
<!DOCTYPE html>
<html style="height: 100%;">
  <head>
    <link rel="stylesheet" href="<?php p($urlGenerator->linkTo('nextpad', 'css/nextpad.css')) ?>?v=<?php p($version) ?>"/>
  </head>
  <body style="margin: 0px; padding: 0px; overflow: hidden; bottom: 37px; top: 0px; left: 0px; right: 0px; position: absolute;">
    <div id="nextpad_bar">
      <strong><?php p($title); ?></strong><span> - <a target="_parent" href="<?php p($url); ?>"><?php p($url); ?></a></span><a id="nextpad_close">x</a>
    </div>
    <iframe frameborder="0" id="nextpad_frame" style="overflow:hidden;width:100%;height:100%;display:block;" height="100%" width="100%" src="<?php p($url); ?>"></iframe>
  </body>
</html>
