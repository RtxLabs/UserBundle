Core.ns("App.User.Router");

App.User.Router.RegistrationRouter = Backbone.Router.extend({
    routes: {
        "": "index"
    },

    index: function() {
        var userModel = new App.User.Model.User();
        this.registrationView = new App.User.View.RegistrationView({model: userModel});
        this.registrationView.render();
    }
});

