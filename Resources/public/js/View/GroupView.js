Core.ns('App.User.View');

App.User.View.GroupView = Backbone.View.extend({
    el: $('#group-main'),

    events: {
        "click #save-group": "save"
    },

    initialize: function() {

        this.template = _.template($('#group-edit-template').html());

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
            success: function(group, response) {
                $('.success').show();
                $('.error').hide();

                if (self.isNew) {
                    self.collection.add(group);
                }
            },
            error: function(group, response){
                if (response.responseText !== undefined) {
                    // Server error
                    $('#notification-error-body').append(response.responseText);
                }
                else {
                    // Client validation error
                    $.each(response, function(key, value) {
                        $('#group-'+key+'-div').addClass('error');
                        $('#group-'+key).addClass('error');
                        $('#notification-error-body').append(ExposeTranslation.get('rtxlabs.user.group.validation.'+key)+'<br/>');
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

        $('form [name^="group["]').each(function(index, dom) {

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
        $(".breadcrumb").append("<li>"+ExposeTranslation.get('rtxlabs.user.group.edit.header')+"</li>");
    }
});
