
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

        this.$wrapper.on(
            'click',
            '.card',
            this.sortingFile.bind(this)
        );

        this.$wrapper.on(
            'hidden.bs.modal',
            '#modalAddDoc',
            this._clearForm.bind(this)
        );

        this.$wrapper.on('click',
            '.js-new-doc-btn',
            this.setSelectedValue.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-display-file',
            this.displaySecuredFile.bind(this)
        )

    };

    $.extend(window.DocumentApp.prototype, {
        _selector: {
            newDocForm: '.js-new-document-form'
        },

        handleDocumentDelete: function (e) {
            e.preventDefault();
            const $link = $(e.currentTarget);
            const idDoc = $(e.currentTarget).closest('tr').data('id');
            $link.addClass('text-danger');

            const deleteUrl =  Routing.generate('api_document_delete', {id: idDoc});
            const $row =  $link.closest('tr');
            const self = this;

            $.ajax({
                url: deleteUrl,
                method: 'DELETE',
                success: function () {
                    $row.fadeOut('normal', function () {
                        console.log('test')
                        /*let totalDocReceiver = Number($(this).closest('ul').find('.js-total-doc-categorie').text());
                        totalDocReceiver = totalDocReceiver - 1;
                        $link.closest('ul').find('.js-total-doc-categorie').text(totalDocReceiver);*/
                        $(this).remove();
                    });
                }
            })
        },

        handleNewFormSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const self = this;

            let formData = new FormData($form[0]);

            $.ajax({
                url: $form.data('url'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#modalAddDoc').modal('hide');
                    const $wrapper = $('tbody#tbody_'+ data.categories + '');
                    self._addNewDoc(data, $wrapper);
                    self._clearForm();

                    /*let totalDocReceiver = Number($wrapper.closest('ul').find('.js-total-doc-categorie').text());
                    totalDocReceiver = totalDocReceiver + 1;
                    $wrapper.closest('ul').find('.js-total-doc-categorie').text(totalDocReceiver);*/
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        displaySecuredFile: function (e) {
            const id = $(e.currentTarget).closest('tr').data('id');
                $.ajax({
                    url: Routing.generate('api_check', {id: id}),
                    method: 'POST',
                    success: function (data) {
                        console.log('test');
                    },
                    error: function (jqXHR) {
                        console.log(JSON.parse(jqXHR.responseText));
                    }
                })
        },

        sortingFile: function(){
            $(".sortable").sortable({
                connectWith: ".connectedSortable",
                items: "tr:not(.notSortable)",
                receive: function (e, ui) {
                    const self = this;
                    const idCat = ($(self)[0].id.match(/\d+/));
                    const id = $(ui.item)[0].id;

                    let totalDocReceiver = Number($(self).closest('ul').find('.js-total-doc-categorie').text());
                    $.ajax({
                        url: Routing.generate('api_document_edit'),
                        data: JSON.stringify({id: id, idCat: idCat}),
                        method: 'POST',
                        success: function (data) {
                            totalDocReceiver = totalDocReceiver + 1;
                            $(self).closest('ul').find('.js-total-doc-categorie').text(totalDocReceiver);
                        },
                        error: function (jqXHR) {
                            const errorData = JSON.parse(jqXHR.responseText);
                            self._mapErrorsToForm(errorData.errors);
                        }
                    })
                },
                remove: function (e, ui) {
                    let totalDocReceiver = Number($(this).closest('ul').find('.js-total-doc-categorie').text());
                    totalDocReceiver = totalDocReceiver - 1;
                    $(this).closest('ul').find('.js-total-doc-categorie').text(totalDocReceiver);
                }
            }).disableSelection()
        },

        setSelectedValue: function (e) {
            e.preventDefault();
            $('.js-select-folder').val($(e.currentTarget).data('id'));
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
            $form.find('.js-inputfile-text').html('Télécharger un fichier');
            $form[0].reset();
        },

        _addRow: function (document) {
            const tplText = $('#js-document-row-template').html();
            const tpl = _.template(tplText);
            const html = tpl(document);
            this.$wrapper.find('tbody').append($.parseHTML(html));
        },

        _addNewDoc: function (document, wrapper) {
            const tplText = $('#js-document-add-template').html();
            const tpl = _.template(tplText);
            const html = tpl(document);
            $(wrapper).append($.parseHTML(html));
        }
    });
})(window, jQuery, Routing);