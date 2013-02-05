Core.ns("App.User.View");

App.User.View.UserListView = Backbone.View.extend({
    el: $('#user-main'),

    initialize: function() {
        this.template = _.template($('#user-list-template').html());
        _.bindAll(this, 'render');
        this.collection.bind('reset', this.render);
        this.collection.bind('remove', this.render);
    },

    render: function() {
        $(this.el).html(this.template());

        this.collection.each(this.renderRow);
        $('[rel=tooltip]').tooltip();

        return this;
    },

    renderRow: function(user) {
        var view = new App.User.View.UserListRowView({model: user});
        this.$("#list-table-body").append(view.render().el);
    }
});
