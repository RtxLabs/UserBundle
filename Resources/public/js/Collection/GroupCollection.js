Core.ns("App.User.Collection");

App.User.Collection.GroupCollection = App.Core.Collection.PaginatedCollection.extend({
    model: App.User.Model.Group,
    url: Routing.generate('rtxlabs_userbundle_group_list'),
    
    initialize: function() {
        this.filter = new App.User.Filter.UserFilter();
    },

    fetch: function(options) {
        typeof(options) != "undefined" || (options = {});
        typeof(options.data) != "undefined" || (options.data = {});

        options.data.filter = this.filter.toJSON();

        return App.Core.Collection.PaginatedCollection.prototype.fetch.call(this, options);
    }
});
