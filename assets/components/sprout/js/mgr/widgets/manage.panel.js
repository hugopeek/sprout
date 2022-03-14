sprout.panel.Manage = function (config) {
    config = config || {};
    Ext.apply(config, {
        border: false,
        baseCls: 'modx-formpanel',
        cls: 'container',
        items: [
            {
                html: '<h2>' + _('sprout.manage.page_title') + '</h2>',
                border: false,
                cls: 'modx-page-header'
            },
            {
                xtype: 'modx-tabs',
                defaults: {
                    border: false,
                    autoHeight: true
                },
                border: true,
                activeItem: 0,
                hideMode: 'offsets',
                items: [
                    {
                        title: _('sprout.manage.page_title'),
                        layout: 'form',
                        items: [
                            {
                                cls: 'main-wrapper',
                               html: 'Greetings from John. Thank you for using GPM.'
                            }
                        ]
                    }
                ]
            }
        ]
    });
    sprout.panel.Manage.superclass.constructor.call(this, config);
};
Ext.extend(sprout.panel.Manage, MODx.Panel);
Ext.reg('sprout-panel-manage', sprout.panel.Manage);
