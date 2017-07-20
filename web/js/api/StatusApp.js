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
            editStatusForm: '.js-edit-status-form',
        },

        handleNewFormSubmit: function (e) {
            e.preventDefault();
            const self = this;
            const $form = $(e.currentTarget);
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
                }).then(function(){
                    $('.form-control').prop('disabled', false);
                }).catch(function(jqXHR){
                    $(self._selector.modalForm).find('button').prop("disabled", false);
                })
            }

        },

        handleEditFormSubmit: function (e) {
            e.preventDefault();
            const self = this;
            const $form = $(e.currentTarget);
            const entretienId = $('.js-edit-entretien-form').data('entretien');
            const formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                url: Routing.generate('api_interviewuser_edit', {id: entretienId}),
                method: 'POST',
                data: JSON.stringify(formData),
            }).then(function (data) {
                const $btnStatus = $('#entretien_id_' + data.interview + '').find('.js-status');
                $('.form-control').prop('disabled', false);
                let html = "";
                // 0 = non Aswered, 1 = present, 2 = absent
                if (Number(data.status) === 1) {
                    html = '<i class="fa fa-thumbs-up" aria-hidden="true"></i>';
                    $btnStatus
                        .removeClass('btn-default btn-danger')
                        .addClass('btn-primary')
                        .attr({
                            'title': 'Présent'
                        });
                } else if (Number(data.status) === 2) {
                    $btnStatus
                        .removeClass('btn-primary btn-danger')
                        .addClass('btn-default')
                        .attr({
                            'title': 'Absent'
                        });
                    html = '<i class="fa fa-thumbs-down" aria-hidden="true"></i>';
                }
                // Et met à jour le bouton du row correspondant
                $btnStatus
                    .empty()
                    .append(html);

                $('#editEntretienModal').modal('hide');
            }).catch(function(){
                $('.form-control').prop('disabled', false);
            })
        }
    });
})(window, jQuery, Routing);