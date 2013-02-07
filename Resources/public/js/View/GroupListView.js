Core.ns("App.User.View");

App.User.View.GroupListView = Backbone.View.extend({
    el: $('#user-main'),

    initialize: function() {
        this.template = _.template($('#group-list-template').html());
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

    renderRow: function(group) {
        var view = new App.User.View.GroupListRowView({model: group});

        this.$("#list-table-body").append(view.render().el);
    }
});
