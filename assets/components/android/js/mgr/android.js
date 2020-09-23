var android = function (config) {
    config = config || {};
    android.superclass.constructor.call(this, config);
};
Ext.extend(android, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('android', android);

android = new android();