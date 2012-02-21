Core.ns("App.User.Model");

App.User.Model.User = Backbone.Model.extend({
    urlRoot: Routing.generate("rtxlabs_userbundle_user_list"),

    initialize: function() {

    },

    hasGroup: function(id) {
        var groups = _.find(this.groups, function(group){
            return id == group.id;
        });

        return groups !== undefined;
    },

    defaults: {
        firstname: "",
        lastname: "",
        email: "",
        personnelNumber: "",
        username: "",
        password: "",
        admin: false,
        locale: "de",
        roles: [],
        groups: []
    }
});