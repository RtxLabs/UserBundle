Core.ns('App.User.View');

App.User.View.GroupEditView = App.Core.View.View.extend({
    el: $('#user-main'),

    events: {
        "click #save-group": "handleSave"
    },

    initialize: function() {
        this.template = _.template($('#group-edit-template').html());
        _.bindAll(this, 'render');
    },

    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));

        $(".chzn-select").chosen();

        return this;
    },

    handleSave: function() {
        $('form:input').removeClass('error');
        $('form div').removeClass('error');
        $('#notification-error-body').html('');

        this.isNew = this.model.isNew();
        var self = this;

        this.model.save(this.getFormValues(), {
            wait: true,
            success: function(group, response) {
                self.defaultSuccess(group, response);

                if (self.isNew) {
                    self.collection.add(group);
                }

                App.User.router.navigate("#group/list", true);
            },
            error: function(group, response){
                if (response.responseText !== undefined && response.status != 406) {
                    // Server error
                    $('#notification-error-body').append(response.responseText);
                }
                else {
                    response = JSON.parse(response.responseText);

                    $.each(response, function(key, value) {
                        $('#group-'+key+'-div').addClass('error');
                        $('#group-'+key).addClass('error');
                        $('#notification-error-body').append(Translator.get('rtxlabs.user.group.validation.'+key)+'<br/>');
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

        $('form [name^="group["]').each(function(index, dom) {

            var el = $(dom);
            var result = dom.id.match(idPattern);

            var obj = "{\""+result[2] +"\":\""+el.val()+"\"}";
            var objInst = JSON.parse(obj);

            values.set(objInst);
        });

        return values.attributes;
    }
});
