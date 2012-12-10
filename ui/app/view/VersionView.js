Ext.define('TableApparatusApp.view.VersionView', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.versionview',
    initComponent: function() {
        var me = this;
        this.addEvents('scroll');
        Ext.applyIf(me, {
            autoScroll: true,
            bodyPadding: 5,
            //itemId: 'versionView',
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
        this.fireEvent('scroll', e, this);
    }

});