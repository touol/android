<?php

/**
 * The home manager controller for android.
 *
 */
class androidHomeManagerController extends modExtraManagerController
{
    /** @var android $android */
    public $android;


    /**
     *
     */
    public function initialize()
    {
        $path = $this->modx->getOption('android_core_path', null,
                $this->modx->getOption('core_path') . 'components/android/') . 'model/android/';
        $this->android = $this->modx->getService('android', 'android', $path);
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('android:default');
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('android');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->android->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->android->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addJavascript($this->android->config['jsUrl'] . 'mgr/android.js');
        $this->addJavascript($this->android->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->android->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->android->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->android->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->android->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->android->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        android.config = ' . json_encode($this->android->config) . ';
        android.config.connector_url = "' . $this->android->config['connectorUrl'] . '";
        Ext.onReady(function() {
            MODx.load({ xtype: "android-page-home"});
        });
        </script>
        ');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->android->config['templatesPath'] . 'home.tpl';
    }
}