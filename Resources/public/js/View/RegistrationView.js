Core.ns('App.User.View');

App.User.View.RegistrationView = App.Core.View.ListView.extend({
    el: $('#registration-main'),

    events: {
        "click #save-registration": "handleSave"
    },

    initialize: function() {
        this.template = _.template($('#registration-template').html());
        _.bindAll(this, 'render');
    },

    render: function() {
        $(this.el).html(this.template());

        return this;
    },

    handleSave: function() {
        var self = this;
        $('form:input').removeClass('error');
        $('form div').removeClass('error');

        this.model.save(this.getFormValues(), {
            url: 'register',
            success: function(user, response) {
                if(response.success == false &&
                    response.message.status == '304') {
                    window.location.href = response.message.url;
                }
                else {
                    self.defaultSuccess(self);
                }
            },
            error: self.defaultError,
            scope: self
        });
    },


    getFormValues: function() {
        var values = new Backbone.Model();
        var idPattern = /(\w.+)\-(\w*\d*\-*_*)/;

        $('form [name^="registration["]').each(function(index, dom) {

            var el = $(dom);
            var result = dom.id.match(idPattern);

            var obj = "{\""+result[2] +"\":\""+el.val()+"\"}";
            var objInst = JSON.parse(obj);

            values.set(objInst);
        });

        values.attributes.passwordRequired = $("#user-passwordRequired").attr('checked') == 'checked';
        values.attributes.admin = $("#user-admin").attr('checked') == 'checked';
        return values.attributes;
    }
});
