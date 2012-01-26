Core.ns('App.User.View');

App.User.View.UserView = Backbone.View.extend({
    el: $('#user-main'),

    events: {
        "click #save-user": "save",
        "change input": "formChanged",
        "change select": "formChanged"
    },

    initialize: function() {

        this.template = _.template($('#user-edit-template').html());
        this.changedAttributes = new Backbone.Model();

        _.bindAll(this, 'render');
        this.render();
    },

    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));

        this.updateBreadcrumb();
        return this;
    },

    save: function() {
        $('form:input').removeClass('error');
        $('form div').removeClass('error');
        $('#notification-error-body').html('');

        this.model.save(this.changedAttributes.attributes, {
            success: function(user, response) {
                $('.success').show();
                $('.error').hide();
            },
            error: function(user, response){
                if (response.responseText !== undefined) {
                    // Server error
                    $('#notification-error-body').append(response.responseText);
                }
                else {
                    // Client validation error
                    $.each(response, function(key, value) {
                        $('#user-'+key+'-div').addClass('error');
                        $('#user-'+key).addClass('error');
                        $('#notification-error-body').append(ExposeTranslation.get('rtxlabs.user.validation.'+key)+'<br/>');
                    });
                }


                $('.success').hide();
                $('.error').show();
            }
        });
    },

    updateBreadcrumb: function() {
        $(".breadcrumb").append("<li>"+ExposeTranslation.get('rtxlabs.user.edit.header')+"</li>");
    },

    formChanged: function(event) {
        var changed = event.currentTarget;
        var value = $("#"+changed.id).val();

        var idPattern = /(\w.+)\-(\w*\d*\-*_*)/;
        var result = changed.id.match(idPattern);

        var obj = "{\""+result[2] +"\":\""+value+"\"}";
        var objInst = JSON.parse(obj);
        this.changedAttributes.set(objInst);
    }
});
