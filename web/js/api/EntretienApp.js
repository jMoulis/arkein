
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
            '#entretienModal',
            this.emptyHtmlModalBody.bind(this)
        );

        this.loadEntretiens();

    };

    $.extend(window.EntretienApp.prototype, {
        _selector: {
            newEntretienForm: '.js-new-entretien-form',
            modalBody: '#entretienModal .modal-body'
        },

        handleNewFormSubmit: function (e) {
            e.preventDefault();
            var $form = $(e.currentTarget);
            var self = this;

            var formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                url: $form.data('url'),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    self._clearForm();
                    self._addRow(data);
                },
                error: function (jqXHR) {
                    var errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadEntretiens: function () {
            var self = this;
            var user = $('h1').data('user');
            $.ajax({
                url: Routing.generate('entretien_list', {id: user}),
                success: function (data) {
                    $.each(data.items, function (key, entretien) {
                        self._addRow(entretien);
                        $('#loading').hide();
                    })
                },
                ajaxSend: function () {
                    $('#loading').show();
                }
            })
        },

        loadDetailEntretien: function (e) {
            var self = this;
            var entretien = $(e.currentTarget).data('id');
            $.ajax({
                url: Routing.generate('entretien_show', {id: entretien}),
                success: function (data) {
                    self._addDetail(data.item);
                }
            })
        },

        emptyHtmlModalBody: function () {
            this.$wrapper.find(this._selector.modalBody).html('');
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            var $form = this.$wrapper.find(this._selector.newEntretienForm);

            $form.find(':input').each(function () {
                var fieldName = $(this).attr('name');
                var $wrapper = $(this).closest('.form-group');
                if (!errorData[fieldName]){
                    return;
                }

                var $error = $('<span class="js-field-error text-danger"></span>');
                $error.html(errorData[fieldName]);
                $wrapper.append($error);
                $wrapper.addClass('has-error');
            });
        },

        _removeFormErrors: function () {
            var $form = this.$wrapper.find(this._selector.newEntretienForm);
            $form.find('.js-field-error').remove();
            $form.find('.form-group').removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            var $form = this.$wrapper.find(this._selector.newEntretienForm);
            $form[0].reset();
        },

        _addRow: function (entretien) {
            var tplText = $('#js-entretien-row-template').html();
            var tpl = _.template(tplText);
            var html = tpl(entretien);
            this.$wrapper.find('tbody').append($.parseHTML(html));
        },

        _addDetail: function (entretien) {
            var tplText = $('#js-entretien-detail-template').html();
            var tpl = _.template(tplText);
            var html = tpl(entretien);
            this.$wrapper.find(this._selector.modalBody).append($.parseHTML(html));
        }
    });
})(window, jQuery, Routing);