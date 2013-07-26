Ext.define('TableApparatusApp.store.DocumentListStore', {
    extend: 'Ext.data.Store',

    requires: [
        'TableApparatusApp.model.DocumentListModel',
    ],

    constructor: function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            storeId: 'DocumentListStore',
            autoLoad: true,
            model: 'TableApparatusApp.model.DocumentListModel',
            proxy: {
                type: 'ajax',
                url: '/sites/all/modules/austese_repository/api/mvds/',
                reader: {
                    type: 'json',
                    root: 'results'
                }
            }
        }, cfg)]);
    }
});