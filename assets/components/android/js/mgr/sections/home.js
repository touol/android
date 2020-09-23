android.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'android-panel-home',
            renderTo: 'android-panel-home-div'
        }]
    });
    android.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(android.page.Home, MODx.Component);
Ext.reg('android-page-home', android.page.Home);