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

        this.$wrapper.on(
            'click',
            '.js-detail-user',
            this.setMemberDetail.bind(this)
        );

        this.loadUsers();
    };

    $.extend(window.UserApp.prototype, {
        _selector: {
            newUserForm: '.js-new-user-form'
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
                    $('#user').val(data.id);
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadUsers: function () {
            const self = this;
            $.ajax({
                url: Routing.generate('young_list'),
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

        setMemberDetail: function(e){
            //The purpose is to set a id of the user clicked to use the right route in the EntretienAppauthor, to display the tight interviews
            $('body').data('memberDetail', $(e.currentTarget).data('id'));
        },
        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newUserForm);

            $form.find(':input').each(function () {
                const fieldName = $(this).attr('name');
                const $wrapper = $('.js-new-user-form');
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
            const $form = $('.js-new-user-form');
            $form.find('.js-field-error').remove();
            $form.removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newUserForm);
            $('.js-new-user-form')[0].reset();

        },

        _addRow: function (user) {
            const tplText = $('#js-user-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(user);
            this.$wrapper.find('tbody').append($.parseHTML(html));
        }

    });
})(window, jQuery, Routing);