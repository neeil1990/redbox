/*
 * Created by: nBrains
 * User: neil kovalev
 * Email: neeil@mail.ru
 * Date: 17.01.2023
 * Time: 11:57
 * File: bootstrap-modal-form-templates.js
 */

(function ($) {

    $.fn.BootstrapModalFormTemplates = function (options) {
        let modal = this;
        let content = modal.find('.modal-content');

        let settings = $.extend({
            title: "Modal",
            btnText: "Принять",
            fields: [
                {
                    type: 'text',
                    name: "text",
                    label: 'Text field',
                    params: [{
                        val: "",
                        placeholder: "Text field",
                    }]
                },
                {
                    type: 'checkbox',
                    name: "checkbox",
                    label: 'Checkbox field',
                    params: [{
                            val: "",
                            checked: false,
                            text: "Checkbox field",
                        }]
                },
                {
                    type: 'textarea',
                    name: "textarea",
                    label: 'Text field area',
                    params: [{
                        val: "",
                        placeholder: "Text field",
                    }]
                },
            ],
            onAgree: function (modal) {
                return modal;
            }
        }, options );

        function init()
        {
            let container = $('<form />', {
                id: 'form'
            });

            let h = header();
            let b = body();
            let f = footer();

            container.append(h);
            $.each(settings.fields, (i, f) => {
                let field = null;

                switch (f.type) {
                    case "textarea":
                        field = textareaField(f);
                        break;
                    case "text":
                        field = textField(f);
                        break;
                    case "checkbox":
                        field = checkboxField(f);
                        break;
                }

                b.append(group(f.label, field));
            });
            container.append(b);
            container.append(f);

            content.html(container);
        }

        function header()
        {
            let header = $('<div />', {
                class: 'modal-header',
            });

            let h4 = $('<h4 />', {
                class: 'modal-title',
            }).text(settings.title);

            let close = $('<button />', {
                type: 'button',
                class: 'close',
                "data-dismiss": 'model',
                "aria-label": 'Close',
            }).append($('<span />').attr('aria-hidden', true).text('×'));

            close.click(() => modal.modal('hide'));

            header.append(h4);
            header.append(close);

            return header;
        }

        function body()
        {
            return $('<div />', {
                class: 'modal-body',
            });
        }

        function footer()
        {
            let footer = $('<div />', {
                class: 'modal-footer justify-content-between'
            });

            let close = $('<button />', {
                type: 'button',
                class: 'btn btn-default',
                "data-dismiss": 'modal',
            }).text('Закрыть');

            close.click(() => modal.modal('hide'));

            let save = $('<button />', {
                type: 'button',
                class: 'btn btn-success',
            }).text(settings.btnText);

            save.click(() => settings.onAgree(modal));

            footer.append([close, save]);

            return footer;
        }

        function group(label, field)
        {
            let group = $('<div />', {
                class: 'form-group'
            });

            let labels = (label) ? $('<label />').text(label) : null;

            return group.append([labels, field])
        }

        function textareaField(f)
        {
            let params = f.params;

            if(!Array.isArray(params))
                return false;

            let container = $('<div />');

            $.each(params, function (i, param) {
                let input = $('<textarea />', {
                    name: f.name,
                    value: param.val,
                    class: 'form-control',
                    placeholder: param.placeholder,
                    rows: 10,
                });

                container.append(input);
            });

            return container;
        }

        function textField(f)
        {
            let params = f.params;

            if(!Array.isArray(params))
                return false;

            let container = $('<div />');

            $.each(params, function (i, param) {
                let input = $('<input />', {
                    type: 'text',
                    name: f.name,
                    value: param.val,
                    class: 'form-control',
                    placeholder: param.placeholder,
                });

                container.append(input);
            });

            return container;
        }

        function checkboxField(f)
        {
            let params = f.params;

            if(!Array.isArray(params))
                return false;

            let container = $('<div />');

            $.each(params, function (i, param) {

                let id = btoa(f.name + i);

                let check = $('<div />', {
                    class: 'custom-control custom-checkbox',
                });

                let checkbox = $('<input />', {
                    id: id,
                    class: 'custom-control-input',
                    type: 'checkbox',
                    name: f.name,
                    value: param.val,
                    checked: param.checked
                });

                let label = $('<label />', {
                    class: 'custom-control-label',
                    for: id,
                }).text(param.text);

                container.append(check.append([checkbox, label]));
            });

            return container;
        }

        init();
    }

}(jQuery));





