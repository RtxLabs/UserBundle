Core.ns('App.User');

App.User = {
    init: function() {
        this.router = new App.User.Router.UserRouter();
        Backbone.history.start();
    }
};
