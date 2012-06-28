Core.ns("App.User.Router");

App.User.Router.UserRouter = Backbone.Router.extend({
    routes: {
        "": "index",
        "create": "create",
        "edit/:id": "edit",
        "account": "editAccount"
    },

    initialize: function() {
        this.user = new App.User.Collection.UserCollection();
    },

    index: function() {
        $('[rel=tooltip]').tooltip('hide');

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

    create: function() {
        $('[rel=tooltip]').tooltip('hide');

        this.updateEditView(new App.User.Model.User());
    },

    edit: function(id) {
        $('[rel=tooltip]').tooltip('hide');

        var user = this.user.get(id);
        if (user === undefined) {
            var self = this;
            Core.DataCache.load("App.User.Model.User", id, {
                success: function(user) {
                    self.updateEditView(user);
                },
                failure: function() {
                    new Error({ message: "Error loading user"});
                }
            });
        }
        else {
            this.updateEditView(user);
        }
    },

    editAccount: function() {
        $('[rel=tooltip]').tooltip('hide');

        new App.User.View.MyAccountView();
    },

    updateEditView: function(user) {
        if (this.editView !== undefined) {
            this.editView.model = user;
            this.editView.render();
        }
        else {
            this.editView = new App.User.View.UserView({
                model: user,
                collection: this.user
            });
        }
    }
});

