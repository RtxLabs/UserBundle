Core.ns('App.User.View');

App.User.View.UserListRowView = App.Core.View.View.extend({
    tagName: 'tr',

    events: {
        'click .user-delete': 'handleDelete'
    },

    initialize: function() {
        this.template = _.template($('#user-list-row-template').html());

        _.bindAll(this, 'render');
        this.model.bind('change', this.render);
        this.render();
    },

    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));
        $('[rel=tooltip]').tooltip();

        return this;
    },

    handleDelete: function() {
        $('[rel=tooltip]').tooltip('hide');

        if (true == confirm(Translator.trans('rtxlabs.user.delete.confirm'))) {
            this.model.destroy();
        }
    }
});
