Core.ns('App.User.View');

App.User.View.UserEditView = App.Core.View.View.extend({
    el: $('#user-main'),

    events: {
        "click #save-user": "handleSave",
        "click #user-passwordRequired": "handlePasswordRequiredChange"
    },

    initialize: function() {
        this.template = _.template($('#user-edit-template').html());
        _.bindAll(this, 'render');
    },

    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));

        this.updateBreadcrumb();

        $(".chzn-select").chosen();
        $(".chzn-single").chosen();
        $('[rel=tooltip]').tooltip('hide');
        $('[rel=tooltip]').tooltip();

        this.updatePasswordFieldVisibility();

        return this;
    },

    handlePasswordRequiredChange: function() {
        this.updatePasswordFieldVisibility();
    },

    handleSave: function() {
        $('form:input').removeClass('error');
        $('form div').removeClass('error');
        $('#notification-error-body').html('');

        this.isNew = this.model.isNew();
        var self = this;

        this.model.save(this.getFormValues(), {
            wait: true,
            success: function(user, response) {
                self.defaultSuccess(user, response);
                App.User.router.navigate("#list", true);
            },
            error: self.defaultError,
            scope: self
        });
    },

    getFormValues: function() {
        var values = new Backbone.Model();
        var idPattern = /(\w.+)\-(\w*\d*\-*_*)/;

        $('form [name^="user["]').each(function(index, dom) {
            var el = $(dom);
            var result = dom.id.match(idPattern);

            var obj = "{\""+result[2] +"\":\""+el.val()+"\"}";
            var objInst = JSON.parse(obj);

            values.set(objInst);
        });

        if($('#user-passwordRequired').length > 0) {
            values.attributes.passwordRequired = $("#user-passwordRequired").attr('checked') == 'checked';
        }
        values.attributes.active = $("#user-active").attr('checked') == 'checked';
        if(values.attributes.roles !== 'null') {
            values.attributes.roles = values.attributes.roles.split(",");
        }
        else {
            values.attributes.roles = [];
        }
        return values.attributes;
    },

    updatePasswordFieldVisibility: function() {
        if($('#user-passwordRequired').length > 0) {
            var passwordRequired = $('#user-passwordRequired').attr('checked') == 'checked';

            if (passwordRequired) {
                $('#user-password-div').show();
                $('#user-passwordRepeat-div').show();
            }
            else {
                $('#user-password-div').hide();
                $('#user-passwordRepeat-div').hide();
            }
        }
    },

    updateBreadcrumb: function() {
        var lastSpanEl = $(".breadcrumb .divider").last().parent();
        lastSpanEl.next().remove();
        $(".breadcrumb").append("<li>"+Translator.get('rtxlabs.user.edit.header')+"</li>");
    }
});
