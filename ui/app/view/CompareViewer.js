Ext.define('TableApparatusApp.view.CompareViewer', {
    extend: 'Ext.window.Window',
    closable: false,
    height: 500,
    header:false,
    border: false,
    resizeHandles: '',
    width: 600,
    layout: {
        type: 'hbox',
        pack: 'start',
        align: 'stretch'
    },
    requires: [
        'TableApparatusApp.view.VersionView',
        'TableApparatusApp.view.VariantCountLabel'
    ],
    alias: 'widget.compareviewer',
    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {
            cls: 'tableapp',
            dockedItems: [
                { 
                    xtype:'toolbar',
                    dock:'bottom',
                    items:[
                           {
                              itemId: 'viewRecordBtn1',
                              text: 'View record',
                              // tooltip: 'Go to next variant'
                           },
                           {
                              itemId: 'prevVariantBtn1',
                              iconCls: 'leftArrowIcon',
                              //tooltip: 'Go to previous variant'
                           },
                           {
                              itemId: 'nextVariantBtn1',
                              iconCls: 'rightArrowIcon',
                             // tooltip: 'Go to next variant'
                           },
                           
                           {
                              xtype: 'tbspacer' 
                           },
                           {
                              xtype: 'variantcountlabel'
                           },
                           {xtype:'tbfill'},
                           {
                               xtype:'button',
                               itemId: 'syncButton',
                               enableToggle:true,
                               pressed: true,
                               tooltip: 'Synchronize scrolling between versions',
                               iconCls:'syncIcon'
                           },
                           {xtype:'tbfill'},
                           {
                               xtype: 'variantcountlabel',
                               style: {textAlign:'right'}
                           },
                           {
                               xtype: 'tbspacer'
                           },
                           {
                               itemId: 'prevVariantBtn2',
                               iconCls: 'leftArrowIcon',
                               //tooltip: 'Go to previous variant'
                           },
                           {
                               itemId: 'nextVariantBtn2',
                               iconCls: 'rightArrowIcon',
                               //tooltip: 'Go to next variant'
                           },
                           {
                               itemId: 'viewRecordBtn2',
                               text: 'View record',
                              // tooltip: 'Go to next variant'
                            }
                    ]
                },
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    enableOverflow: true,
                    items: [
                        {
                            xtype: 'combobox',
                            itemId: 'documentSelector',
                            forceSelection: true,
                            fieldLabel: 'Document',
                            store: 'DocumentListStore',
                            displayField: 'documentId',
                            valueVield: 'documentId',
                            editable: false,
                            grow: true,
                            labelWidth: 55
                        },
                        {
                            xtype: 'tbfill'
                        },
                        {
                            xtype: 'combobox',
                            itemId: 'versionSelector1',
                            typeAhead: true,
                            forceSelection: true,
                            fieldLabel: 'Version 1',
                            store: 'VersionListStore',
                            displayField: 'longname',
                            valueField: 'version',
                            matchFieldWidth: false,
                            editable: false,
                            labelWidth: 55,
                            width: 350
                        },
                        {
                            xtype: 'combobox',
                            itemId: 'versionSelector2',
                            typeAhead: true,
                            forceSelection: true,
                            fieldLabel: 'Version 2',
                            store: 'VersionListStore',
                            displayField: 'longname',
                            valueField: 'version',
                            matchFieldWidth: false,
                            editable: false,
                            labelWidth: 55,
                            width: 350
                        },
                        {
                            xtype: 'button',
                            iconCls: 'fullscreenIcon',
                            itemId: 'toggleFullscreenButton',
                            tooltip: 'Toggle fullscreen mode'
                        }
                    ]
                }
            ],
            items: [
                {
                    xtype: 'versionview',
                    flex: 1,
                    /*dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'bottom',
                        items: [
                            
                            {
                                xtype:'tbfill'
                            },
                            {
                                text: 'Prev'
                            },
                            {
                                text: 'Next'
                            }
                        ]
                    }]*/
                    
                },
                {
                    xtype: 'versionview',
                   flex: 1
                }
            ]
        });

        me.callParent(arguments);
    }
});
