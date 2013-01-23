Core.ns("App.User.View");

App.User.View.TermsOfServiceView = App.Core.View.ModalView.extend({

    events: {
        'click .close-modal': 'closeModal'
    },

    initialize: function() {
        this.template = _.template($("#terms-of-service-template").html());
        _.bindAll(this, "render");
    },

    render: function() {
        $(this.el).html(this.template());

        this.showModal();
        return this;
    }
});