Core.ns('App.User.View');

App.User.View.GroupListRowView = Backbone.View.extend({
    tagName: 'tr',

    events: {
        'click .delete': 'deleteGroup'
    },

    initialize: function() {
        this.template = _.template($('#group-list-row-template').html());

        _.bindAll(this, 'render');
        this.model.bind('change', this.render);
        this.render();
    },

    deleteGroup: function() {
        $('.twipsy').remove();

        if (true == confirm(ExposeTranslation.get('rtxlabs.user.delete.confirm'))) {
            this.model.destroy();
        }
    },

    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));

        // Restore Twipsy tooltips
        this.$('[rel=tooltip]').twipsy();

        return this;
    }
});
