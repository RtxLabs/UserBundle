Core.ns("App.User.Collection");

App.User.Collection.UserCollection = Backbone.Collection.extend({
    model: App.User.Model.User,
    url: Routing.generate('rtxlabs_userbundle_user_list')
});
