Core.ns("App.User.Collection");

App.User.Collection.RoleCollection = Backbone.Collection.extend({
    model: App.User.Model.Role,
    url: Routing.generate('rtxlabs_userbundle_role_list')
});
