(function (window, $, Routing) {
    'use strict';

    window.AnswerApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-delete-answer',
            this.handleAnswerDelete.bind(this)
        );

        this.$wrapper.on(
            'submit',
            this._selector.newAnswerForm,
            this.handleNewFormSubmit.bind(this)
        );

        this.loadAnswers();

    };

    $.extend(window.AnswerApp.prototype, {
        _selector: {
            newAnswerForm: '.js-new-answer-form'
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
            const ticketId = $('h1').data('id');
            const $form = $(e.currentTarget);
            const self = this;
            let formData = {};

            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                beforeSend: function(){
                    $('.js-answer-wrapper').prepend('<span class="loading">Chargement...</span>');
                },
                url: Routing.generate('api_answer_new', {id: ticketId}),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    $('.js-answer-wrapper .loading').remove();
                    self._clearForm();
                    self._addNewDiv(data);
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    $('.js-answer-wrapper .loading').remove();
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadAnswers: function () {
            const self = this;
            const ticketId = $('h1').data('id');

            $.ajax({
                beforeSend: function(){
                    $('.js-answer-wrapper').prepend('<span class="loading">Chargement...</span>');
                },
                url: Routing.generate('api_answer_list', {id: ticketId}),
                success: function (data) {
                    $('.js-answer-wrapper .loading').remove();
                    $.each(data.items, function (key, answer) {
                        self._addListDiv(answer);
                    })
                },
                error: function (jqXHR) {
                    console.log(jqXHR.responseText);
                }
            })
        },

        _mapErrorsToForm: function (errorData) {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newAnswerForm);

            $form.find(':input').each(function () {
                const fieldName = $(this).attr('name');
                const $wrapper = $('.js-new-answer-form');
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
            const $form = $('.js-new-answer-form');
            $form.find('.js-field-error').remove();
            $form.removeClass('has-error');
        },

        _clearForm: function () {
            this._removeFormErrors();
            const $form = this.$wrapper.find(this._selector.newAnswerForm);
            $('.js-new-answer-form')[0].reset();

        },

        _addListDiv: function (answer) {
            const tplText = $('#js-answer-template').html();
            const tpl = _.template(tplText);
            const html = tpl(answer);
            $('.js-answer-wrapper').append($.parseHTML(html));
        },

        _addNewDiv: function (answer) {
            const tplText = $('#js-answer-template').html();
            const tpl = _.template(tplText);
            const html = tpl(answer);
            $('.js-answer-wrapper').prepend($.parseHTML(html));
        }

    });
})(window, jQuery, Routing);