Core.ns("App.User.Model");

App.User.Model.User = Backbone.Model.extend({
    urlRoot: Routing.generate("rtxlabs_userbundle_user_list"),

    initialize: function() {
        //this.roles = new App.User.Collection.RoleCollection();
        //this.groups = new App.User.Collection.GroupCollection();
    }
});