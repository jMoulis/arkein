(function (window, $, Routing) {
    'use strict';

    window.TicketAppCreated = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-delete-ticket',
            this.handleTicketDelete.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.newTicketForm,
            this.handleNewFormSubmit.bind(this)
        );

        this.loadTicketsCreated();

    };

    $.extend(window.TicketAppCreated.prototype, {
        _selector: {
            newTicketForm: '.js-new-ticket-form'
        },

        handleTicketDelete: function (e) {
            e.preventDefault();
            const $link = $(e.currentTarget);

            $link.addClass('text-danger');

            const deleteUrl =  $link.data('url');
            const $row =  $link.closest('tr');

            $.ajax({
                url: deleteUrl,
                method: 'DELETE',
                success: function () {
                    $row.fadeOut('normal', function () {
                        $(this).remove();
                    });
                }
            })
        },

        handleNewFormSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const self = this;

            let formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                url: $form.data('url'),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    self._clearForm();
                    $('#ticket').val(data.id);
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadTicketsCreated: function () {
            const self = this;
            $.ajax({
                url: Routing.generate('api_ticket_created_list'),
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

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newTicketForm);

            $form.find(':input').each(function () {
                const fieldName = $(this).attr('name');
                const $wrapper = $('.js-new-ticket-form');
                if (!errorData[fieldName]){
                    return;
                }

                const $error = $('<span class="js-field-error text-danger"></span>');
                $error.html(errorData[fieldName]);
                $wrapper.prepend($error);
                $wrapper.addClass('has-error');
            });
        },

        _removeFormErrors: function () {
            const $form = $('.js-new-ticket-form');
            $form.find('.js-field-error').remove();
            $form.removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newTicketForm);
            $('.js-new-ticket-form')[0].reset();

        },

        _addRow: function (ticket) {
            const tplText = $('#js-ticket-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(ticket);
            this.$wrapper.find('tbody').append($.parseHTML(html));
        }

    });
})(window, jQuery, Routing);