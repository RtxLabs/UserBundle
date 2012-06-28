Core.ns('App.User.View');

App.User.View.MyAccountView = App.Core.View.View.extend({
    el: $('#user-main'),

    events: {
        'click #myaccount-save-btn': 'handleSaveClick'
    },

    initialize: function() {
        this.template = _.template($('#myaccount-template').html());
        this.loadingTemplate = _.template($('#loading-template').html());

        this.model = new App.User.Model.User({id: window.currentUser.id});

        _.bindAll(this, 'render');
        this.model.bind('change', this.render);
        this.render();

        this.model.fetch();
    },

    render: function() {
        if (this.model.get('username').length == 0) {
            this.renderLoadingSpinner();
        }
        else {
            this.renderUser();
        }

        return this;
    },

    renderLoadingSpinner: function() {
        $(this.el).html(this.loadingTemplate());
    },

    renderUser: function() {
        $(this.el).html(this.template(this.model.toJSON()));
    },

    handleSaveClick: function() {
        var self = this;

        $('#myaccount-save-btn').attr('disabled', 'disabled');

        this.model.save({
            firstname: $('#myaccount-firstname').val(),
            lastname: $('#myaccount-lastname').val(),
            email: $('#myaccount-email').val(),
            plainPassword: $('#myaccount-plainPassword').val(),
            passwordRepeat: $('#myaccount-passwordRepeat').val(),
            locale: $('#myaccount-locale').val(),
            roles: this.model.get('roles').join(',')
        }, {
            success: function(user, response) {
                self.showAlert("alert-success",
                    ExposeTranslation.get("core.general.notification.success.header"),
                    ExposeTranslation.get("core.general.notification.save.success"));

                $('#myaccount-save-btn').removeAttr('disabled');
            },
            error: function(response) {
                self.handleError(response);
                $('#myaccount-save-btn').removeAttr('disabled');
            }
        });
    }
});
