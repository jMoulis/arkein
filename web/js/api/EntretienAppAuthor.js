
(function (window, $, Routing) {
    'use strict';

    window.EntretienAppAuthor = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'submit',
            this._selector.newEntretienForm,
            this.handleNewFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.editEntretienForm,
            this.handleEditFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-detail-entretien',
            this.loadEditFormData.bind(this)
        );

        this.$wrapper.on(
            'hidden.bs.modal',
            '#editEntretienModal',
            this._emptyHtmlModalBody.bind(this)
        );

        this.$wrapper.on(
            'hidden.bs.modal',
            '.modal',
            this._clearForm.bind(this)
        );

        this.$wrapper.on(
            'change',
            '#guests',
            this.addSpanGuest.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-delete-guest',
            this.removeGuest.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-table-wrapper tbody tr',
            this._updateActualRow.bind(this)
        );

        this.loadEntretiens();

        this.loadInvitations();

    };

    $.extend(window.EntretienAppAuthor.prototype, {
        _selector: {
            newEntretienForm: '.js-new-entretien-form',
            editEntretienForm: '.js-edit-entretien-form',
            editModalBody: '#editEntretienModal .modal-body',
            editModalFooter: '#editEntretienModal .modal-footer',
            guestSelect: '#guests',
            youngSelect: '#young'
        },

        handleNewFormSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const self = this;
            const alertNoRecordFound = $('#myInterview').find('.js-table-wrapper tbody .alert');
            const $guests = $form.find('ul').children();
            /* Afin de pouvoir enregistrer des guests, il était nécessaire
             * de créer un objet avec les id des users
             * J'ai donc crée une liste avec les id des users que je récupère en forme
             * d'objet JSON qui sera transmis à l'API
             */
            const guestsObject = {};
            $.each($guests, function (key, fieldData) {
                guestsObject[fieldData.title] = Number(fieldData.id);
            });

            const formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                url: $form.data('url'),
                method: 'POST',
                data: JSON.stringify([formData, guestsObject]),
                success: function (data) {
                    $(alertNoRecordFound).remove();
                    self._clearForm();
                    self._addInterviewsRow(data);
                    $('#newEntretienModal').modal('hide');


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
            const entretienId = $form.data('entretien');
            const $guests = $form.find('.js-actual-guests').children();
            const tr = self.$wrapper.find('.js-main-content-created table tbody tr[title=id_'+  entretienId +']');

            /* Afin de pouvoir enregistrer des guests, il était nécessaire
             * de créer un objet avec les id des users
             * J'ai donc crée une liste avec les id des users que je récupère en forme
             * d'objet JSON qui sera transmis à l'API
             */
            const guestsObject = {};
            $.each($guests, function (key, fieldData) {
                guestsObject[fieldData.title] = Number(fieldData.id);
            });

            const formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            let arrayToBeSend = [formData, guestsObject];

            $.ajax({
                beforeSend: function(){
                    $('#editEntretienModal').modal('hide');
                },
                url: Routing.generate('entretien_edit', {id: entretienId}),
                method: 'POST',
                data: JSON.stringify(arrayToBeSend),
                success: function (data) {
                    self._updateActualRow(tr, data.date, data.objet, data.compteRendu);

                    self._clearForm();
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadEntretiens: function () {
            const self = this;
            const user = $('.js-entretien-wrapper').data('user');
            $.ajax({
                beforeSend: function(){
                    $('#myInterview').append('<span class="loading">Chargement...</span>');
                },
                url: Routing.generate('entretien_list_by_author', {id: user}),
                success: function (data) {
                    if($(data.items).length <= 0){
                        $('#myInterview').find('.loading').remove();
                        $('#myInterview').find('tbody').append('' +
                            '<div class="alert alert-success" role="alert">' +
                            'Aucun entretiens trouvés</div>');
                    } else {
                        $.each(data.items, function (key, entretien) {
                            $('#myInterview').find('.loading').remove();
                            self._addInterviewsRow(entretien);
                        })
                    }
                }
            })
        },

        loadInvitations: function () {
            const self = this;
            const user = $('.js-entretien-wrapper').data('user');
            $.ajax({
                beforeSend: function(){
                    $('#myInvitation').append("<span class='loading'>Chargement...</span>");
                },
                url: Routing.generate('entretien_list_by_invitation', {id: user}),
                success: function (data) {
                    if($(data.items).length <= 0){
                        $('#myInvitation').find('.loading').remove();
                        $('#myInvitation').find('tbody').append('' +
                            '<div class="alert alert-success" role="alert">' +
                            'Aucun entretiens trouvés</div>');
                    } else {
                        $.each(data.items, function (key, entretien) {
                            $('#myInvitation').find('.loading').remove();
                            self._addInvitationsRow(entretien);
                        })
                    }
                }
            })
        },

        loadEditFormData: function (e) {
            const self = this;
            const entretien = $(e.currentTarget).data('id');
            const user = $('.js-entretien-wrapper').data('user');
            const tplTextLoggedIn = $('#js-entretien-detail-template-logged').html();
            const tplTextNonLoggedIn = $('#js-entretien-detail-template-non-logged').html();

            $.ajax({
                beforeSend: function(){
                    $('#editEntretienModal .modal-footer').append("<p>Chargement...</p>");
                },
                url: Routing.generate('entretien_modal_detail', {id: entretien}),
                success: function (data) {
                    $('#loading').hide();
                    if(data.item.authorId !== user){
                        self._addEditForm(data.item, tplTextNonLoggedIn);
                    } else {
                        self._addEditForm(data.item, tplTextLoggedIn);
                        self.loadUsers();
                        self.loadGuests($('#young').val());
                    }
                }
            })
        },

        loadUsers: function () {
            const self = this;
            self._emptySelectYoung();
            self._emptySelectGuest();

            $.ajax({
                url: Routing.generate('young_list'),
                success: function (data) {
                    $.each(data.items, function (key, user) {
                        self._addSelect(user);
                    });
                }
            })
        },

        loadGuests: function (user) {
            const self = this;
            if(user) {
                $.ajax({
                    url: Routing.generate('guest_list', { user: user }),
                    success: function (data) {
                        self._emptySelectGuest();
                        self.$wrapper.find(self._selector.guestSelect).prop('disabled', false);
                        $.each(data.items, function (key, user) {
                            self._addGuestsSelect(user);
                        });
                    }
                })
            } else {
                self._emptySelectGuest();
                this.$wrapper.find(this._selector.guestSelect).prop('disabled', true);
            }
        },

        _emptyHtmlModalBody: function () {
            this.$wrapper.find(this._selector.editModalBody).html('');
            this.$wrapper.find(this._selector.editModalFooter).html('');
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newEntretienForm);

            $form.find(':input').each(function () {
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
            const $form = this.$wrapper.find(this._selector.newEntretienForm);
            $form.find('.js-field-error').remove();
            $form.find('.form-group').removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            $('.js-actual-guests').empty();
            const $form = this.$wrapper.find(this._selector.newEntretienForm);
            $form[0].reset();

        },

        _addEditForm: function (entretien, tplText) {
            const self = this;
            const tpl = _.template(tplText);
            const html = tpl(entretien);
            this.$wrapper.find(this._selector.editModalBody).append($.parseHTML(html));
            this.$wrapper.find(this._selector.editModalFooter).html('');
            self._loadStatusUpdateForm(entretien);
        },

        _addInterviewsRow: function (entretien) {
            const tplText = $('#js-entretien-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(entretien);
            $('#myInterview').find('tbody').append($.parseHTML(html));
        },

        _addInvitationsRow: function (entretien) {
            const tplText = $('#js-entretien-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(entretien);
            $('#myInvitation').find('tbody').append($.parseHTML(html));
        },

        _addSelect: function (user) {
            const tplText = $('#js-user-option-template').html();
            const tpl = _.template(tplText);
            const html = tpl(user);
            $('.js-select-young').append($.parseHTML(html));
        },

        _addGuestsSelect: function (user) {
            const tplText = $('#js-guests-option-template').html();
            const tpl = _.template(tplText);
            const html = tpl(user);
            this.$wrapper.find('.js-select-guest select').append($.parseHTML(html));
        },

        addSpanGuest: function (e) {
            const guest = '#guests option:selected';
            $('.js-actual-guests').append('' +
                '<li id="'+ $(guest).val()+'" title="'+ $(guest).text() +'">' +
                    '<span class="js-delete-guest badge badge-danger">X</span> ' +
                    '<span class="badge badge-warning">'+ $(guest).text() +'</span>' +
                '</li>'
            );
        },

        removeGuest: function (e) {
            $(e.currentTarget).parent('li').remove();
        },

        _emptySelectGuest: function () {
            this.$wrapper.find(this._selector.guestSelect).html('<option value="" selected="selected">Ajouter des participants</option>');
        },

        _emptySelectYoung: function () {
            this.$wrapper.find(this._selector.youngSelect).html('<option value="" selected="selected">Sélectionner un jeune</option>');
        },

        _updateActualRow: function (tr, date, objet, compteRendu) {
            const $columnDate = $(tr).find('td').first();
            const $columnObjet = $columnDate.next();
            const $columnCompteRendu = $columnObjet.next();

            $columnDate.text(date);
            $columnObjet.text(objet);
            $columnCompteRendu.text(compteRendu);
        },

        _loadStatusUpdateForm: function (status) {
            const tplText = $('#js-status-update').html();
            const tpl = _.template(tplText);
            const html = tpl(status);
            this.$wrapper.find(this._selector.editModalFooter).append($.parseHTML(html));
        }
    });
})(window, jQuery, Routing);