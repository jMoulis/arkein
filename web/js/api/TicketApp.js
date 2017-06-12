(function (window, $, Routing) {
    'use strict';

    window.TicketApp = function ($wrapper) {
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

        this.$wrapper.on(
            'submit',
            this._selector.editTicketForm,
            this.handleEditFormSubmit.bind(this)
        );

        this.loadTicketsCreated();

    };

    $.extend(window.TicketApp.prototype, {
        _selector: {
            newTicketForm: '.js-new-ticket-form',
            editTicketForm: '.js-edit-ticket-form',
            myTicket: '#myTicket',
            myAttribution: '#myAttribution'
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

        handleEditFormSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const self = this;
            const ticketid = $('h1').data('id');

            let formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                url: Routing.generate('api_ticket_edit', {id: ticketid}),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    $('#myModal').modal('hide');
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
                beforeSend: function(){
                    $('#myTicket').append('<span class="loading">Chargement...</span>');
                },
                url: Routing.generate('api_ticket_created_list'),
                success: function (data) {
                    const $connectedUser = $('body').data('user');
                    if($(data.items).length <= 0){
                        $(self._selector.myTicket).find('.loading').remove();
                        $(self._selector.myTicket).find('tbody').append('' +
                            '<div class="alert alert-success" role="alert">' +
                            'Aucun tickets trouvés</div>');
                    } else {
                        $.each(data.items, function (key, ticket) {
                            $(self._selector.myTicket).find('.loading').remove();
                            self._addCreatedRow(ticket);
                            if($('body').data('user') != ticket.auteurId) {
                                $('.js-tickets tbody tr#ticket_auteur_'+ ticket.auteurId +'').css('backgroundColor', '#b2e5ff');
                            }
                        })
                    }
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
            $('.js-new-ticket-form')[0].reset();

        },

        _addCreatedRow: function (ticket) {
            const tplText = $('#js-ticket-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(ticket);
            $(this._selector.myTicket).find('tbody').append($.parseHTML(html));
        },

        _addAttributedRow: function (ticket) {
            const tplText = $('#js-ticket-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(ticket);
            $(this._selector.myAttribution).find('tbody').append($.parseHTML(html));
        }

    });
})(window, jQuery, Routing);