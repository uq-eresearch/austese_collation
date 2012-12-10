Ext.define('TableApparatusApp.controller.CompareAppController', {
    extend: 'Ext.app.Controller',
    
    toggleFullscreen: function(button, e, options){
        button.up('window').toggleMaximize();
        if (button.iconCls=='exitFullscreenIcon') {
            button.setIconCls('fullscreenIcon');
        } else {
            button.setIconCls('exitFullscreenIcon');
            // set height of placeholder to 0 to prevent overflow in browser window
            var placeholder = Ext.get('uiplaceholder');
            placeholder.setHeight(0);
            Ext.getBody().scrollTo('top',0);
        }
    },
    
    onDocumentIdChange: function(t, newVal, oldVal, opts) {
        // update the list of versions when the document id changes (this will trigger version views to update)
        var versionListStore = Ext.getStore("VersionListStore");
        versionListStore.getProxy().url = '/json/list/' + newVal;
        versionListStore.load();
    },
    onVersionListLoad: function(store, records){
        // ensure first and last record are loaded into versionSelector combos and force select event to fire
        // this will ensure that the other views are updated
        var versionSelector1 = Ext.ComponentQuery.query('#versionSelector1')[0];
        var versionSelector2 = Ext.ComponentQuery.query('#versionSelector2')[0];
        if (records && records.length > 0){
            versionSelector1.select(records[0]);
            versionSelector2.select(records[records.length-1]);
            versionSelector2.fireEvent('select',versionSelector1,records);
        }
    },
    onVersionSelectionChange: function(combo, records, options) {
        var rec = records[0];
        if (rec) {
            var versionName = rec.get("version");
            var versions = Ext.ComponentQuery.query('versionview');
            var version1 = Ext.ComponentQuery.query('#versionSelector1')[0].getValue();
            var version2 = Ext.ComponentQuery.query('#versionSelector2')[0].getValue();
            var documentId = Ext.ComponentQuery.query('#documentSelector')[0].getValue();

            // update left hand side
            versions[0].body.load({
                url: '/html/comparison/' + documentId,
                method: 'GET',
                params: {
                    'version1': version1,
                    'version2': version2,
                    'diff_kind': 'deleted'
                },
                success: function(){
                  
                }
            });
            // update right hand side
            versions[1].body.load({
                url: '/html/comparison/' + documentId,
                method: 'GET',
                params: {
                    'version1': version2,
                    'version2': version1,
                    'diff_kind': 'added'
                },
                success: function(){
                  
                }
            });
        }
    },
    syncScroll: function(event, scrolledView){
        var views = Ext.ComponentQuery.query("versionview");
        var otherView;
        if (scrolledView == views[0]){
            otherView = views[1]
        } else {
            otherView = views[0];
        }
        otherView.suspendEvents();
        // FIXME: write version that uses ExtJS
        synchroScroll(scrolledView.body.dom,otherView.body.dom)
        otherView.resumeEvents();
    },
    resizeUI: function(w, h){
        // force resize and repositioning of app when window resizes
        var uiPanel = Ext.ComponentQuery.query("compareviewer")[0];
        var placeholder = Ext.get('uiplaceholder');
        var newHeight = h - (placeholder.getY());
        var newWidth = w - placeholder.getX()*2;
        placeholder.setHeight(newHeight);
        uiPanel.setHeight(newHeight);
        placeholder.setWidth(newWidth);
        uiPanel.setWidth(newWidth);
        uiPanel.showAt(placeholder.getX(), placeholder.getY());
    },
    init: function(application) {
        Ext.EventManager.onWindowResize(this.resizeUI, this);
        this.control({
            "#configureButton": {
                click: this.showConfigureOptions
            },
            "#toggleFullscreenButton": {
                click: this.toggleFullscreen
            },
            "#versionSelector1, #versionSelector2": {
                select: this.onVersionSelectionChange
            },
            "#documentSelector": {
                change: this.onDocumentIdChange
            },
            "versionview": {
                scroll: this.syncScroll
            },
            "compareviewer": {
                restore: function(){
                    this.resizeUI(Ext.Element.getViewportWidth(),Ext.Element.getViewportHeight());
                },
                afterrender: function(){
                    this.resizeUI(Ext.Element.getViewportWidth(),Ext.Element.getViewportHeight());
                }
            }
            
        });
        
        Ext.getStore('VersionListStore').on('load',this.onVersionListLoad);
    }

});
