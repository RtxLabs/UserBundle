Core.ns("App.User.Filter");

App.User.Filter.UserFilter = App.Core.Filter.Filter.extend({
    defaults: {
        "firstname": null,
        "lastname": null,
        "username": null,
        "personnelNumber": null
    }
});
