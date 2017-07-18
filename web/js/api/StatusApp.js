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
            } else {
                $.ajax({
                    url: $form.data('url'),
                    method: 'POST',
                    data: JSON.stringify([formData]),
                }).done(function(){
                    console.log('test')
                }).fail(function(jqXHR){
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                })
            }

        },

        handleEditFormSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const self = this;
            const entretienId = $('.js-edit-entretien-form').data('entretien');
            console.log(entretienId)
            const formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                url: Routing.generate('api_interviewuser_edit', {id: entretienId}),
                method: 'POST',
                data: JSON.stringify(formData),
            }).done(function(data){
                console.log(data);
                const $btnStatus = $('#entretien_id_'+ data.interview +'').find('.js-detail-entretien');
                let html = "";
                // 0 = non Aswered, 1 = present, 2 = absent
                if(Number(data.status) === 1){
                    html = '<i class="fa fa-thumbs-up" aria-hidden="true"></i>';
                    $btnStatus
                        .removeClass('btn-secondary btn-danger')
                        .addClass('btn-primary');
                } else if(Number(data.status) === 2) {
                    $btnStatus
                        .removeClass('btn-primary btn-danger')
                        .addClass('btn-secondary');
                    html = '<i class="fa fa-thumbs-down" aria-hidden="true"></i>';
                }
                // Et met à jour le bouton du row correspondant
                $btnStatus
                    .empty()
                    .append(html);

                $('#editEntretienModal').modal('hide');
            }).fail(function(jqXHR){
                const errorData = JSON.parse(jqXHR.responseText);
                self._mapErrorsToForm(errorData.errors);
            })
        },

        /*_mapErrorsToForm: function (errorData) {
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
        },*/
    });
})(window, jQuery, Routing);