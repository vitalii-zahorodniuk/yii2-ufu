(function ($) {
    var settings = {};

    var methods = {
        init: function (options) {
            settings = $.extend({
                'data': {},
                'emptyText': "<i>(noname)</i>",
                'multiselect': true,
                'name': '',
                'selectedItems': [],
                'showSelected': true,
                'height': 'auto',
                'onlyType': false,
            }, options);
            return this.each(make);
        },
        showOnlyType: function (typeId) {
            settings.onlyType = typeId;
            methods.render.apply(this, arguments);
        },
        render: function () {
            $(this).html(renderTreeRecursive(settings.data));
            if (settings.showSelected) {
                $(this)
                    .find('input:checked')
                    .each(function () {
                        $(this).parents('ul.ctreeview-child').each(function () {
                            $(this).show();
                            $(this).parent().find('> span.glyphicon-chevron-right').hide();
                            $(this).parent().find('> span.glyphicon-chevron-down').show();
                        });
                    });
            }
        },
    };

    $.fn.categoryTreeView = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
        $.error('Method ' + method + ' not exist!');
    };

    function renderTreeRecursive(dataList, parentId) {
        var items = '';
        $.each(dataList, function (key, value) {
            if (settings.onlyType === false || settings.onlyType === value.type) {
                var childs = renderTreeRecursive(value.childs, value.id);
                var hasNoChilds = childs.length === 0;
                items += "<li data-id=\"" + value.id + "\" data-type=\"" + value.type + "\">\n";
                items += "<span data-enable=\"" + hasNoChilds + "\" class=\"ctreeview-nochevron\" style=\"display: " + (hasNoChilds ? 'inline' : 'none') + ";\"></span>";
                items += "<span data-enable=\"" + !hasNoChilds + "\" class=\"glyphicon glyphicon-chevron-right\" style=\"display: " + (hasNoChilds ? 'none' : 'inline') + ";\"></span>";
                items += "<span data-enable=\"" + !hasNoChilds + "\" class=\"glyphicon glyphicon-chevron-down\"></span>"; // it's hidden by default in css
                var isChecked = $.inArray(value.id, settings.selectedItems) > -1;
                if (settings.multiselect) {
                    items += "<input type=\"checkbox\" name=\"" + settings.name + "\" value=\"" + value.id + "\"" + (isChecked ? ' checked' : '') + ">";
                } else {
                    items += "<input type=\"radio\" name=\"" + settings.name + "\" value=\"" + value.id + "\"" + (isChecked ? ' checked' : '') + ">";
                }
                items += "<span class=\"ctreeview-item-label\">" + (value.name.length ? value.name : settings.emptyText) + "</span>\n";
                items += childs;
                items += "</li>\n";
            }
        });
        if (items.length == 0) {
            return '';
        }
        if (parentId > 0) {
            return "<ul class=\"ctreeview-child\">" + items + "</ul>\n";
        }
        return "<div class=\"ctreeview\" style=\"height: " + settings.height + ";\"><ul>\n" + items + "</ul></div>\n";
    }

    var make = function () {
        $(this).addClass('panel panel-default panel-body');

        $(this).on('click', 'span.glyphicon-chevron-right', function (e) {
            $(this).hide();
            $(this).parent().find('> span.glyphicon-chevron-down').show();
            $(this).parent().find('> ul.ctreeview-child').slideDown();
        });

        $(this).on('click', 'span.glyphicon-chevron-down', function (e) {
            $(this).hide();
            $(this).parent().find('> span.glyphicon-chevron-down').show();
            $(this).parent().find('ul.ctreeview-child').slideUp(function () {
                $(this).parent().find('span.glyphicon-chevron-down').each(function () {
                    if ($(this).attr('data-enable') === 'true') {
                        $(this).hide();
                    }
                });
                $(this).parent().find('span.glyphicon-chevron-right').each(function () {
                    if ($(this).attr('data-enable') === 'true') {
                        $(this).show();
                    }
                });
            });
        });

        methods.render.apply(this, arguments);
    };
})(jQuery);