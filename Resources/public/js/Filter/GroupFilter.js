Core.ns("App.User.Filter");

App.User.Filter.GroupFilter = App.Core.Filter.Filter.extend({
    defaults: {
        "name": null,
        "roles": null,
        "userCount": 0
    }
});
