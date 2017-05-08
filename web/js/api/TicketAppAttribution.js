(function (window, $, Routing) {
    'use strict';

    window.TicketAppAttribution = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.loadTicketsAttributed();
    };

    $.extend(window.TicketAppAttribution.prototype, {
        _selector: {
            newTicketForm: '.js-new-ticket-form'
        },

        loadTicketsAttributed: function () {
            var self = this;
            $.ajax({
                url: Routing.generate('api_ticket_attributed_list'),
                success: function (data) {
                    $.each(data.items, function (key, ticket) {
                        self._addRow(ticket);
                    })
                },
                error: function (jqXHR) {
                    console.log(jqXHR.responseText);
                }
            })
        },

        _addRow: function (ticket) {
            var tplText = $('#js-ticket-row-template').html();
            var tpl = _.template(tplText);
            var html = tpl(ticket);
            this.$wrapper.find('tbody').append($.parseHTML(html));
        }

    });
})(window, jQuery, Routing);