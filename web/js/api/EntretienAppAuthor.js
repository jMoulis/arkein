
(function (window, $, Routing) {
    'use strict';

    window.EntretienAppAuthor = function ($wrapper) {
        this.$wrapper = $wrapper;

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
            '.js-detail-compteRendu',
            this.loadDetailCompteRendu.bind(this)
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
            'change',
            '.js-select-young',
            this._selectGuestByInterviewee.bind(this)
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

        this.$wrapper.on(
            'click',
            '.js-new-entretien-btn',
            this.loadUsers.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-participe',
            function () {
                $('#status').val(1);
            });

        this.$wrapper.on(
            'click',
            '.js-participe-pas',
            function () {
                $('#status').val(2);
            });

        this.loadEntretiens();
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
            tbodyInvitations: '.tab-content tbody',
        },

        handleNewEntretienSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const self = this;

            const $guests = $form.find('ul').children();
            /** Afin de pouvoir enregistrer des guests, il était nécessaire
             * de créer un objet avec les id des users
             * J'ai donc crée une liste avec les id des users que je récupère en forme
             * d'objet JSON qui sera transmis à l'API
             *
             */
            const guestsObject = {};
            $.each($guests, function (key, fieldData) {
                guestsObject[Number($(fieldData).data('user'))] = fieldData.title;
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
                data: JSON.stringify([formData, guestsObject])
            }).then(function(data){
                self.sucessSendAction();
                self._clearForm();
                self._addInterviewsRow(data);
                $(self._selector.newModal).modal('toggle');
            }).catch(function (jqXHR) {
                console.log('catch');
                const errorData = JSON.parse(jqXHR.responseText);
                self._mapErrorsToForm(errorData.errors);
                $(self._selector.modalForm).find('button').prop("disabled", false);
            })
        },

        handleNewCompteRendu: function (e) {
            e.preventDefault();
            const self = this;
            const $form = $(e.currentTarget);

            // This input value is set when the compte-rendu btn is clicked
            // adn get the entretien value.
            const entretienId = $form.find('.js-entretien-input').val();

            // Get the action's btn to apply different rules in the then method
            const $compteRenduBtn = $('#entretien_id_'+ entretienId +'').find('td .js-saisir-compteRendu');
            const $detailBtn = $compteRenduBtn.closest('tr').find('td .js-detail-entretien');

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
                data: JSON.stringify(formData)
            }).then(function(){
                self.sucessSendAction();
                self._clearForm();
                $(self._selector.newCompteRenduModal).modal('toggle');
                $compteRenduBtn.remove();
                $detailBtn
                    .removeClass('btn-success')
                    .addClass('btn-info')
                    .empty()
                    .append('<i class="fa fa-check-square-o" aria-hidden="true"></i>'
                    );
            }).catch(function(jqXHR){
                const errorData = JSON.parse(jqXHR.responseText);
                $(this._selector.modalForm).find('button').prop("disabled", false);
                self._mapErrorsToForm(errorData.errors);
            })
        },

        handleEditFormSubmit: function (e) {
            e.preventDefault();
            const self = this;
            const $form = $(e.currentTarget);

            const entretienId = $form.data('entretien');
            const $guests = $form.find('.js-actual-guest-edit').children();

            const tr = self.$wrapper.find('.js-main-content-created table tbody tr[title=entretien_id_'+  entretienId +']');

            /** Afin de pouvoir enregistrer des guests, il était nécessaire
             * de créer un objet avec les id des users
             * J'ai donc crée une liste avec les id des users que je récupère en forme
             * d'objet JSON qui sera transmis à l'API
             */
            const guestsObject = {};
            $.each($guests, function (key, fieldData) {
                guestsObject[Number($(fieldData).data('user'))] = fieldData.title;
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
                data: JSON.stringify(arrayToBeSend)
            }).then(function(data){
                self._updateActualRow(tr, data.date, data.objet, data.odj);
                self.sucessSendAction();
                self._clearForm();
                $(self._selector.editEntretienModal).modal('toggle');
            }).catch(function (jqXHR) {
                console.log('catch');
                const errorData = JSON.parse(jqXHR.responseText);
                $(self._selector.modalForm).find('button').prop("disabled", false);
                self._mapErrorsToForm(errorData.errors);
            })
        },

        loadEntretiens: function () {
            const self = this;

            // This is the default value used to display the interviews
            let $userToSendToAjax = $('body').data('user');

            let routeName = 'entretien_list_by_author';

            /**
            * This variable stores the id of the displayed
            * membered. This variable will be defined only if
            * the user is on the show interviews action. So if it is
            * we change the route fos value and the user id to filter
            * the interviews by the diplayed user.
            * */
            const $isYoung = $('#member-detail').data('user');

            if($isYoung !== undefined){
                $userToSendToAjax = $isYoung;
                routeName = 'entretien_list_by_young';
            }

            $.ajax({
                beforeSend: function(){
                    $(self._selector.tabInterview).append('<span class="loading">Chargement...</span>');
                },
                url: Routing.generate(routeName, {id: $userToSendToAjax})
            }).then(function(data){
                if($(data.items).length <= 0){
                    $(self._selector.tabInterview).find('.loading').remove();
                } else {
                    $.each(data.items, function (key, entretien) {
                        $(self._selector.tabInterview).find('.loading').remove();
                        self._addInterviewsRow(entretien);
                    });
                }
            })
        },

        loadEditFormData: function (e) {
            e.preventDefault();
            const self = this;
            const entretien = $(e.currentTarget).data('id');
            const user = $('body').data('user');

            $.ajax({
                beforeSend: function(){
                    $(self._selector.editModalFooter).append("<span class='loading'>Chargement...</span>");
                },
                url: Routing.generate('entretien_modal_detail', {id: entretien}),
            }).then(function(data){
                $('.loading').remove();
                self._addEditForm(data.item);
                self.loadUsers();
                self.loadGuests($('#young').val());
                /**
                 * 1- Check if the interview is active
                 * --> We disable everything
                 * 2- Check if the user isn't the author,
                 * --> We load the check in presence
                 * */
                if(data.item.isArchived === true){
                    self._disabledControlEditForm();
                } else if(data.item.authorId !== user){
                    self._disabledControlEditForm();
                    self._loadStatusUpdateForm();
                }
            }).catch(function(){
                console.log('Une erreur')
            })
        },

        loadUsers: function () {
            const self = this;
            self._emptySelectYoung();
            self._emptySelectGuest();
            $.ajax({
                url: Routing.generate('young_list')
            }).then(function(data){
                $.each(data.items, function (key, user) {
                    self._addSelect(user);
                });
            })
        },

        loadGuests: function (user) {
            const self = this;
            if(user) {
                $.ajax({
                    url: Routing.generate('guest_list', { user: user })
                }).then(function(data){
                    self._emptySelectGuest();
                    self.$wrapper.find(self._selector.guestSelect).prop('disabled', false);
                    $('.js-actual-guests').empty();
                    $.each(data.items, function (key, user) {
                        self._addGuestsSelect(user);
                        self.addAllSpanGuest(user);
                    });
                }).catch(function(jqXHR){
                    const errorData = JSON.parse(jqXHR.responseText);
                    console.log(errorData);
                });

            } else {
                self._emptySelectGuest();
                this.$wrapper.find(this._selector.guestSelect).prop('disabled', true);
            }
        },

        _selectGuestByInterviewee: function (e) {
            const $user = $(e.currentTarget).val();
            this.loadGuests($user);
        },
        loadDetailCompteRendu: function(e){
            const self =this;
            $('#editEntretienModal').modal('toggle');
            let entretienId = $(e.currentTarget).closest('.js-edit-entretien-form').data('entretien');
            let modal = $('.js-modal-viewer h5').attr('data-id', entretienId);
            self.loadPdf(entretienId);
        },

        loadPdf: function (entretienId) {
            const self = this;
            $('#pdfViewer').modal('show');
            $.ajax({
                beforeSend: function(){
                    $(self._selector.editModalFooter).append("<span class='loading'>Chargement...</span>");
                },
                url: Routing.generate('entretien_modal_detail', {id: entretienId})
            }).then(function(data){
                const lienpdf = data.item.compteRenduLien;
                PDFObject.embed(lienpdf, '#pdfViewer .modal-body');
            });
            $('#pdfViewer .modal-body').css({ height: '50rem'});
        },
        _emptyHtmlModalBody: function () {
            $('body').removeClass('.modal-open');
            this.$wrapper.find(this._selector.editModalBody).html('');
            this.$wrapper.find(this._selector.editModalFooter).html('');
        },

        _mapErrorsToForm: function (errorData) {
            console.log(errorData)
            this._removeFormErrors();
            const $form = this.$wrapper.find('.show').find('.modal-body').find('form');

            $form.find('.form-control').each(function () {
                const fieldName = $(this).attr('name');
                const $wrapper = $(this).closest('.form-group');
                const $error = $('<span class="js-field-error text-danger"></span>');

                if (!errorData[fieldName]){
                    return;
                }

                $error.html(errorData[fieldName]);
                $wrapper.append($error);
                $wrapper.addClass('has-error');
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

        _addEditForm: function (entretien) {
            const tplText = $('#js-entretien-detail-template').html();
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
        /**
         * If needed add another guest
         *
         * Because the edit and new interview forms are loaded
         * I check which ul is loaded through the modal's css display's attribute
         * and set the right ul
         * */
        addSpanGuest: function () {
            const guest = '#guests option:selected';
            let $ul = $('.js-actual-guests');

            if($('#editEntretienModal').css('display') !== 'none'){
                $ul = $('.js-actual-guest-edit');
            }

            $ul.append('' +
                '<li id="guest_'+ $(guest).val()+'" title="'+ $(guest).text() +'" data-user="'+ $(guest).val() +'">' +
                    '<span class="js-delete-guest badge badge-danger">X</span> ' +
                    '<span class="badge badge-warning">'+ $(guest).text() +'</span>' +
                '</li>'
            );
        },
        /**
         * Add all guest related to the young selected by default
         * function only for the new interview form
         * */
        addAllSpanGuest: function (user) {
            $('.js-actual-guests').append('' +
                '<li id="guest_'+ user.id +'" title="'+ user.fullname +'" data-user="'+ user.id +'">' +
                '<span class="js-delete-guest badge badge-danger">X</span> ' +
                '<span class="badge badge-warning">'+ user.fullname +'</span>' +
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
        _disabledControlEditForm: function () {
            // We lock all form items
            $('.form-control').prop('disabled', true);
            // Hide and disabled the guests select
            $('#guests').prop('disabled', true).hide();
            // We remove the delete guest button
            $('.js-delete-guest').remove();
            //Removes tht modify button
            $(this._selector.editEntretienForm).find('button[type=submit]').remove();

        },
        /**
         * On edit interview, modifies the rows informations
         * */
        _updateActualRow: function (tr, date, objet, compteRendu) {
            const $columnDate = $(tr).find("td:nth-child(3)");
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