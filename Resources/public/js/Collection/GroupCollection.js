Core.ns("App.User.Collection");

App.User.Collection.GroupCollection = Backbone.Collection.extend({
    model: App.User.Model.Group,
    url: Routing.generate('rtxlabs_userbundle_role_list')
});
