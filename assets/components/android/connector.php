<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
}
else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var android $android */
$android = $modx->getService('android', 'android', $modx->getOption('android_core_path', null,
        $modx->getOption('core_path') . 'components/android/') . 'model/android/'
);
$modx->lexicon->load('android:default');

// handle request
$corePath = $modx->getOption('android_core_path', null, $modx->getOption('core_path') . 'components/android/');
$path = $modx->getOption('processorsPath', $android->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));