Core.ns('App.User.View');

App.User.View.UserListRowView = Backbone.View.extend({
    tagName: 'tr',

    events: {
        'click .delete': 'deleteUser'
    },

    initialize: function() {
        this.template = _.template($('#user-list-row-template').html());

        _.bindAll(this, 'render');
        this.model.bind('change', this.render);
        this.render();
    },

    deleteUser: function() {
        $('[rel=tooltip]').tooltip('hide');

        if (true == confirm(Translator.get('rtxlabs.user.delete.confirm'))) {
            this.model.destroy();
        }
    },

    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));

        $('[rel=tooltip]').tooltip();

        return this;
    }
});
