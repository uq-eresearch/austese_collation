Ext.define('TableApparatusApp.store.VersionListStore', {
    extend: 'Ext.data.Store',

    requires: [
        'TableApparatusApp.model.VersionListModel',
        'TableApparatusApp.reader.VersionListReader'
    ],

    constructor: function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            storeId: 'VersionListStore',
            model: 'TableApparatusApp.model.VersionListModel',
            proxy: {
                type: 'ajax',
                url: '', // managed by controller based on selected document id
                reader: {
                    type: 'version-reader'
                }
            }
        }, cfg)]);
    }
    
   /* using the JSON data
     constructor: function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            storeId: 'VersionListStore',
            model: 'TableApparatusApp.model.VersionListModel',
            proxy: {
                type: 'ajax',
                url: '', // managed by controller based on selected document id
                reader: {
                    type: 'json',
                    root: 'versions'
                }
            }
        }, cfg)]);
    }
    */
});