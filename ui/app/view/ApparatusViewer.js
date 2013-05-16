Ext.define('TableApparatusApp.view.ApparatusViewer', {
    extend: 'Ext.window.Window',
    closable: false,
    height: 500,
    header:false,
    border: false,
    resizeHandles: '',
    width: 600,
    layout: {
        type: 'border'
    },
    requires: [
        'TableApparatusApp.view.TableView',
        'TableApparatusApp.view.VersionView'
    ],
    alias: 'widget.apparatusviewer',
    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {
            cls: 'tableapp',
            dockedItems: [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            xtype: 'button',
                            iconCls: 'configureIcon',
                            itemId: 'configureButton'
                        },
                        {
                            xtype: 'tbfill'
                        },
                        {
                            
                            xtype: 'combobox',
                            itemId: 'documentSelector',
                            forceSelection: false,
                            fieldLabel: 'Document',
                            store: 'DocumentListStore',
                            displayField: 'documentId',
                            valueVield: 'documentId',
                            editable: false,
                            grow: true,
                            labelWidth: 55
                            
                        },
                        {
                            xtype: 'combobox',
                            itemId: 'versionSelector',
                            // typeAhead: true,
                            forceSelection: true,
                            fieldLabel: 'Base Version',
                            store: 'VersionListStore',
                            displayField: 'longname',
                            valueField: 'version',
                            editable: false,
                            grow: true,
                            labelWidth: 70
                        },
                        {
                            xtype: 'button',
                            iconCls: 'fullscreenIcon',
                            itemId: 'toggleFullscreenButton'
                        }
                    ]
                }
            ],
            items: [
                {
                    xtype: 'versionview',
                    flex: 3,
                    region: 'center'
                },
                {
                    xtype: 'tableview',
                    flex: 0,
                    region: 'south',
                    height: 150,
                    minHeight: 50,
                    resizable: {handles:'n'},
                    collapsible: true
                }
            ]
        });

        me.callParent(arguments);
    }
});
