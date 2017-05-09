(function (window, $, Routing) {
    'use strict';

    window.StatusApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'submit',
            this._selector.newStatusForm,
            this.handleNewFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.editStatusForm,
            this.handleEditFormSubmit.bind(this)
        );

    };

    $.extend(window.StatusApp.prototype, {
        _selector: {
            newStatusForm: '.js-new-status-form',
            editStatusForm: '.js-edit-status-form'
        },

        handleNewFormSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const self = this;

            const formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });
            if(formData <= 0)
            {
                console.log('Je suis vide rien à passer');
            } else {
                $.ajax({
                    url: $form.data('url'),
                    method: 'POST',
                    data: JSON.stringify([formData]),
                    success: function (data) {

                    },
                    error: function (jqXHR) {
                        const errorData = JSON.parse(jqXHR.responseText);
                        self._mapErrorsToForm(errorData.errors);
                    }
                })
            }

        },

        handleEditFormSubmit: function (e) {
            e.preventDefault();
            console.log(e.currentTarget);
            const $form = $(e.currentTarget);
            const self = this;
            const entretienId = $('.js-edit-entretien-form').data('entretien');

            console.log($form);
            const formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });
            console.log(formData);


            $.ajax({
                url: Routing.generate('api_interviewuser_edit', {id: entretienId}),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    $('#editEntretienModal').modal('hide');
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newStatusForm);

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
            const $form = this.$wrapper.find(this._selector.newStatusForm);
            $form.find('.js-field-error').remove();
            $form.find('.form-group').removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            $('.js-actual-guests').empty();
            const $form = this.$wrapper.find(this._selector.newStatusForm);
            $form[0].reset();

        }
    });
})(window, jQuery, Routing);