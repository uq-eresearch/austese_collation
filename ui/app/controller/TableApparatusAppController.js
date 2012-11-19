Ext.define('TableApparatusApp.controller.TableApparatusAppController', {
    extend: 'Ext.app.Controller',
    getConfigWindow: function(){
        if (!this.configWindow) {
            this.configWindow = Ext.ComponentQuery.query('#tableappwindow')[0];
        }
        return this.configWindow;
    },
    showConfigureOptions: function(button, e, options) {
        this.getConfigWindow().show();
    },
    cancelConfigureOptions: function(button, e, options) {
        button.up('window').hide();
    },
    toggleFullscreen: function(button, e, options){
        button.up('window').toggleMaximize();
        if (button.iconCls=='exitFullscreenIcon') {
            button.setIconCls('fullscreenIcon');
        } else {
            button.setIconCls('exitFullscreenIcon');
            // set height of placeholder to 0 to prevent overflow in browser window
            var placeholder = Ext.get('tableuiplaceholder');
            placeholder.setHeight(0);
            Ext.getBody().scrollTo('top',0);
        }
    },
    /* 
     * Reads config options from form and grid in the options window
     */
    getTableViewConfig: function(){
        var configWindow = this.getConfigWindow();
        var params = configWindow.down('form').getForm().getValues();
        var selectedVersions = configWindow.down('grid').getSelectionModel().getSelection();
        var selectedVersionsParam = '';
        Ext.Array.forEach(selectedVersions,function(r,i){
            selectedVersionsParam += r.get('version');
            if (i < selectedVersions.length - 1){
                selectedVersionsParam += ",";
            }
        });
        if (selectedVersionsParam){
            params['SELECTED_VERSIONS'] = selectedVersionsParam;
            params['SOME_VERSIONS'] = 1;
        }
        var versionSelector = Ext.ComponentQuery.query('#versionSelector');
        if (versionSelector.length > 0) {
            params['version1'] = versionSelector[0].getSubmitValue();
        }
        //console.log(params);
        return params;
    },
    applyOptions: function() {
        var tableView = Ext.ComponentQuery.query('#tableView')[0];
        var documentId = Ext.ComponentQuery.query('#documentSelector')[0].getValue();
        var url = '/html/table/' + documentId;
        var params = this.getTableViewConfig();
        // reload the table view with the new config options
        tableView.body.load({
            url: url,
            method: 'GET',
            params: params
        });
        
        this.getConfigWindow().hide();
    },
    onDocumentIdChange: function(t, newVal, oldVal, opts) {
        // update the list of versions when the document id changes (this will trigger version view to update)
        var versionListStore = Ext.getStore("VersionListStore");
        // TODO: update to /json/list when id is included in json
        versionListStore.getProxy().url = '/json/list/' + newVal;
        
        // after version list has loaded, reset the table view version selection options
        versionListStore.load({scope: this, callback:function(){
            this.getConfigWindow().down('grid').getSelectionModel().selectAll();
        }});
    },
    onVersionListLoad: function(store, records){
        // ensure first record is loaded into versionSelector combo and force select event to fire
        // this will ensure that the other views are updated
        var versionSelector = Ext.ComponentQuery.query('#versionSelector')[0];
        versionSelector.select(records[0]);
        versionSelector.fireEvent('select',versionSelector,records);
    },
    onVersionSelectionChange: function(combo, records, options) {
        //console.log("on version selection change", arguments)
        var rec = records[0];
        if (rec) {
            var versionName = rec.get("version");
            var documentId = Ext.ComponentQuery.query('#documentSelector')[0].getValue();
            // load selected version into versionView
            var versionView = Ext.ComponentQuery.query('#versionView')[0];

            versionView.body.load({
                url: '/html/' + documentId,
                method: 'GET',
                params: {'version1': versionName},
                success: function(){
                  /*  var versionViewBody = Ext.ComponentQuery.query('#versionView')[0].body;
                    var textContent = versionViewBody.dom.textContent || versionViewBody.dom.innerText;
                    var numContentCharacters = textContent.length;
                    Ext.ComponentQuery.query('#tableappwindow')[0].down('form').getForm().findField('LENGTH').setValue(numContentCharacters);
                    */
                    
                }
            });
            // reload table view (with this version as base)
            var tableView = Ext.ComponentQuery.query('#tableView')[0];
            if (tableView && tableView.body){
                this.applyOptions();
            } else if (tableView) {
                tableView.on('render',this.applyOptions);
            }
        }
    },
    
    syncScroll: function(fromVersionView){
        // temporary behaviour until proper sync scroll that does incremental loading of table is implemented:
        // fetch table length of entire version, scroll to match
        var versionViewBody = Ext.ComponentQuery.query('#versionView')[0].body;
        var tableView = Ext.ComponentQuery.query('#tableView')[0];
        var tableViewBody = tableView.body;
        var textContent = versionViewBody.dom.textContent || versionViewBody.dom.innerText;
        var numContentCharacters = textContent.length;
        
        var tableOptions = this.getTableViewConfig();
        if (numContentCharacters > tableOptions.LENGTH) {
            this.getConfigWindow().down('form').getForm().findField('LENGTH').setValue(numContentCharacters);
            this.applyOptions();
            
        }
        
        var maxScroll = 0;
        var otherMaxScroll = 0;
        var currentScroll = 0;
        var percent = 0;
        var otherScroll = 0;
        if (fromVersionView) {
            // get current versionView scroll amount and calculate percentage of scroll position
            maxScroll = versionViewBody.dom.scrollHeight - versionViewBody.dom.clientHeight;
            
            currentScroll = versionViewBody.getScroll().top;
            if (currentScroll != 0){
                percent = currentScroll / maxScroll;
            }

            var xy = versionViewBody.getXY();
            var firstVisible, prevVisible;
            // adjust scroll amount so that scroll amount matches with how offsetsTo is calculated
            var currentScrollAdjusted = percent * Ext.fly(versionViewBody, '_internal').getHeight();
            var contentCharOffset = 0;
            // find the first element visible in the version view, which we will use to align content
            Ext.Array.each(versionViewBody.query("*"),function(e){
                var current = Ext.get(e);
                var offsets = current.getOffsetsTo(versionViewBody);
                if (offsets[1] > currentScrollAdjusted){
                    // if current's offset is greater than what would be shown on screen, use prev
                    if (offsets[1] > (currentScrollAdjusted + versionViewBody.getHeight())) {
                        firstVisible = prevVisible;
                    } else {
                        
                        firstVisible =  current;
                    }
                    return false;
                }
                contentCharOffset += (e.textContent || e.innerText).length;
                prevVisible = e;
            });
            /*if (firstVisible) {
                //console.log(currentScrollAdjusted + " offset of first visible",firstVisible.getOffsetsTo(versionViewBody), firstVisible.dom.textContent || firstVisible.dom.innerText);
                firstVisible.highlight("ffff9c", { attr: 'backgroundColor', duration: 500 });
                //firstVisible.frame("#ff0000", 1, { duration: 1000 });
            }*/
            //var alignText = (firstVisible.dom.textContent || firstVisible.dom.innerText).substring(0,10);
            
            // now try to find some text that matches close to the same percentage of scroll in tableView
            // use the content of the last row in the table as this will be the same version as displayed in version view
            var textPercent = contentCharOffset / numContentCharacters;
            otherMaxScroll = tableViewBody.dom.scrollWidth - tableViewBody.dom.clientWidth;
            otherScroll = otherMaxScroll * textPercent;

            //otherScroll = percent * Ext.fly(tableViewBody, '_internal').getWidth();
            var tableFirstVisible;
            // FIXME: scroll by same percentage until the content alignment is working 
            tableViewBody.scrollTo('left', otherScroll);
            /*Ext.Array.each(tableViewBody.query("tr:last-child td"), function(e){
                var current = Ext.get(e);
                var offsets = current.getOffsetsTo(tableViewBody);
                if (offsets[0] > otherScroll){
                     //if current's offset is greater than what would be shown on screen, use prev
                    if (offsets[0] > (otherScroll + tableViewBody.getWidth())) {
                        tableFirstVisible = prevVisible;
                    } else {
                        tableFirstVisible =  current;
                    }
                    return false;
                }
                prevVisible = e;
            });*/
            
            if (tableFirstVisible) {
              /*  //console.log("looking for: " + alignText);//,tableFirstVisible);
                var counter = 0;
                var matchText = tableFirstVisible.dom.textContent || tableFirstVisible.dom.innerText;
                var tmpNode = tableFirstVisible;
                var match = false;
                while (counter < 5 && tmpNode && !matchText.match(alignText)) {
                    matchText = tmpNode.dom.textContent || tmpNode.dom.innerText;
                    tmpNode = tmpNode.prev();
                    
                    counter++;
                }
                if (!matchText.match(alignText)) {
                    // no match found, look in other direction
                    counter = 0;
                    matchText = "";
                    tmpNode = tableFirstVisible.next();
                    while(counter < 5 && tmpNode && !matchText.match(alignText)){
                        matchText = tmpNode.dom.textContent || tmpNode.dom.innerText;
                        tmpNode = tmpNode.next();
                        
                        counter++;
                    }
                    if (matchText.match(alignText)){
                        //console.log("match found next : " + matchText,tmpNode)
                        tableFirstVisible = tmpNode;
                    }
                } else {
                    //console.log("match found prev: " + matchText,tmpNode)
                    tableFirstVisible = tmpNode;
                }*/
                if (tableFirstVisible){
                    tableFirstVisible.scrollIntoView(tableViewBody);
                    //console.log(currentScrollAdjusted + " offset of first visible",firstVisible.getOffsetsTo(versionViewBody), firstVisible.dom.textContent || firstVisible.dom.innerText);
                    tableFirstVisible.highlight("ffff9c", { attr: 'backgroundColor', duration: 500 });
                    //firstVisible.frame("#ff0000", 1, { duration: 1000 });
                }
            }
            
        } else {
            // get current tableView scroll and move versionView to match
        }
        //console.log("from " + currentScroll + " scrolled " + (fromVersionView? "tableview":"versionview") + " " + otherScroll + " " + percent);
        
    },
    resizeUI: function(w, h){
        //console.log("resizeUI")
        // force resize and repositioning of app when window resizes
        var uiPanel = Ext.ComponentQuery.query("apparatusviewer")[0];
        var placeholder = Ext.get('tableuiplaceholder');
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
            "#cancelButton": {
                click: this.cancelConfigureOptions
            },
            "#applyButton": {
                click: this.applyOptions
            },
            "#versionSelector": {
                select: this.onVersionSelectionChange
            },
            "#documentSelector": {
                change: this.onDocumentIdChange
            },
            "#versionView": {
                scroll: function(){this.syncScroll(true)},
               
            },
            "apparatusviewer": {
                restore: function(){
                    this.resizeUI(Ext.Element.getViewportWidth(),Ext.Element.getViewportHeight());
                },
                afterrender: function(){
                    this.resizeUI(Ext.Element.getViewportWidth(),Ext.Element.getViewportHeight());
                }
            }
            /*,"#tableView": {
                scroll: this.syncScroll
            }*/
            
        });
        Ext.getStore('VersionListStore').on('load',this.onVersionListLoad);
    }

});
