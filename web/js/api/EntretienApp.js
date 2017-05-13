
(function (window, $, Routing) {
    'use strict';

    window.EntretienApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'submit',
            this._selector.newEntretienForm,
            this.handleNewFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-detail-entretien',
            this.loadDetailEntretien.bind(this)
        );

        this.$wrapper.on(
            'hidden.bs.modal',
            '#editEntretienModal',
            this.emptyHtmlModalBody.bind(this)
        );

    };

    $.extend(window.EntretienApp.prototype, {
        _selector: {
            newEntretienForm: '.js-new-entretien-form',
            modalBody: '#editEntretienModal .modal-body'
        },

        handleNewFormSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const $guests = $form.find('ul').children();
            const self = this;


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
                    self._clearForm();
                    window.location = data.links._self;
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadListEntretiens: function () {
            const self = this;
            const user = $('h1').data('user');
            $.ajax({
                url: Routing.generate('entretien_list_by_young', {id: user}),
                success: function (data) {
                    if($(data.items).length <= 0){
                        self.$wrapper.find('tbody').append('' +
                            '<br /><div class="alert alert-success" role="alert">' +
                            'Aucun entretiens trouvés</div>');
                        $('#loading').hide();
                    } else {
                        $.each(data.items, function (key, entretien) {
                            self._addRow(entretien);
                            $('#loading').hide();
                        })
                    }
                },
                ajaxSend: function () {
                    $('#loading').show();
                }
            })
        },

        loadDetailEntretien: function (e) {
            const self = this;
            const entretien = $(e.currentTarget).data('id');
            console.log('ok');
            $.ajax({
                url: Routing.generate('entretien_modal_detail', {id: entretien}),
                success: function (data) {
                    self._addDetail(data.item);
                }
            })
        },

        loadUsers: function (e) {
            const self = this;
            $.ajax({
                url: Routing.generate('api_user_list'),
                success: function (data) {
                    $.each(data.items, function (key, user) {
                        self._addSelect(user);
                    });
                }
            })
        },

        loadGuests: function (e) {
            const self = this;
            $.ajax({
                url: Routing.generate('guest_list'),
                success: function (data) {
                    $.each(data.items, function (key, user) {
                        self._addGuestsSelect(user);
                    });
                }
            })
        },

        emptyHtmlModalBody: function () {
            this.$wrapper.find(this._selector.modalBody).html('');
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
            const $form = this.$wrapper.find(this._selector.newEntretienForm);
            $form[0].reset();
        },

        _addDetail: function (entretien) {
            const tplText = $('#js-entretien-detail-template').html();
            const tpl = _.template(tplText);
            const html = tpl(entretien);
            this.$wrapper.find(this._selector.modalBody).append($.parseHTML(html));
        },

        _addRow: function (entretien) {
            const tplText = $('#js-entretien-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(entretien);
            this.$wrapper.find('tbody').append($.parseHTML(html));
        },

        _addSelect: function (entretien) {
            const tplText = $('#js-user-option-template').html();
            const tpl = _.template(tplText);
            const html = tpl(entretien);
            $('.js-select-user').append($.parseHTML(html));
        },

        _addGuestsSelect: function (entretien) {
            const tplText = $('#js-guests-option-template').html();
            const tpl = _.template(tplText);
            const html = tpl(entretien);
            $('.js-select-guest').append($.parseHTML(html));
        }
    });
})(window, jQuery, Routing);