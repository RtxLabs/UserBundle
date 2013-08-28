Core.ns("App.User.Model");

App.User.Model.User = App.Core.Model.Model.extend({
    urlRoot: Routing.generate("rtxlabs_userbundle_user_list"),

    hasGroup: function(id) {
        var groups = _.find(this.groups, function(group){
            return id == group.id;
        });

        return groups !== undefined;
    },

    defaults: {
        "id": null,
        "firstname": "",
        "lastname": "",
        "email": "",
        "personnelNumber": "",
        "username": "",
        "passwordRequired": true,
        "password": "",
        "plainPassword": "",
        "passwordRepeat": "",
        "locale": "de",
        "lastLogin": null,
        "roles": [],
        "groups": [],
        "active": false
    }
});