(function (window, $, Routing) {
    'use strict';

    window.CategoryApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-delete-document',
            this.handleCategoryDelete.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.newCatForm,
            this.handleNewFormSubmit.bind(this)
        );

        this.loadCategories();
    };

    $.extend(window.CategoryApp.prototype, {
        _selector: {
            newCatForm: '.js-new-folder-form'
        },

        handleCategoryDelete: function (e) {
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
                    $('#addFolderForm').collapse('toggle');
                    $('#categorie').val(data.id);
                },
                error: function (jqXHR) {
                    var errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadCategories: function () {
            var self = this;
            $.ajax({
                url: Routing.generate('category_list'),
                success: function (data) {
                    $.each(data.items, function (key, category) {
                        self._addSelect(category);
                    })
                }
            })
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            var $form = this.$wrapper.find(this._selector.newCatForm);

            $form.find(':input').each(function () {
                var fieldName = $(this).attr('name');
                var $wrapper = $('.js-new-folder-form');
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
            var $form = $('.js-new-folder-form');
            $form.find('.js-field-error').remove();
            $form.removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            var $form = this.$wrapper.find(this._selector.newDocForm);
            $('.js-new-folder-form')[0].reset();

        },

        _addSelect: function (category) {
            var tplText = $('#js-cat-option-template').html();
            var tpl = _.template(tplText);
            var html = tpl(category);
            $('.js-select-folder').append($.parseHTML(html));
        }

    });
})(window, jQuery, Routing);