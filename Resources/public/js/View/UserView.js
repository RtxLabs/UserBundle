Core.ns('App.User.View');

App.User.View.UserView = Backbone.View.extend({
    el: $('#user-main'),

    events: {
        "click #save-user": "save"
    },

    initialize: function() {

        this.template = _.template($('#user-edit-template').html());

        _.bindAll(this, 'render');
        this.render();
    },

    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));

        this.updateBreadcrumb();

        $(".chzn-select").chosen();

        return this;
    },

    save: function() {
        $('form:input').removeClass('error');
        $('form div').removeClass('error');
        $('#notification-error-body').html('');

        this.isNew = this.model.isNew();
        var self = this;

        this.model.save(this.getFormValues(), {
            success: function(user, response) {
                $('.success').show();
                $('.error').hide();

                if (self.isNew) {
                    self.collection.add(user);
                }
            },
            error: function(user, response){
                if (response.responseText !== undefined && response.status != 406) {
                    // Server error
                    $('#notification-error-body').append(response.responseText);
                }
                else {
                    if (response.responseText !== undefined) {
                        response = JSON.parse(response.responseText);
                    }

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

    getFormValues: function() {
        var values = new Backbone.Model();
        var idPattern = /(\w.+)\-(\w*\d*\-*_*)/;

        var collection = $('form [name^="user["]');

        $('form [name^="user["]').each(function(index, dom) {

            var el = $(dom);
            var result = dom.id.match(idPattern);

            var obj = "{\""+result[2] +"\":\""+el.val()+"\"}";
            var objInst = JSON.parse(obj);

            values.set(objInst);
        });

        return values.attributes;
    },

    updateBreadcrumb: function() {
        var lastSpanEl = $(".breadcrumb .divider").last().parent();
        lastSpanEl.next().remove();
        $(".breadcrumb").append("<li>"+ExposeTranslation.get('rtxlabs.user.edit.header')+"</li>");
    }
});
