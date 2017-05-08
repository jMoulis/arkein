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
            const $form = $(e.currentTarget);
            const self = this;

            let formData = {};
            $.each($form.serializeArray(), function (key, fieldData) {
                formData[fieldData.name] = fieldData.value;
            });

            $.ajax({
                url: $form.data('url'),
                method: 'POST',
                data: JSON.stringify(formData),
                success: function (data) {
                    self._clearForm();
                    console.log('Nouveau'+ data);
                },
                error: function (jqXHR) {
                    const errorData = JSON.parse(jqXHR.responseText);
                    self._mapErrorsToForm(errorData.errors);
                }
            })
        },

        loadAnswers: function () {
            const self = this;
            $.ajax({
                url: Routing.generate('api_answer_list'),
                success: function (data) {
                    $.each(data.items, function (key, answer) {
                        console.log(answer);
                        self._addDiv(answer);
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

        _addDiv: function (answer) {
            const tplText = $('#js-answer-template').html();
            const tpl = _.template(tplText);
            const html = tpl(answer);
            this.$wrapper.append($.parseHTML(html));
        }

    });
})(window, jQuery, Routing);