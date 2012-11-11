
Ext.Loader.setConfig({
    enabled: true
});
var modulePath = '/sites/all/modules/austese_collation';
Ext.Loader.setPath('TableApparatusApp.store',  modulePath + '/ui/app/store');
Ext.Loader.setPath('TableApparatusApp.model',  modulePath + '/ui/app/model');
Ext.Loader.setPath('TableApparatusApp.reader',  modulePath + '/ui/app/reader');
Ext.Loader.setPath('TableApparatusApp.controller', modulePath + '/ui/app/controller');
Ext.Loader.setPath('TableApparatusApp.view',  modulePath + '/ui/app/view');
Ext.Loader.setPath('Ext.ux', '/ext-4.1.1a/examples/ux');

Ext.application({
    models: [
        'VersionListModel',
        'DocumentListModel'
    ],
    stores: [
        'VersionListStore',
        'DocumentListStore'
    ],
    views: [
        'ApparatusViewer',
        'OptionsWindow',
        'TableView',
        'VersionView'
    ],
    autoCreateViewport: false,
    name: 'TableApparatusApp',
    controllers: [
        'TableApparatusAppController'
    ],
    launch: function(){
        Ext.create('TableApparatusApp.view.ApparatusViewer', {
            renderTo: 'tableappui'
        });
        // render config window
        Ext.create('TableApparatusApp.view.OptionsWindow').show().hide();
    }
});
