android.window.CreateItem = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'android-item-window-create';
    }
    Ext.applyIf(config, {
        title: _('android_item_create'),
        width: 550,
        autoHeight: true,
        url: android.config.connector_url,
        action: 'mgr/item/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    android.window.CreateItem.superclass.constructor.call(this, config);
};
Ext.extend(android.window.CreateItem, MODx.Window, {

    getFields: function (config) {
        return [{
            /* xtype: 'android-combo-user',
			fieldLabel: _('android_grid_username'),
			//name: 'user_id',
			id: config.id + '-' + 'user_id',
			anchor: '99%'
		},{ */
            xtype: 'textfild',
            fieldLabel: _('android_item_api_key'),
            name: 'api_key',
            id: config.id + '-api_key',
            height: 150,
            anchor: '99%'
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('android_item_active'),
            name: 'active',
            id: config.id + '-active',
            checked: true,
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('android-item-window-create', android.window.CreateItem);


android.window.UpdateItem = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'android-item-window-update';
    }
    Ext.applyIf(config, {
        title: _('android_item_update'),
        width: 550,
        autoHeight: true,
        url: android.config.connector_url,
        action: 'mgr/item/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    android.window.UpdateItem.superclass.constructor.call(this, config);
};
Ext.extend(android.window.UpdateItem, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        }, {
            /*xtype: 'android-combo-user',
			fieldLabel: _('android_grid_username'),
			//name: 'user_id',
			id: config.id + '-' + 'user_id',
			anchor: '99%'
		},{*/
            xtype: 'textfild',
            fieldLabel: _('android_item_api_key'),
            name: 'api_key',
            id: config.id + '-api_key',
            height: 150,
            anchor: '99%'
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('android_item_active'),
            name: 'active',
            id: config.id + '-active',
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('android-item-window-update', android.window.UpdateItem);

