define([
    'underscore',
    'jquery',
    'popup-layout',
    'backbone',
    'backbone.modal'
], function (
    _,
    $,
    popupLayout,
    Backbone
) {
    var init = function (options) {
        var popupInstance;
        var settings = {
            templateData: {},
            template: function () {
                throw new Error('template is undefined');
            },
            onSubmitCallback: function () {},
            onRenderCallback: function () {},
            onCancelCallback: function () {},
            onShowCallback: function () {},
            keyControl: true
        };

        _.extend(settings, options);

        popupInstance = showPopup(settings.template(settings.templateData));

        function showPopup(renderedTemplate) {
            var popupModal;
            var PopupModal = Backbone.Modal.extend({
                template: renderedTemplate,
                submitEl: '.base-popup_controls_action',
                cancelEl: '.base-popup_close, .base-popup_controls_close-btn',
                prefix: 'base-popup',
                onRender: settings.onRenderCallback,
                beforeSubmit: settings.onSubmitCallback,
                beforeCancel: settings.onCancelCallback,
                onShow: settings.onShowCallback,
                keyControl: settings.keyControl,
                terminate: function onTerminatePopupHandler() {
                    popupLayout.modals.destroy();
                }
            });

            popupModal = new PopupModal();
            popupLayout.modals.show(popupModal);

            return popupModal;
        }

        return popupInstance;

    };

    return init;
});
