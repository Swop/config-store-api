//(function($, _) {
//    $(function() {
//        var diffRowsContainer = $('tbody[data-role="diff-rows"]');
//        var diffRowsCount = diffRowsContainer[0].children.length;
//
//        $('*[data-role="add-config-button"]').click(function() {
//            event.preventDefault();
//
//            var prototypeKey = '<td class="editable_app">' +
//                '<input type="text" name="appConfig[configItems][__name__][key]" value="" />' +
//                '<div class="btn-toolbar diff-toolbar-main" role="toolbar" data-role="config-item-toolbar">' +
//                '<div class="btn-group btn-group-xs pull-right">' +
//                '<a href="#" class="btn btn-link" data-role="add-config-button" data-toggle="tooltip" data-placement="bottom" title="Copy right to left"><span class="glyphicon glyphicon-arrow-left"></span></a>' +
//                '<a href="#" class="btn btn-link" data-role="delete-config-button" data-config-index="__name__" data-toggle="tooltip" data-placement="bottom" title="Delete config"><span class="glyphicon glyphicon-trash"></span></a>' +
//                '</div>' +
//                '</div>' +
//                '</td>';
//            var prototypeValue = '<td class="diff_good editable_app"><input type="text" name="appConfig[configItems][__name__][value]" value="" /></td>';
//            var prototypeOtherAppValue = '<td></td>';
//
//            var newWidget = '<tr class="diff_row" data-role="config-node-__name__">'+prototypeKey + prototypeValue + prototypeOtherAppValue + '</tr>';
//            newWidget = newWidget.replace(/__name__/g, diffRowsCount);
//            diffRowsCount++;
//
//            diffRowsContainer.append($(newWidget));
//        });
//
//        $(document).on('click', '*[data-role="copy-right-to-left-button"]', function(event) {
//            event.preventDefault();
//            var configIndex = $(this).data('config-index');
//            var configKey = $(this).data('config-keyname');
//            var configValue = $(this).data('config-value');
//            var configNode = $('[data-role="config-node-' + configIndex + '"]');
//
//            var columns = configNode.find('td');
//
//            var keyColumn = $(columns[0]);
//
//            keyColumn
//                .addClass('editable_app')
//                .empty()
//                .append('<input type="text" name="appConfig[configItems]['+configIndex+'][key]" value="'+configKey+'" />')
//            ;
//
//            var leftColumn = $(columns[1]);
//
//            leftColumn
//                .empty()
//                .append('<input type="text" name="appConfig[configItems]['+configIndex+'][value]" value="'+configValue+'" />')
//            ;
//
//            var actionColumn = $(columns[2]);
//
//            actionColumn
//                .empty()
//                .append('' +
//                    '<div class="btn-toolbar diff-toolbar-main" role="toolbar" data-role="config-item-toolbar">' +
//                        '<div class="btn-group btn-group-xs pull-right">' +
//                            '<a href="#" class="btn btn-link" data-role="copy-right-to-left-button" data-toggle="tooltip" data-placement="bottom" title="Copy right to left" data-config-index="'+configIndex+'" data-config-keyname="'+configKey+'" data-config-value="'+configValue+'"><span class="glyphicon glyphicon-arrow-left"></span></a>' +
//                            '<a href="#" class="btn btn-link" data-role="delete-config-button" data-config-index="'+configIndex+'" data-toggle="tooltip" data-placement="bottom" title="Delete config"><span class="glyphicon glyphicon-trash"></span></a>' +
//                        '</div>' +
//                    '</div>')
//            ;
//
//            var rightColumn = $(columns[3]);
//
//            rightColumn
//                .removeClass('diff_good')
//            ;
//        });
//
//        $(document).on('click', '*[data-role="delete-config-button"]', function(event) {
//            event.preventDefault();
//            var configIndex = $(this).data('config-index');
//            var configNode = $('[data-role="config-node-' + configIndex + '"]');
//
//            configNode.remove();
//        });
//
//        $('*[data-role="config-item-toolbar"]').tooltip({container: 'body'});
//
//        $('*[data-role="save-config-button"]').click(function (event) {
//            event.preventDefault();
//            var form = $('form[data-role="save-config-form"]');
//            var appSlug = form.data('app-slug');
//            var formObject = form.serializeObject();
//
//            if (typeof formObject.appConfig == "undefined") {
//                formObject.appConfig = {
//                    configItems: []
//                };
//            } else {
//                var cpt = 0;
//                formObject.appConfig.configItems = _.reduce(formObject.appConfig.configItems, function(results, config) {
//                    if(typeof config != "undefined") {
//                        results[cpt] = config;
//                    }
//
//                    cpt += 1;
//
//                    return results;
//                }, {});
//            }
//
//            var json = JSON.stringify(formObject);
//            console.log(json);
//
//            $.ajax({
//                url: "/admin/apps/"+appSlug+"/config/update",
//                data: json,
//                type: 'POST',
//                dataType: 'json',
//                contentType: 'application/json',
//                statusCode: {
//                    200: function() {
//                        window.location.reload();
//                    }
//                },
//                error: function (xhr) {
//                    if (xhr.status != 200) {
//                        console.log(xhr.status);
//                        alert('Error');
//                    }
//                }
//            });
//        });
//    });
//})(jQuery, _);
