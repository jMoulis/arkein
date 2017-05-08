(function (window, $, Routing) {
    'use strict';

    window.UserApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-delete-user',
            this.handleUserDelete.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.newUserForm,
            this.handleNewFormSubmit.bind(this)
        );

        this.loadUsers();
    };

    $.extend(window.UserApp.prototype, {
        _selector: {
            newUserForm: '.js-new-user-form'
        },

        handleUserDelete: function (e) {
            e.preventDefault();
            var $link = $(e.currentTarget);

            $link.addClass('text-danger');

            var deleteUrl =  $link.data('url');
            var $row =  $link.closest('tr');
            var self = this;

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
                    self._addSelect(data);
                    $('#user').val(data.id);
                },
                error: function (jqXHR) {
                    var errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadUsers: function () {
            var self = this;
            $.ajax({
                url: Routing.generate('api_user_list'),
                success: function (data) {
                    $.each(data.items, function (key, user) {
                        self._addRow(user);
                    })
                },
                error: function (jqXHR) {
                    console.log(jqXHR.responseText);
                }
            })
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            var $form = this.$wrapper.find(this._selector.newUserForm);

            $form.find(':input').each(function () {
                var fieldName = $(this).attr('name');
                var $wrapper = $('.js-new-user-form');
                if (!errorData[fieldName]){
                    return;
                }

                var $error = $('<span class="js-field-error text-danger"></span>');
                $error.html(errorData[fieldName]);
                $wrapper.prepend($error);
                $wrapper.addClass('has-error');
            });
        },

        _removeFormErrors: function () {
            var $form = $('.js-new-user-form');
            $form.find('.js-field-error').remove();
            $form.removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            var $form = this.$wrapper.find(this._selector.newUserForm);
            $('.js-new-user-form')[0].reset();

        },

        _addRow: function (user) {
            var tplText = $('#js-user-row-template').html();
            var tpl = _.template(tplText);
            var html = tpl(user);
            this.$wrapper.find('tbody').append($.parseHTML(html));
        }

    });
})(window, jQuery, Routing);