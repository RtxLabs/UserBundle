Core.ns("App.User.Router");

App.User.Router.RegistrationRouter = Backbone.Router.extend({
    routes: {
        "": "index",
        "confirmed": "confirmed",
        "reactivation": "reactivation",
        "reactivation/confirmed": "reactivationConfirmed"
    },

    index: function() {
        var userModel = new App.User.Model.User();
        this.registrationView = new App.User.View.RegistrationView({model: userModel});
        this.registrationView.render();
    },
    
    confirmed: function() {
        this.confirmedView = new App.User.View.RegistrationConfirmedView();
        this.confirmedView.render();

        redirectOnConfirm();
    },

    reactivation: function() {
        this.reactivationView = new App.User.View.ReactivationView();
        this.reactivationView.render();
    },

    reactivationConfirmed: function() {
        this.reactivationConfirmedView = new App.User.View.ReactivationConfirmedView();
        this.reactivationConfirmedView.render();

        redirectOnConfirm();
    }
});

