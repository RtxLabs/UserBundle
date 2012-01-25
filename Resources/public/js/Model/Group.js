Core.ns("App.User.Model");

App.User.Model.Group = Backbone.Model.extend({
    urlRoot: Routing.generate("rtxlabs_userbundle_group_list"),

    initialize: function() {
        this.roles = new App.User.Collection.RoleCollection();
    }
});