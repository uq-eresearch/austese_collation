Ext.define('TableApparatusApp.store.DocumentListStore', {
    extend: 'Ext.data.Store',

    requires: [
        'TableApparatusApp.model.DocumentListModel',
    ],

    constructor: function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            // TODO: Eventually the possible document values should be loaded from the repository
            data: [
                   {documentId: 'english/shakespeare/kinglear/act1/scene1'},
                   {documentId: 'italian/capuana/aristofanunculos/Introduction'},
                   {documentId: 'italian/capuana/aristofanunculos/Frammento 1'}
            ],
            storeId: 'DocumentListStore',
            model: 'TableApparatusApp.model.DocumentListModel',
            proxy: {
                type: 'memory',
                reader: {
                    type: 'json'
                }
            }
        }, cfg)]);
    }
});