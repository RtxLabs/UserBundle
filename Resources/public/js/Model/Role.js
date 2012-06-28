Core.ns("App.User.Model");

App.User.Model.Role = Backbone.Model.extend({
    urlRoot: Routing.generate("rtxlabs_userbundle_role_list")
});