
(function (window, $, Routing) {
    'use strict';

    window.DocumentApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-delete-document',
            this.handleDocumentDelete.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.newDocForm,
            this.handleNewFormSubmit.bind(this)
        );

        //this.loadDocuments();
    };

    $.extend(window.DocumentApp.prototype, {
        _selector: {
            newDocForm: '.js-new-document-form'
        },

        handleDocumentDelete: function (e) {
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
                    console.log('success');
                    /*self._clearForm();*/
                    self._addRow(data);
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadDocuments: function () {
            const self = this;
            $.ajax({
                url: Routing.generate('document_list'),
                success: function (data) {
                    $.each(data.items, function (key, document) {
                        self._addRow(document);
                    })
                }
            })
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newDocForm);

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
            const $form = this.$wrapper.find(this._selector.newDocForm);
            $form.find('.js-field-error').remove();
            $form.find('.form-group').removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newDocForm);
            $form[0].reset();
        },

        _addRow: function (document) {
            const tplText = $('#js-document-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(document);
            this.$wrapper.find('tbody').append($.parseHTML(html));
        }

    });
})(window, jQuery, Routing);