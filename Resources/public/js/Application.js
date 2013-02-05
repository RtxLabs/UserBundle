Core.ns('App.User');

App.User = {
    initUserModule: function() {
        this.router = new App.User.Router.UserRouter();
        Backbone.history.start();
    },

    initGroupModule: function() {
        this.router = new App.User.Router.GroupRouter();
        Backbone.history.start();
    },

    initRegistrationModule: function() {
        this.router = new App.User.Router.RegistrationRouter();
        Backbone.history.start();
    },

    initAdminModule: function() {
        this.router = new App.User.Router.AdminRouter();
        Backbone.history.start();
    }
};
