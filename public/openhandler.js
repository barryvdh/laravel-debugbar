if (typeof(PhpDebugBar) == 'undefined') {
    // namespace
    var PhpDebugBar = {};
}

(function($) {

    PhpDebugBar.OpenHandler = PhpDebugBar.Widget.extend({

        className: 'phpdebugbar-openhandler',

        defaults: {
            items_per_page: 20
        },

        render: function() {
            var self = this;
            
            this.$el.appendTo('body').hide();
            this.$closebtn = $('<a href="javascript:"><i class="icon-remove"></i></a>');
            this.$table = $('<tbody />');
            $('<div class="header">PHP DebugBar | Open</div>').append(this.$closebtn).appendTo(this.$el);
            $('<table><thead><tr><th>ID</th><th>URL</th><th>Date</th><th>IP</th></tr></thead></table>').append(this.$table).appendTo(this.$el);
            this.$actions = $('<div class="actions" />').appendTo(this.$el);

            this.$closebtn.on('click', function() {
                self.hide();
            });

            this.$loadmorebtn = $('<a href="javascript:">Load more</a>')
                .appendTo(this.$actions)
                .on('click', function() {
                    self.find(self.last_find_request, self.last_find_request.offset + self.get('items_per_page'), self.handleFind.bind(self));
                });

            this.$showonlycurrentbtn = $('<a href="javascript:">Show only current URL</a>')
                .appendTo(this.$actions)
                .on('click', function() {
                    self.$table.empty();
                    self.find({uri: window.location.pathname}, 0, self.handleFind.bind(self));
                });

            this.$showallbtn = $('<a href="javascript:">Show all</a>')
                .appendTo(this.$actions)
                .on('click', function() {
                    self.refresh();
                });

            this.$clearbtn = $('<a href="javascript:">Delete all</a>')
                .appendTo(this.$actions)
                .on('click', function() {
                    self.clear(function() {
                        self.hide();
                    });
                });

            this.$overlay = $('<div class="phpdebugbar-openhandler-overlay" />').hide().appendTo('body');
            this.$overlay.on('click', function() {
                self.hide();
            });
        },

        refresh: function() {
            this.$table.empty();
            this.$loadmorebtn.show();
            this.find({}, 0, this.handleFind.bind(this));
        },

        handleFind: function(data) {
            var self = this;
            $.each(data, function(i, meta) {
               var a = $('<a href="javascript:" />')
                    .text(meta['id'])
                    .on('click', function(e) {
                        self.hide();
                        self.load(meta['id'], function(data) {
                            self.callback(meta['id'], data);
                        });
                        e.preventDefault();
                    });

                $('<tr />')
                    .append($('<td />').append(a))
                    .append('<td>' + meta['uri'] + '</td>')
                    .append('<td>' + meta['datetime'] + '</td>')
                    .append('<td>' + meta['ip'] + '</td>')
                    .appendTo(self.$table);
            });
            if (data.length < this.get('items_per_page')) {
                this.$loadmorebtn.hide();
            }
        },

        show: function(callback) {
            this.callback = callback;
            this.$el.show();
            this.$overlay.show();
            this.refresh();
        },

        hide: function() {
            this.$el.hide();
            this.$overlay.hide();
        },

        find: function(filters, offset, callback) {
            var data = $.extend({}, filters, {max: this.get('items_per_page'), offset: offset || 0});
            this.last_find_request = data;
            $.getJSON(this.get('url'), data, callback);
        },

        load: function(id, callback) {
            $.getJSON(this.get('url'), {op: "get", id: id}, callback);
        },

        clear: function(callback) {
            $.getJSON(this.get('url'), {op: "clear"}, callback);
        }

    });

})(jQuery);