Core.ns("App.User.Model");

App.User.Model.Group = App.Core.Model.Model.extend({
    urlRoot: Routing.generate("rtxlabs_userbundle_group_list"),

    defaults: {
        "name": '',
        "userCount": 0,
        "roles": []
    }
});