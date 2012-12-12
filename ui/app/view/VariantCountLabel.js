Ext.define('TableApparatusApp.view.VariantCountLabel', {
    extend: 'Ext.form.Label',
    alias: 'widget.variantcountlabel',
    initComponent: function() {
        var me = this;
        Ext.applyIf(me, {
            width: 100,
            variantCount: 0,
            currentVariant: 0,
            previousVariant: 0,
            tpl: new Ext.Template("{currentVariant} of {variantCount}").compile()
        });
        me.callParent(arguments);
    },
    setVariantCount: function(count){
        this.variantCount = count;
        this.setText(this.tpl.apply(this));
    },
    setCurrentVariant: function(current){
        if (current) {
            this.currentVariant = current;
            delete this.previousVariant;
        } else {
            this.previousVariant = current;
            this.currentVariant = "--";
        }
        this.setText(this.tpl.apply(this));
    },
    getCurrentVariant: function(){
        // previousVariant is only set when current was set to undefined
        return this.previousVariant || this.currentVariant;
    }
});