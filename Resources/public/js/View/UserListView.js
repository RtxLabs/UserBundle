Core.ns("App.User.View");

App.User.View.UserListView = Backbone.View.extend({
    el: $('#user-main'),

    initialize: function() {
        this.template = _.template($('#user-list-template').html());

        _.bindAll(this, 'render');
        this.collection.bind('reset', this.render);
        this.collection.bind('remove', this.render);

        this.render();
    },

    render: function() {
        this.el.html(this.template());

        this.collection.each(this.renderLineItem);

        return this;
    },

    renderLineItem: function(user) {
        var view = new App.User.View.UserListRowView({model: user});

        this.$("#list-table-body").append(view.render().el);
    }
});
