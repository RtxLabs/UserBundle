Core.ns("App.User.Router");

App.User.Router.AdminRouter = App.Core.Router.CoreRouter.extend({
    routes: {
        "": "renderUserList",
        "list": "renderUserList",
        "create": "renderCreateUser",
        "edit/:id": "renderEditUser",
        "group": "renderGroup",
        "group/create": "renderCreateGroup",
        "group/edit/:id": "renderEditGroup"
    },

    viewCache: {},

    initialize: function() {
        this.userCollection = new App.User.Collection.UserCollection();
        this.userCollection.fetch({async: false});
    },

    renderUserList: function() {
        var view = this.findCachedView("UserListView");
        if (view == null) {
            view = new App.User.View.UserListView({collection: this.userCollection});
            this.cacheView(view, "UserListView");
        }

        view.render();
    },

    renderCreateUser: function() {
        var view = this.findCachedView("UserEditView");
        if (view == null) {
            view = new App.User.View.UserEditView({
                collection: this.userCollection
            });
            this.cacheView(view, "UserEditView");
        }
        view.model = new App.User.Model.User();

        view.render();
    },

    renderEditUser: function(id) {
        var view = this.findCachedView("UserEditView");
        if (view == null) {
            view = new App.User.View.UserEditView({
                collection: this.userCollection
            });
            this.cacheView(view, "UserEditView");
        }
        view.model = this.userCollection.get(id);

        view.render();
    },

    findCachedView: function(ident) {
        return this.viewCache[ident];
    },

    cacheView: function(view, ident) {
        this.viewCache[ident] = view;
    }
});
