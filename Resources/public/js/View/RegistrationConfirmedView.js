Core.ns('App.User.View.');

App.User.View.RegistrationConfirmedView = Backbone.View.extend({
   el: $('#registration-main'),

    initialize: function() {
        this.template = _.template($('#registration-confirmed-template').html());
        _.bindAll(this, 'render');
    },

    render: function() {
        $(this.el).html(this.template());
        return this;
    }
});