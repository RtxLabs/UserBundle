Core.ns('App.User.View');

App.User.View.UserListRowView = Backbone.View.extend({
    tagName: 'tr',

    initialize: function() {
        this.template = _.template($('#user-list-row-template').html());

        _.bindAll(this, 'render');
        this.model.bind('change', this.render);
        this.render();
    },

    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));
        return this;
    }
});
