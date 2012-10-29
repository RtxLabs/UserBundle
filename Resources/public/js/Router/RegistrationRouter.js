Core.ns("App.User.Router");

App.User.Router.RegistrationRouter = Backbone.Router.extend({
    routes: {
        "": "index",
        "confirmed": "confirmed"
    },

    initialize: function() {
        this.user = new App.User.Collection.UserCollection();
    },

    index: function() {
        var userModel = new App.User.Model.User();
        this.registrationView = new App.User.View.RegistrationView({model: userModel});
        this.registrationView.render();
    },
    
    confirmed: function() {
        this.confirmedView = new App.User.View.ConfirmedView();
        this.confirmedView.render();

        setTimeout(function() {
            window.location.href = '/app_dev.php/user/index#account';
        }, 10000);
    }
});

