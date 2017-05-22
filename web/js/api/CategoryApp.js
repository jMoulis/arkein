(function (window, $, Routing) {
    'use strict';

    window.CategoryApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'submit',
            this._selector.newCatForm,
            this.handleNewFromUserProfileFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.editCatForm,
            this.handleEditFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.newCatFromAdminPanelForm,
            this.handleNewCatFromAdminPanelSubmit.bind(this)
        );

        this.$wrapper.on(
            'hidden.bs.modal',
            '.modal',
            this._clearForm.bind(this)
        );

        this.$wrapper.on(
            'hidden.bs.modal',
            '#modalAddDoc',
            this._clearForm.bind(this)
        );

        this.$wrapper.on(
            'hidden.bs.modal',
            '#catEditModal',
            this._emptyHtmlModalBody.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-delete-document',
            this.handleCategoryDelete.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-edit-categorie-btn',
            this.loadEditFormData.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-classified',
            this.handleEditFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-new-folder',
            this.lockNewDocBtn.bind(this)
        );

        this.loadListSelectCategorie();

        this.loadListTreeViewCategorie();
    };

    $.extend(window.CategoryApp.prototype, {
        _selector: {
            newCatForm: '.js-new-folder-form',
            newCatFromAdminPanelForm: '.js-new-categorie',
            newModal: '#catNewModal',
            newModalFooter: '#catNewModal .modal-footer',
            editCatForm: '.js-edit-categorie',
            editModal: '#catEditModal',
            editModalBody: '#catEditModal .modal-body',
            editModalFooter: '#catEditModal .modal-footer',
        },

        handleCategoryDelete: function (e) {
            e.preventDefault();
            const $link = $(e.currentTarget);
            const deleteUrl =  $link.data('url');
            const $row =  $link.closest('tr');
            $link.addClass('text-danger');

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

        handleNewFromUserProfileFormSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const userid = $('h1').data('user');
            const self = this;
            let formData = {};

            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                beforeSend: function(){
                    $(self._selector.newModalFooter).append("<span class='waiting'>Chargement...</span>");
                },
                url: Routing.generate('api_category_new', {userid: userid}),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    self._addSelect(data, data.id);
                    $('#addFolderForm').collapse('toggle');
                    self._addTreeView(data);
                    self._clearForm();
                    self._unlockNewDocBtn();
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                    this._removeWaiting();
                }
            })
        },

        handleNewCatFromAdminPanelSubmit: function (e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const userid = $('.js-select-user option:selected').val();
            const self = this;

            const formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                beforeSend: function(){
                    $(self._selector.newModalFooter).append("<span class='waiting'>Chargement...</span>");
                },
                url: Routing.generate('api_category_new', {userid: userid}),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    $(self._selector.newModal).modal('hide');
                    self._clearForm();
                    self._removeWaiting();
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                    self._removeWaiting();
                }
            })
        },

        handleEditFormSubmit: function (e) {
            e.preventDefault();
            const self = this;
            const $form = $(e.currentTarget);
            const userid = $('.js-select-user option:selected').val();
            const catid = $form.data('id');

            let formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                beforeSend: function(){
                    $(self._selector.editModalFooter).append("<span class='waiting'>Chargement...</span>");
                },
                url: Routing.generate('api_category_edit', {userid: userid, catid: catid}),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    self._removeWaiting();
                    $(self._selector.editModal).modal('hide');
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                    self._removeWaiting();
                }
            })
        },

        loadListSelectCategorie: function () {
            const self = this;
            const userid = $('h1').data('user');
            $.ajax({
                url: Routing.generate('category_list', {userid: userid}),
                success: function (data) {
                    $.each(data.items, function (key, category) {
                        self._addSelect(category);
                    })
                }
            })
        },

        loadListTreeViewCategorie: function () {
            const self = this;
            const userid = $('h1').data('user');

            $.ajax({
                beforeSend: function () {
                    const html = "<span class='waiting'>Chargement des dossiers en cours... </span>";
                    $('.js-document-table').append(html);
                },
                url: Routing.generate('categorie_treeview', {id: userid}),
                success: function (data) {
                    self._removeWaiting();
                    $('.js-document-table').append('<h4>Documents <button type="button" ' +
                        'class="btn btn-success btn-sm" ' +
                        'data-toggle="modal" ' +
                        'data-target="#modalAddDoc"> +</button>' +
                        '</h4>');
                    $.each(data.items, function (key, category) {
                        self._addTreeView(category);
                    });
                    self.loadDocuments();
                }
            })
        },

        loadDocuments: function () {
            const self = this;
            const userid = $('h1').data('user');

            $.ajax({
                url: Routing.generate('api_document_list_by_destinataire', {userid: userid}),
                success: function (data) {

                    $.each(data.items, function (key, document) {
                        let testArray =[];
                        let $wrapper = $('tbody#tbody_'+ document.categories);
                        self._addNewDoc(document, $wrapper);
                        testArray.push(document);
                        $('#collapseFile_'+ document.categories).prev('.card').find('span').text($('#collapseFile_'+ document.categories).length)
                    });

                }
            })
        },

        loadUsers: function () {
            const self = this;
            $.ajax({
                url: Routing.generate('user_list'),
                success: function (data) {
                    $.each(data.items, function (key, user) {
                        self._addUserSelect(user);
                    });
                }
            })
        },

        loadEditFormData: function (e) {
            const self = this;
            const categorie = $(e.currentTarget).data('id');

            $.ajax({
                beforeSend: function(){
                    $(self._selector.editModalFooter).append("<span class='waiting'>Chargement...</span>");
                },
                url: Routing.generate('categorie_modal_detail', {id: categorie}),
                success: function (data) {
                    $('.waiting').remove();
                    self._editFormCat(data.item);
                    self.loadUsers();
                }
            })
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            const $form = this.$wrapper.find('.modal-body').find('form');
            console.log($form);

            $form.find(':input').each(function () {
                const fieldName = $(this).attr('name');
                if (!errorData[fieldName]){
                    return;
                }

                const $error = $('<span class="js-field-error text-danger"></span>');
                $error.html(errorData[fieldName]);
                $form.prepend($error);
                $form.addClass('has-error');
            });
        },

        _removeFormErrors: function () {
            const $form = this.$wrapper.find('.modal-body').find('form');
            $form.find('.js-field-error').remove();
            $form.removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            const $form = this.$wrapper.find('.modal-body').find('form');
            $form[0].reset();
            $form[1].reset();
            this._removeWaiting();
        },

        _emptyHtmlModalBody: function () {
            this.$wrapper.find(this._selector.editModalBody).html('');
            this.$wrapper.find(this._selector.editModalFooter).html('');
            this._removeFormErrors();
        },

        _removeWaiting: function() {
            $('.waiting').remove();
        },

        _addSelect: function (category, id) {
            const tplText = $('#js-cat-option-template').html();
            const tpl = _.template(tplText);
            const html = tpl(category);
            $('.js-select-folder').append($.parseHTML(html));
            $('.js-select-folder').val(id);
        },

        _addTreeView: function (category) {
            const tplText = $('#js-treeview-categorie-template').html();
            const tpl = _.template(tplText);
            const html = tpl(category);
            $('.js-document-table').append($.parseHTML(html));
        },

        _addNewDoc: function (document, wrapper) {
            const tplText = $('#js-document-add-template').html();
            const tpl = _.template(tplText);
            const html = tpl(document);
            $(wrapper).append($.parseHTML(html));
        },

        _editFormCat: function (categorie) {
            const tplText =$('#js-categorie-edit-template').html();
            const tpl = _.template(tplText);
            const html = tpl(categorie);
            $(this._selector.editModalBody).append($.parseHTML(html));
        },

        _addUserSelect: function (user) {
            const tplText = $('#js-users-option-template').html();
            const tpl = _.template(tplText);
            const html = tpl(user);
            $('.js-select-user').append($.parseHTML(html));
        },
        
        lockNewDocBtn: function () {
            $('.modal-footer .btn-group .btn').prop('disabled', true);
        },

        _unlockNewDocBtn: function () {
            $('.modal-footer .btn-group .btn').prop('disabled', false);
        }
    });
})(window, jQuery, Routing);