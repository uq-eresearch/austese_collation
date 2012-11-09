Ext.define('TableApparatusApp.view.TableView', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.tableview',
    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {
            autoScroll: true,
            title: 'Apparatus Table',
            bodyPadding: 5,
            itemId: 'tableView',
            listeners: {
                scroll: {
                    element: 'body', 
                    fn: me.onBodyScroll,
                    scope: me
                }
           }
        });

        me.callParent(arguments);
    },
    onBodyScroll: function(e, t, opts) {
        // fire custom event when body scrolls that controller can respond to 
        // (because it can only listen for component events, not element events)
        this.fireEvent('scroll', e);
    }
});