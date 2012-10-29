Core.ns('App.User.View');

App.User.View.RegistrationView = Backbone.View.extend({
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
        $('form:input').removeClass('error');
        $('form div').removeClass('error');
        $('#notification-error-body').html('');

        this.model.save(this.getFormValues(), {
            url: 'register',
            success: function(user, response) {
                $('.alert-success').show();
                $('.alert-error').hide();
            },
            error: function(user, response){
                if (response.responseText !== undefined && response.status != 406) {
                    $('#notification-error-body').append(response.responseText);
                }
                else {
                    response = JSON.parse(response.responseText);

                    $.each(response, function(key, value) {
                        $('#user-'+key+'-div').addClass('error');
                        $('#user-'+key).addClass('error');
                        $('#notification-error-body').append(ExposeTranslation.get('rtxlabs.user.validation.'+key)+'<br/>');
                    });
                }
                $('.alert-success').hide();
                $('.alert-error').show();
            }
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
