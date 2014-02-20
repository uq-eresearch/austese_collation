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
        this.versionListInit = false;
        versionListStore.load();
    },
    initSelectDocument: function(){
        var docombo = Ext.ComponentQuery.query('#documentSelector')[0];
        var urlsplit = document.location.href.split('#');
        var docstore = Ext.getStore('DocumentListStore');
        if (urlsplit.length > 1){
            var docpath = decodeURIComponent(urlsplit[1]);
            var rec = docstore.findRecord('documentId',docpath);
            if (!rec || rec == -1){
                // add to document list if it is not already in the list
                rec = docstore.add({documentId:docpath});
            } 
            docombo.select(rec);
        } else {
            // set default init value for document if one wasn't provided
            docstore.add({documentId:'english/shakespeare/kinglear/act1/scene1'});
            docombo.setValue('english/shakespeare/kinglear/act1/scene1');
        }
    },
    onVersionListLoad: function(store, records){
        // ensure first and last record are loaded into versionSelector combos and force select event to fire
        // this will ensure that the other views are updated
        var versionSelector1 = Ext.ComponentQuery.query('#versionSelector1')[0];
        var versionSelector2 = Ext.ComponentQuery.query('#versionSelector2')[0];
        if (records && records.length > 0){
            if (!this.versionListInit) {
                versionSelector1.select(records[0]);
                versionSelector2.select(records[records.length-1]);
                this.versionListInit = true;
            }
            versionSelector2.fireEvent('select',versionSelector1,records);
        }
    },
    moveVariant: function(button, event, direction){
        var versions = Ext.ComponentQuery.query('versionview');
        var currentVersion;
        var current;
        if (button.itemId =="prevVariantBtn1" || button.itemId == "nextVariantBtn1"){
            // left hand side
            currentVersion = versions[0];
            current = parseInt(button.next("variantcountlabel").getCurrentVariant(), 10);
        } else {
            // right hand side
            currentVersion = versions[1];
            current = parseInt(button.prev("variantcountlabel").getCurrentVariant(), 10);
        }
        if (!current) {
            current = 1;
        }
        var prev = currentVersion.body.select("span[data-variant=" + (current + (direction * 1)) + "]").elements[0];
        if (prev) {
            prev.click();
            Ext.get(prev).scrollIntoView(currentVersion.body);
        }
    },
    viewRecord: function(button, event){
        var docstore = Ext.getStore('DocumentListStore');
        var docombo = Ext.ComponentQuery.query('#documentSelector')[0];
        var docpath = docombo.getValue();
        var docrecord = docstore.getById(docpath);
        var resname;
        if (button.itemId == "viewRecordBtn1"){
            // left hand side
            var version1 = Ext.ComponentQuery.query('#versionSelector1')[0].getValue();
            resname = version1.split('/');
        } else {
            var version2 = Ext.ComponentQuery.query('#versionSelector2')[0].getValue();
            var resname = version2.split('/');

        }
        // the name of the version will be in either one of these positions e.g. could be path/Base/vname or path/vname/add0 etc
        if (resname.length > 1) {
            var resname1 = resname[resname.length - 1];
        }
        if (resname.length > 2){
            var resname2 = resname[resname.length - 2];
        }
        
        var resuuid = resname;
        var resources = docrecord.get("resources");
        for (var i = 0; i < resources.length; i++){
           var res = resources[i];
           if (res.name && (res.name == resname1 || res.name == resname2)){
             resuuid = res.id;
           } 
        }
           
        var dataId = this.baseurl + "/repository/resources/" + resuuid;
        document.location.href=dataId;
    },
    attachSyncActions: function(versionView,otherVersionView, counterLabel, otherCounterLabel,cls){
       var variants = versionView.body.query("span[class='added'], span[class='deleted']");
       var variantcount = variants.length;
       counterLabel.setVariantCount(variantcount);
       var counter = 1;
       var controller = this;
       counterLabel.setCurrentVariant(0);
       otherCounterLabel.setCurrentVariant(0);
       Ext.Array.each(variants, function(e){
           var elem = Ext.get(e);
           elem.set({'data-variant':counter++});
           elem.on("click",function(event,htmlelem){
               // TODO handle parent/child links
               var vcurrent = elem.getAttribute("data-variant");
               counterLabel.setCurrentVariant(vcurrent);
               // get id of prev sibling
               var prev = elem.prev("span[class='merged']");
               if (!prev){
                   var allElem = jQuery("*");
                   var currentIndex = allElem.index(elem.dom);
                   var nearest;
                   jQuery(".merged").each(function(i, elm) {
                       var index = allElem.index(elm);
                       if (currentIndex > index) {
                           nearest = elm;
                       }
                   });
                   prev = Ext.get(nearest);
               }
               if (prev){
                   var previd = prev.id;
                   var theNumber = previd.substring(1,previd.length);
                   var theLetter = previd.substring(0,1);
                   var otherLetter, elemColor, otherColor, otherCls;
                   if (cls == "added"){
                       otherLetter = "d";
                       otherCls = "deleted";
                       otherColor = "ff0000";
                       elemColor = "2156d1";
                   } else {
                       otherLetter = "a";
                       otherCls = "added";
                       otherColor = "2156d1";
                       elemColor = "ff0000";
                   }
                   // highlight selected variant
                   elem.highlight(elemColor, { attr: 'backgroundColor', duration: 1000 });
                   // lookup corresponding variant on other side
                   var matching = Ext.get(otherLetter+theNumber);
                   var mvcount;
                   if (matching){
                       // highlight next span sibling of that elem (because we are syncing based on id of previous element to the one that was clicked)
                       var matchingnext = matching.next("span");
                       if (matchingnext){
                           matching = matchingnext;
                       }
                       matching.highlight(otherColor, { attr: 'backgroundColor', duration: 1000 });
                       matching.scrollIntoView(otherVersionView.body);
                       mvcount = matching.getAttribute("data-variant");
                   }
                   otherCounterLabel.setCurrentVariant(mvcount);
               }
           });
       });
    },
    onVersionSelectionChange: function(combo, records, options) {
        var rec = records[0];
        var controller = this;
        if (rec) {
            var versionName = rec.get("version");
            var versions = Ext.ComponentQuery.query('versionview');
            var counterLabels = Ext.ComponentQuery.query('variantcountlabel');
            var version1 = Ext.ComponentQuery.query('#versionSelector1')[0].getValue();
            var version2 = Ext.ComponentQuery.query('#versionSelector2')[0].getValue();
            var documentId = Ext.ComponentQuery.query('#documentSelector')[0].getValue();
            var baseurl = this.baseurl;
            // update left hand side
            versions[0].body.load({
                url: '/html/comparison/' + documentId,
                method: 'GET',
                params: {
                    'version1': version1,
                    'version2': version2,
                    'diff_kind': 'deleted'
                },
                scope:controller,
                success: function(response){
                  controller.attachSyncActions(versions[0],versions[1], counterLabels[0], counterLabels[1],"deleted");

                    if (!response.responseText) {
                        // Find the Resource UUID
                        var resname = version1.split('/');
                        resname=resname[resname.length - 2];
                        var docstore = Ext.getStore('DocumentListStore');
                        var docrecord = docstore.getById(documentId);
                        var resuuid = resname;
                        var resources = docrecord.get("resources");
                        for (var i = 0; i < resources.length; i++){
                           var res = resources[i];
                           if (res.name && res.name == resname){
                             resuuid = res.id;
                           } 
                        }

                        var dataId = baseurl + "/repository/resources/" + resuuid + "/content";
                        if (response && response.target && response.target){
                            var bodyEl = response.target.dom;
                            jQuery(bodyEl).removeAnnotator().data('id', dataId).data('annolabel','LHS');
                            bodyEl.annotationsEnabled = false;
                            //console.log("enable anno on compare body", bodyEl)
                            enableAnnotationsOnElement(bodyEl);
                        }
                    }
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
                scope:controller,
                success: function(response){
                    controller.attachSyncActions(versions[1],versions[0], counterLabels[1], counterLabels[0],"added");

                    if (!response.responseText) {
                        var resname = version2.split('/');
                        resname=resname[resname.length -1];
                        var docstore = Ext.getStore('DocumentListStore');
                        var docrecord = docstore.getById(documentId);
                        var resuuid = resname;
                        var resources = docrecord.get("resources");
                        for (var i = 0; i < resources.length; i++){
                           var res = resources[i];
                           if (res.name && res.name == resname){
                             resuuid = res.id;
                           } 
                        }
                        
                        var dataId = baseurl + "/repository/resources/" + resuuid + "/content";
                        var bodyEl = response.target.dom;
                        jQuery(bodyEl).removeAnnotator().data('id', dataId).data('annolabel','RHS');
                        bodyEl.annotationsEnabled = false;

                        enableAnnotationsOnElement(bodyEl);
                    }
                }
            });
        }
    },
    syncScroll: function(event, scrolledView){
        var syncButton = Ext.ComponentQuery.query("#syncButton")[0];
        if (!syncButton.pressed){
            return;
        }
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
        var newHeight = h - (placeholder.getY()) - 70;
        var newWidth = w - placeholder.getX() - 30;
        placeholder.setHeight(newHeight);
        uiPanel.setHeight(newHeight);
        placeholder.setWidth(newWidth);
        uiPanel.setWidth(newWidth);
        uiPanel.showAt(placeholder.getX(), placeholder.getY());
    },
    init: function(application) {
        this.baseurl = jQuery('#metadata').data('baseurl');
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
            "#prevVariantBtn1, #prevVariantBtn2":{
                click: function(button, event) {
                    this.moveVariant(button, event, -1);
                }
            },
            "#nextVariantBtn1, #nextVariantBtn2":{
                click: function(button, event) {
                    this.moveVariant(button, event, 1);
                }
            },
            "#viewRecordBtn1, #viewRecordBtn2":{
                click: this.viewRecord
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
        Ext.getStore('DocumentListStore').on('load',this.initSelectDocument, this);
        Ext.getStore('VersionListStore').on('load',this.onVersionListLoad, this);
    }

});
