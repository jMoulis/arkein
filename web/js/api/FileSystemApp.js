(function (window, $, Routing) {
    'use strict';

    window.FileSystemApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-delete-answer',
            this.handleAnswerDelete.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.newDocForm,
            this.uploadFileFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.newFolderForm,
            this.handleNewFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-new-doc-btn',
            this.getFolderPath.bind(this)
        );
        this.loadFolders();

    };

    $.extend(window.FileSystemApp.prototype, {
        _selector: {
            newFolderForm: '.js-add-folder',
            newDocForm: '.js-upload-file'
        },

        handleAnswerDelete: function (e) {
            e.preventDefault();
            const $link = $(e.currentTarget);

            $link.addClass('text-danger');

            const deleteUrl =  $link.data('url');
            const $row =  $link.closest('tr');

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
            let formData = {};

            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                url: Routing.generate('api_new_folder'),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    self._clearForm();
                    console.log('success');
                    console.log(data);
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    console.log(errorData);
                    //self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadFolders: function () {
            const self = this;

            $.ajax({
                url: Routing.generate('api_get_folders'),
                method: 'POST',
                success: function (data) {

                    $.each(data.items, function (key, folder) {
                        self._addFolders(folder);
                    });
                },
                error: function (jqXHR) {
                    console.log(JSON.parse(jqXHR.responseText));
                }
            })
        },

        uploadFileFormSubmit: function (e) {
            e.preventDefault();
            //const folderPath = $('.js-folder').val();
            //console.log(folderPath);
            const $form = $(e.currentTarget);
            const self = this;

            let formData = new FormData($form[0]);

            $.ajax({
                url: Routing.generate('api_new_file'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    console.log(data);
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        getFolderPath: function (e) {
            const folderPath = $(e.currentTarget).closest('ul').find('h5').data('route');
            $('.modal .js-folder').val(folderPath);
        },

        _mapErrorsToForm: function (errorData) {
            const self = this;
            self._removeFormErrors();
            const $form = self.$wrapper.find(self._selector.newFolderForm);

            $form.find(':input').each(function () {
                const fieldName = $(this).attr('name');
                const $wrapper = $(self._selector.newFolderForm);
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
            const $form = $(this._selector.newFolderForm);
            $form.find('.js-field-error').remove();
            $form.removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            const $form = $(this.$wrapper.find(this._selector.newFolderForm));
            $form.reset();

        },

        _addFolders: function (folder) {
            const tplText = $('#js-treeview-folder-template').html();
            const tpl = _.template(tplText);
            const html = tpl(folder);
            this.$wrapper.find('.js-filesystem-treeview-wrapper').append($.parseHTML(html));
        },
    });
})(window, jQuery, Routing);