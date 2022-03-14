sprout.page.Manage = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [
            {
                xtype: 'sprout-panel-manage',
                renderTo: 'sprout-panel-manage-div'
            }
        ]
    });
    sprout.page.Manage.superclass.constructor.call(this, config);
};
Ext.extend(sprout.page.Manage, MODx.Component);
Ext.reg('sprout-page-manage', sprout.page.Manage);
