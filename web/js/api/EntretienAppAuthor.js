
(function (window, $, Routing) {
    'use strict';

    window.EntretienAppAuthor = function ($wrapper) {
        this.$wrapper = $wrapper;
        const self = this;

        this.$wrapper.on(
            'submit',
            this._selector.newEntretienForm,
            this.handleNewEntretienSubmit.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.editEntretienForm,
            this.handleEditFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.newCompteRendu,
            this.handleNewCompteRendu.bind(this)
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
            'click',
            '.js-detail-compteRendu', function (e) {
                $('#editEntretienModal').modal('toggle');
                let entretienId = $(e.currentTarget).closest('.js-edit-entretien-form').data('entretien');
                let modal = $('.js-modal-viewer h5').attr('data-id', entretienId);
                self.loadPdf(entretienId);
            }
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

        this.$wrapper.on(
            'click',
            '.js-saisir-compteRendu',
            this.setInputIdEntretienNewCompteRenduForm.bind(this)
        );

        this.loadEntretiens();

        this.loadInvitations();

    };

    $.extend(window.EntretienAppAuthor.prototype, {
        _selector: {
            newEntretienForm: '.js-new-entretien-form',
            editEntretienForm: '.js-edit-entretien-form',
            editEntretienModal: '#editEntretienModal',
            editModalBody: '#editEntretienModal .modal-body',
            editModalFooter: '#editEntretienModal .modal-footer',
            guestSelect: '#guests',
            youngSelect: '#young',
            tabInvitation: '#myInvitation' ,
            tabInterview: '#myInterview',
            newModal: '#newEntretienModal',
            newModalFooter: '#newEntretienModal .modal-footer',
            newCompteRendu: '#js-compte-rendu-form',
            newCompteRenduModal: '#saisirCompteRenduModal',
            newCompteRenduModalFooter: '#saisirCompteRenduModal .modal-footer',
            editCompteRenduModalBody: '#editCompteRenduModal .modal-body',
            modalFooter: '.modal-footer',
            modalForm: '.modal-body form',
            tbodyEntretiens: '.tab-content tbody',
            tbodyInvitations: '.tab-content tbody'
        },

        handleNewEntretienSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const self = this;

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
                beforeSend: function(){
                    self.beforeSendAction();
                },
                url: $form.data('url'),
                method: 'POST',
                data: JSON.stringify([formData, guestsObject]),
                success: function (data) {
                    self.sucessSendAction();
                    self._clearForm();
                    self._addInterviewsRow(data);
                    $(self._selector.newModal).modal('toggle');
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        handleNewCompteRendu: function (e) {
            e.preventDefault();
            const self = this;
            const $form = $(e.currentTarget);
            const entretienId = $form.find('.js-entretien-input').val();
            const $compteRenduBtn = self.$wrapper.find('.js-main-content-created table tbody tr[title=id_'+  entretienId +'] .js-saisir-compteRendu');
            const $detailBtn = self.$wrapper.find('.js-main-content-created table tbody tr[title=id_'+  entretienId +'] .js-detail-entretien');

            const formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });
            $.ajax({
                beforeSend: function(){
                    self.beforeSendAction();
                },
                url: Routing.generate('compterendu_new'),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    self.sucessSendAction();
                    self._clearForm();
                    $(self._selector.newCompteRenduModal).modal('toggle');
                    $compteRenduBtn.remove();
                    $detailBtn.removeClass('btn-warning').addClass('btn-success');
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self.sucessSendAction();
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
                    self.beforeSendAction();
                },
                url: Routing.generate('entretien_edit', {id: entretienId}),
                method: 'POST',
                data: JSON.stringify(arrayToBeSend),
                success: function (data) {
                    self._updateActualRow(tr, data.date, data.objet, data.odj);
                    self.sucessSendAction();
                    self._clearForm();
                    $(self._selector.editEntretienModal).modal('toggle');
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
                    $(self._selector.tabInterview).append('<span class="loading">Chargement...</span>');
                },
                url: Routing.generate('entretien_list_by_author', {id: user}),
                success: function (data) {
                    if($(data.items).length <= 0){
                        $(self._selector.tabInterview).find('.loading').remove();
                    } else {
                        $.each(data.items, function (key, entretien) {
                            $(self._selector.tabInterview).find('.loading').remove();
                            self._addInterviewsRow(entretien);
                        });
                    }
                }
            })
        },

        loadInvitations: function () {
            const self = this;
            const user = $('.js-entretien-wrapper').data('user');
            $.ajax({
                beforeSend: function(){
                    $(self._selector.tabInvitation).append("<span class='loading'>Chargement...</span>");
                },
                url: Routing.generate('entretien_list_by_invitation', {id: user}),
                success: function (data) {
                    if($(data.items).length <= 0){
                        $(self._selector.tabInvitation).find('.loading').remove();
                        $(this._selector.tbody).append('' +
                            '<div class="js-nodata-found alert alert-success" role="alert">' +
                            'Aucun entretiens trouvés</div>');
                    } else {
                        $.each(data.items, function (key, entretien) {
                            $(self._selector.tabInvitation).find('.loading').remove();
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
                    $(self._selector.editModalFooter).append("<span class='loading'>Chargement...</span>");
                },
                url: Routing.generate('entretien_modal_detail', {id: entretien}),
                success: function (data) {
                    $('.loading').remove();
                    if(data.item.compteRendu){
                        self._addEditForm(data.item, tplTextNonLoggedIn);
                        $('#odj_edit_2').prop('disabled', true);
                    } else if(data.item.authorId !== user){
                        self._addEditForm(data.item, tplTextNonLoggedIn);
                        self._loadStatusUpdateForm();
                        $('#odj_edit_2').prop('disabled', true);
                    } else {
                        self._addEditForm(data.item, tplTextLoggedIn);
                        self.loadUsers();
                        self.loadGuests($('#young').val());
                    }
                }
            })
        },

        loadPdf: function (entretienId) {
            const self = this;
            $('#pdfViewer').modal('show');
            $.ajax({
                beforeSend: function(){
                    $(self._selector.editModalFooter).append("<span class='loading'>Chargement...</span>");
                },
                url: Routing.generate('entretien_modal_detail', {id: entretienId}),
                success: function (data) {
                    const lienpdf = data.item.compteRenduLien;
                    PDFObject.embed(lienpdf, '#pdfViewer .modal-body');
                }
            });
            $('#pdfViewer .modal-body').css({ height: '50rem'});

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
            $('body').removeClass('.modal-open');
            this.$wrapper.find(this._selector.editModalBody).html('');
            this.$wrapper.find(this._selector.editModalFooter).html('');
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            const $form = this.$wrapper.find('.modal-body').find('form');
            $form.find(':input').each(function () {
                const fieldName = $(this).attr('name');
                if (!errorData[fieldName]){
                    return;
                }

                const $error = $('<span class="js-field-error text-danger"></span>');
                $error.html(errorData[fieldName]);
                $form.prepend($error);
                $form.addClass('has-error');
            });
        },

        _removeFormErrors: function () {
            const $form = this.$wrapper.find('.modal-body').find('form');
            $form.find('.js-field-error').remove();
            $form.removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            const $form = this.$wrapper.find('.modal-body').find('form');
            $.each($form, function (key, form) {
                form.reset();
            });
            $('.js-actual-guests').empty();

            $('.saving').remove();
            $(this._selector.newCompteRenduModal).find('.js-btn-submit-compterendu').prop("disabled", false);
        },

        _addEditForm: function (entretien, tplText) {
            const tpl = _.template(tplText);
            const html = tpl(entretien);
            this.$wrapper.find(this._selector.editModalBody).append($.parseHTML(html));
            this.$wrapper.find(this._selector.editModalFooter).html('');

        },

        _addInterviewsRow: function (entretien) {
            const tplText = $('#js-entretien-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(entretien);
            $(this._selector.tabInterview).find('tbody').prepend($.parseHTML(html));
        },

        _addInvitationsRow: function (entretien) {
            const tplText = $('#js-entretien-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(entretien);
            $(this._selector.tabInterview).find('tbody').append($.parseHTML(html));
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

        _loadStatusUpdateForm: function () {
            const tplText = $('#js-status-update').html();
            const tpl = _.template(tplText);
            const html = tpl(status);
            this.$wrapper.find(this._selector.editModalFooter).append($.parseHTML(html));
        },

        setInputIdEntretienNewCompteRenduForm: function (e) {
            const entretien = $(e.currentTarget).data('id');
            $('.js-entretien-input').val(entretien);
        },

        beforeSendAction: function () {
            $(this._selector.modalFooter).find('.saving').remove();
            $(this._selector.modalForm).find('button').prop("disabled", false);
            $(this._selector.modalFooter).append("<span class='saving'>Enregistrement...</span>");
            $(this._selector.modalForm).find('button').prop("disabled", true);
        },

        sucessSendAction : function () {
            $(this._selector.modalFooter).find('.loading').remove();
            $(this._selector.modalFooter).find('.saving').remove();

            $(this._selector.modalForm).find('button').prop("disabled", false);
            $(this._selector.tbody).find('.alert').remove();
        }

    });
})(window, jQuery, Routing);