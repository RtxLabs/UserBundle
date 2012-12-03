Core.ns('App.User.View.');

App.User.View.ReactivationView = Backbone.View.extend({
    el: $('#registration-main'),

    initialize: function() {
        this.template = _.template($('#reactivation-template').html());
        _.bindAll(this, 'render');
    },

    render: function() {
        $(this.el).html(this.template());
        return this;
    }
});