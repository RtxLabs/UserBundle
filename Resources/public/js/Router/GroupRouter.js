Core.ns("App.User.Router");

App.User.Router.GroupRouter = Backbone.Router.extend({
    routes: {
        "": "index",
        "new": "new",
        "edit/:id": "edit"
    },

    initialize: function() {
        this.groups = new App.User.Collection.GroupCollection();
    },

    index: function() {
        $('[rel=tooltip]').tooltip('hide');

        if (this.groups.length == 0) {
            var self = this;
            this.groups.fetch({
                silent: true,
                error: function(collection, response) {
                    new Error({ message: "Error loading documents."});
                },
                success: function(collection) {
                    self.groupListView = new App.User.View.GroupListView({collection: collection});
                }
            });
        }
        else {
            this.groupListView = new App.User.View.GroupListView({collection: this.groups});
        }
    },

    new: function() {
        $('[rel=tooltip]').tooltip('hide');

        this.updateEditView(new App.User.Model.Group());
    },

    edit: function(id) {
        $('[rel=tooltip]').tooltip('hide');

        var group = this.groups.get(id);
        if (group === undefined) {
            var self = this;
            Core.DataCache.load("App.User.Model.Group", id, {
                success: function(group) {
                    self.updateEditView(group);
                },
                failure: function() {
                    new Error({ message: "Error loading user group"});
                }
            });
        }
        else {
            this.updateEditView(group);
        }
    },

    updateEditView: function(group) {
        if (this.editView !== undefined) {
            this.editView.model = group;
            this.editView.render();
        }
        else {
            this.editView = new App.User.View.GroupView({
                model: group,
                collection: this.groups
            });
        }
    }
});