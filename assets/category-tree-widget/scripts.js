(function ($) {
    $.fn.categoryTreeView = function (options) {
        var settings = $.extend({
            'data': {},
            'background-color': 'blue'
        }, options);

        return this.each(function () {

            // Тут пишем код плагина tooltip

        });

    };
})(jQuery);

// $('div.ufu-ctree').on('click', 'span.glyphicon-chevron-right', function (e) {
//     $(this).hide();
//     $(this).parent().find('> span.glyphicon-chevron-down').show();
//     $(this).parent().find('> ul.ufu-ctree-child').slideDown();
// });
//
// $('div.ufu-ctree').on('click', 'span.glyphicon-chevron-down', function (e) {
//     $(this).hide();
//     $(this).parent().find('> span.glyphicon-chevron-down').show();
//     $(this).parent().find('ul.ufu-ctree-child').slideUp(function () {
//         $(this).parent().find('span.glyphicon-chevron-down').hide();
//         $(this).parent().find('span.glyphicon-chevron-right').show();
//     });
// });
