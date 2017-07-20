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

        this.$wrapper.on(
            'hidden.bs.modal',
            '.modal',
            this._clearForm.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-new-ticket',
            this.loadNewForm.bind(this));

        this.loadTicketsCreated();
    };

    $.extend(window.TicketApp.prototype, {
        _selector: {
            newTicketForm: '.js-new-ticket-form',
            editTicketForm: '.js-edit-ticket-form',
            myTicket: '#myTicket',
            myAttribution: '#myAttribution',
            newModal: '#newTicketModal',
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
                url: Routing.generate('api_ticket_new'),
                method: 'POST',
                data: JSON.stringify(formData)
            }).then(function (data) {
                self._addCreatedRow(data);
                self._clearForm();
                $('#ticket').val(data.id);
                $(self._selector.newModal).modal('toggle');

            }).catch(function (jqXHR) {
                const errorData = JSON.parse(jqXHR.responseText);
                self._mapErrorsToForm(errorData.errors);
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
        _loadThemesSelect: function() {
            const self = this;
            $.ajax({
                url: Routing.generate('api_ticket_themes_list')
            }).then(function(data){
                $.each(data, function (key, theme) {
                    $.each(theme, function(key, value){
                        $('#objet').append('<option value="'+ value.value +'">'+ value.label +'</option>');
                    })
                });
            }).catch(function (jqXHR) {

            })
        },
        loadUsers: function () {
            const self = this;
            self._emptySelectYoung();
            $.ajax({
                url: Routing.generate('young_list')
            }).then(function(data){
                $.each(data.items, function (key, user) {
                    self._addSelect(user);
                });
            })
        },
        loadNewForm: function(){
            const self = this;

            self.loadUsers();
            self._hideSelectBoxYoungster();
        },
        loadTicketsCreated: function () {
            const self = this;
            $.ajax({
                beforeSend: function(){
                    $('#myTicket').append('<span class="loading">Chargement...</span>');
                },
                url: Routing.generate('api_ticket_created_list'),
            }).then(function(data){
                self._loadThemesSelect();
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
                    });
                }
            }).catch(function (jqXHR) {

            })
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newTicketForm);

            $form.find('.form-control').each(function () {
                const fieldName = $(this).attr('name');
                const $wrapper = $(this).closest('.form-group');
                if (!errorData[fieldName]){
                    return;
                }

                const $error = $('<span class="js-field-error text-danger"></span>');

                $error.html(errorData[fieldName]);
                $wrapper.append($error);
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
            $(this._selector.myTicket).find('tbody').prepend($.parseHTML(html));
        },

        _addSelect: function (user) {
            const tplText = $('#js-user-option-template').html();
            const tpl = _.template(tplText);
            const html = tpl(user);
            $('#aboutWho').append($.parseHTML(html));
        },
        _emptySelectYoung: function () {
            $('#aboutWho').html('<option value="" selected="selected">Sélectionner un jeune</option>');
        },
        _hideSelectBoxYoungster: function(){
            const $selectBox = $('.js-new-ticket-form').find('#aboutWho');
            if($('body').data("role") === 'ROLE_YOUNGSTER'){
                $selectBox.parent().remove();
            }

        }

    });
})(window, jQuery, Routing);