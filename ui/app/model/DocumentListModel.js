
Ext.define('TableApparatusApp.model.DocumentListModel', {
    extend: 'Ext.data.Model',
    idProperty: 'documentId',
    fields: [
        {
            name: 'documentId',
            mapping: 'name'
        },
        {
            name: 'resources'
        }
    ]
});