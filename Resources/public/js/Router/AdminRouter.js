Core.ns("App.User.Router");

App.User.Router.AdminRouter = App.Core.Router.CoreRouter.extend({
    routes: {
        "": "renderUserList",
        "list": "renderUserList",
        "create": "renderCreateUser",
        "edit/:user": "renderEditUser",
        "group": "renderGroupList",
        "group/list": "renderGroupList",
        "group/create": "renderCreateGroup",
        "group/edit/:group": "renderEditGroup"
    },

    viewCache: {},

    breadcrumbs: {
        "group": Translator.trans("rtxlabs.user.group.header"),
        "list": Translator.trans("rtxlabs.user.label.admin.list"),
        "create": Translator.trans("rtxlabs.user.label.admin.create"),
        "edit": Translator.trans("rtxlabs.user.label.admin.edit")
    },

    initialize: function() {
        App.Core.Router.CoreRouter.prototype.initialize.call(this);
        this.userCollection = new App.User.Collection.UserCollection();
        this.groupCollection = new App.User.Collection.GroupCollection();
    },

    renderUserList: function() {
        var view = this.findCachedView("UserListView");

        if (view == null) {
            view = new App.User.View.UserListView({collection: this.userCollection});
            this.cacheView(view, "UserListView");
            this.userCollection.limit = 500;
            this.userCollection.fetch();
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
        view.model.bind('sync', function(model, response, options) {
            this.userCollection.add(model);
        }, this);

        view.render();
    },

    renderEditUser: function(id) {
        var view = this.findCachedView("UserEditView");
        if (view == null) {
            view = new App.User.View.UserEditView();
            this.cacheView(view, "UserEditView");
        }
        view.model = this.getUserModel(id);
        view.model.bind('change', function(model, response, options) {
            view.render();
            view.model.unbind('change');
        });

        view.render();
    },

    renderGroupList: function() {
        var view = this.findCachedView("GroupListView");
        if (view == null) {
            view = new App.User.View.GroupListView({collection: this.groupCollection});
            this.cacheView(view, "GroupListView");
        }

        view.render();
        this.groupCollection.fetch();
    },

    renderCreateGroup: function() {
        var view = this.findCachedView("GroupEditView");
        if (view == null) {
            view = new App.User.View.GroupEditView({
                collection: this.groupCollection
            });
            this.cacheView(view, "GroupEditView");
        }
        view.model = new App.User.Model.Group();
        view.model.bind('sync', function(model, response, options) {
            this.groupCollection.add(model);
        }, this);

        view.render();
    },

    renderEditGroup: function(id) {
        var view = this.findCachedView("GroupEditView");
        if (view == null) {
            view = new App.User.View.GroupEditView({
                collection: this.userCollection
            });
            this.cacheView(view, "GroupEditView");
        }
        view.model = this.getGroupModel(id);

        view.render();
    },

    getGroupModel: function(id) {
        var group = this.groupCollection.get(id);

        if (group == null) {
            group = new App.User.Model.Group({id: id});
            group.fetch();
        }

        return group;
    },

    getUserModel: function(id) {
        var user = this.userCollection.get(id);

        if (user == null) {
            user = new App.User.Model.User({id: id});
            user.fetch();
        }

        return user;
    },

    findCachedView: function(ident) {
        return this.viewCache[ident];
    },

    cacheView: function(view, ident) {
        this.viewCache[ident] = view;
    }
});
