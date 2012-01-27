Core.ns("App.User.Router");

App.User.Router.UserRouter = Backbone.Router.extend({
    routes: {
        "": "index",
        "new": "new",
        "edit/:id": "edit"
    },

    initialize: function() {
        this.user = new App.User.Collection.UserCollection();
    },

    index: function() {
        $('.twipsy').remove();

        if (this.user.length == 0) {
            this.user.fetch({
                error: function(collection, response) {
                    new Error({ message: "Error loading documents."});
                },
                success: function(collection) {
                    this.userListView = new App.User.View.UserListView({collection: collection});
                }
            });
        }
        else {
            this.userListView = new App.User.View.UserListView({collection: this.user});
        }
    },

    new: function() {
        $('.twipsy').remove();


        this.editView = new App.User.View.UserView({
            model: new App.User.Model.User()
        });
    },

    edit: function(id) {
        $('.twipsy').remove();

        var user = this.user.get(id);
        if (user === undefined) {
            Core.DataCache.load("App.User.Model.User", id, {
                success: function(user) {
                    this.editView = new App.User.View.UserView({
                        model: user
                    });
                },
                failure: function() {
                    new Error({ message: "Error loading user"});
                }
            });
        }
        else {
            /*if (this.editView !== undefined) {
                this.editView.remove();
            }*/
            this.editView = new App.User.View.UserView({
                model: user
            });
            this.editView.render();
        }
    }
});

