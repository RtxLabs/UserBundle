Core.ns("App.User.Router");

App.User.Router.UserRouter = Backbone.Router.extend({
    routes: {
        "": "index",
        "new": "new"
    },

    initialize: function() {
        this.user = new App.User.Collection.UserCollection();
        this.userListView = new App.User.View.UserListView({collection: this.user});
    },

    index: function() {
        this.user.fetch({
            error: function(collection, response) {
                new Error({ message: "Error loading documents."});
            }
        });
    },

    new: function() {
        new App.Andon.View.LineView({
            model: new App.User.Model.User(),
            collection: this.user
        });
    }
});

