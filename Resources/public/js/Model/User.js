Core.ns("App.User.Model");

App.User.Model.User = Backbone.Model.extend({
    urlRoot: Routing.generate("rtxlabs_userbundle_user_list"),

    initialize: function() {
        //this.roles = new App.User.Collection.RoleCollection();
        //this.groups = new App.User.Collection.GroupCollection();
    },

    defaults: {
        firstname: "",
        lastname: "",
        email: "",
        personnelNumber: "",
        username: "",
        password: "",
        admin: false
    },

    validate: {
        firstname: {
            required: true
        },
        lastname: {
            required: true
        },
        password: {
            required: true,
            minlength: 5
        },
        username: {
            required: true
        },
        email: {
            required: true,
            type: "email"
        },
        personnelNumber: {
            pattern: /^[0-9]{4}$/
        }
    }
});