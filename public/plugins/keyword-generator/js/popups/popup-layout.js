define('popup-layout', [
    'underscore',
    'jquery',
    'backbone',
    'backbone.marionette',
    'backbone.modal',
    'backbone.marionette.modals'
], function (
    _,
    $,
    Backbone
) {
    var Layout,
        popupLayout;

    Backbone.Marionette.Renderer.render = function (template, data) {
        if (!template) {
            throw new Backbone.Marionette.Error({
                name: 'TemplateNotFoundError',
                message: 'Cannot render the template since its false, null or undefined.'
            });
        }

        var templateFunc = _.isFunction(template) ? template : Backbone.Marionette.TemplateCache.get(template);

        return templateFunc(data);
    };

    Layout = Backbone.Marionette.LayoutView.extend({
        template: function () {
            return '<div class="modals-container"></div>';
        },
        className: 'modals-container-wrapper',
        regions: {
            modals: {
                selector: '.modals-container',
                regionClass: Backbone.Marionette.Modals
            }
        }
    });

    popupLayout = new Layout();

    $('body').append(popupLayout.render().el);

    return popupLayout;
});
