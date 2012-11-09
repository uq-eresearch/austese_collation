/*
 * A custom reader that can read the version lists returned by HRITServer
 */
Ext.define('TableApparatusApp.reader.VersionListReader', {
    extend: 'Ext.data.reader.Array',
    alias: 'reader.version-reader',
    read: function(object) {
        var data = [];
        Ext.Array.map(object.responseText.split(","),function(i){
                data.push(new TableApparatusApp.model.VersionListModel({"version": i.trim(), "longname": "Version " + i.trim()}))
        });
        object.Result = data;
        return this.callParent([data]);
    }
});