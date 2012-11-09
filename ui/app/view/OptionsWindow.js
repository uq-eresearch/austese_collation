Ext.define('TableApparatusApp.view.OptionsWindow', {
    extend: 'Ext.window.Window',
    autoHeight: true,
    width: 400,
    title: 'Table Apparatus Options',
    id: 'tableappwindow',
    closeAction: 'hide',
    requires: ['Ext.selection.CheckboxModel'],
    initComponent: function() {
        var me = this;
        Ext.applyIf(me, {
            items: [
                {
                    xtype: 'form',
                    defaultType: 'checkboxfield',
                    bodyPadding: 10,
                    defaults: {
                        anchor: '100%',
                        inputValue: 1
                    },
                    items: [
                        {
                            xtype: 'numberfield',
                            name: 'OFFSET',
                            fieldLabel: 'Start Offset',
                            minValue: 0,
                            value: 0,
                            step: 100
                        },
                        {
                            xtype: 'numberfield',
                            name: 'LENGTH',
                            fieldLabel: 'Length',
                            minValue: 0,
                            value: 1000,
                            step: 100
                        },
                        
                        {
                            name: 'COMPACT',
                            boxLabel: 'Compact',
                            tooltip: 'Select compact display format'
                        },
                        {
                            name: 'WHOLE_WORDS',
                            boxLabel: 'Whole words',
                            tooltip: 'Display whole words when using compact display'
                        },
                        {
                            name: 'HIDE_MERGED',
                            boxLabel: 'Hide Merged'
                        }
                    ]
                },
                {
                    xtype: 'grid',
                    title: 'Include versions:',
                    height: 130,
                    store: Ext.getStore('VersionListStore'),
                    selModel: Ext.create('Ext.selection.CheckboxModel'),
                    columns: [
                         {text: 'Version', dataIndex: 'version'},
                         {text: 'Name', dataIndex: 'longname'}
                    ]
                }
            ],
            dockedItems: [
                {
                    xtype: 'toolbar',
                    dock: 'bottom',
                    items: [
                        {
                            xtype: 'tbfill'
                        },
                        {
                            xtype: 'button',
                            text: 'Cancel',
                            itemId: 'cancelButton'
                        },
                        {
                            xtype: 'button',
                            text: 'Apply',
                            itemId: 'applyButton',
                            tooltip: 'Apply configuration options to the apparatus table'
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);
    }

});