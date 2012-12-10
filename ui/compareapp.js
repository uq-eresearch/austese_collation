
Ext.Loader.setConfig({
    enabled: true
});
var modulePath = '/sites/all/modules/austese_collation';
Ext.Loader.setPath('TableApparatusApp.store',  modulePath + '/ui/app/store');
Ext.Loader.setPath('TableApparatusApp.model',  modulePath + '/ui/app/model');
Ext.Loader.setPath('TableApparatusApp.reader',  modulePath + '/ui/app/reader');
Ext.Loader.setPath('TableApparatusApp.controller', modulePath + '/ui/app/controller');
Ext.Loader.setPath('TableApparatusApp.view',  modulePath + '/ui/app/view');
Ext.Loader.setPath('Ext.ux', '/sites/all/libraries/ext-4.1.1a/examples/ux');
// keep z-index seed low to avoid interfering with drupal admin overlay
Ext.WindowMgr.zseed = 1040;
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
        'VersionView'
    ],
    autoCreateViewport: false,
    name: 'TableApparatusApp',
    controllers: [
        'CompareAppController'
    ],
    launch: function(){
        var placeholder = Ext.get('uiplaceholder');
        var mainWindow = Ext.create('TableApparatusApp.view.CompareViewer',{
            renderTo: Ext.getBody(),
        }).showAt(placeholder.getX(),placeholder.getY());
    }
});
