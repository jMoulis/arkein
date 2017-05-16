(function (window, $, Routing) {
    'use strict';

    window.BlogApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-delete-billet',
            this.handleUserDelete.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.newUserForm,
            this.handleNewFormSubmit.bind(this)
        );

        this.loadBillets();
    };

    $.extend(window.BlogApp.prototype, {
        _selector: {
            newUserForm: '.js-new-billet-form'
        },

        handleUserDelete: function (e) {
            e.preventDefault();
            const $link = $(e.currentTarget);
            $link.addClass('text-danger');
            const deleteUrl =  $link.data('url');
            const $row =  $link.closest('tr');
            const self = this;

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

            const formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                url: $form.data('url'),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    self._clearForm();
                    self._addSelect(data);
                    $('#billet').val(data.id);
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadBillets: function () {
            const self = this;
            $.ajax({
                beforeSend: function () {
                    self.$wrapper.find('.js-content').append('<span class="loading">Chargement...</span>');
                },
                url: Routing.generate('api_billet_list'),
                success: function (data) {
                    self._removeLoading();
                    $.each(data.items, function (key, billet) {
                        self._addRow(billet);
                    })
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    //self._mapErrorsToForm(errorData.errors);
                    self._removeLoading();
                }
            })
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newUserForm);

            $form.find(':input').each(function () {
                const fieldName = $(this).attr('name');
                const $wrapper = $('.js-new-billet-form');
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
            const $form = $('.js-new-billet-form');
            $form.find('.js-field-error').remove();
            $form.removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newUserForm);
            $form[0].reset();

        },

        _addRow: function (billet) {
            const tplText = $('#js-billet-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(billet);
            this.$wrapper.find('.js-content').append($.parseHTML(html));
        },

        _removeLoading: function(){
            $('.loading').remove();
        }
    });
})(window, jQuery, Routing);