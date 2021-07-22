define([
    'popup',
    'tpl!templates/base-popup_confirm'
], function (
    popup,
    popupConfirmTemplate
) {
    var init = function (contentText, headerText, actionButtonSubmitCallback, actionButtonLabel, cancelCallback) {
        popup({
            templateData: {
                header: headerText,
                content: contentText,
                actionButtonLabel: actionButtonLabel || 'Ok'
            },
            template: popupConfirmTemplate,
            onSubmitCallback: actionButtonSubmitCallback,
            onCancelCallback: cancelCallback
        });
    };

    return init;
});
