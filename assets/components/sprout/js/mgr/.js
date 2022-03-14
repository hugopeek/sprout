var Sprout = function (config) {
    config = config || {};
    Sprout.superclass.constructor.call(this, config);
};
Ext.extend(Sprout, Ext.Component, {

    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    field: {},
    config: {},

});
Ext.reg('sprout', Sprout);
sprout = new Sprout();
