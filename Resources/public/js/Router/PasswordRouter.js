Core.ns("App.User.Router");

App.User.Router.PasswordRouter = Backbone.Router.extend({
    routes: {
        "": "index",
        "confirmed": "confirmed"
    },

    index: function() {
        this.passwordView = new App.User.View.PasswordView();
        this.passwordView.render();
    },
    
    confirmed: function() {
        this.confirmedView = new App.User.View.ConfirmedView();
        this.confirmedView.render();
    }
});

