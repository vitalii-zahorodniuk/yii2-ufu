(function ($) {
    $.fn.categoryTreeView = function (options) {
        var settings = $.extend({
            'data': {},
            'emptyText': "<i>(not set)</i>",
            'multiselect': true,
            'name': 'qq',
            'selectedItems': [12, 2, 4],
            'showSelected': true,
            'height': 'auto',
        }, options);

        function isEmpty(obj) {
            for (var key in obj) {
                return false;
            }
            if (key == undefined) {
                return true;
            }
        }

        function renderTreeRecursive(dataList, parentId) {
            var items = '';
            $.each(dataList, function (key, value) {
                var hasNoChilds = isEmpty(value.childs);
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
                if (!hasNoChilds) {
                    items += renderTreeRecursive(value.childs, value.id);
                }
                items += "</li>\n";
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

            $(this).html(renderTreeRecursive(settings.data));

            if (settings.showSelected) {
                console.log($(this)
                    .find('input:checked'))
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
        };

        return this.each(make);
    };
})(jQuery);
