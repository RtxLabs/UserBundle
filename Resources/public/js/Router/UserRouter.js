Core.ns("App.User.Router");

App.User.Router.UserRouter = App.Core.Router.CoreRouter.extend({
    routes: {
        "": "renderAccount",
        "account": "renderAccount"
    },

    viewCache: {},

    initialize: function() {
        this.user = new App.User.Collection.UserCollection();
    },

    renderAccount: function() {
        var view = this.findCachedView("MyAccountView");
        if (view == null) {
            view = new App.User.View.MyAccountView();
            this.cacheView(view, "MyAccountView");
        }

        view.render();
    },

    findCachedView: function(ident) {
        return this.viewCache[ident];
    },

    cacheView: function(view, ident) {
        this.viewCache[ident] = view;
    }
});

