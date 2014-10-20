(function($, window) {
    //$.fn.serializeObject = function() {
    //    var o = {};
    //    var a = this.serializeArray();
    //    $.each(a, function() {
    //        if (o[this.name] !== undefined) {
    //            if (!o[this.name].push) {
    //                o[this.name] = [o[this.name]];
    //            }
    //            o[this.name].push(this.value || '');
    //        } else {
    //            o[this.name] = this.value || '';
    //        }
    //    });
    //    return o;
    //};

    $(function() {
        $('a[data-role="api-key-toggle-button"]').click(function(event) {
            event.preventDefault();
            var apiKeyBlock = $('#' + $(this).data('target'));
            apiKeyBlock.toggle();
        });

        $('button[data-role="app-compare-modal-submit-button"]').click(function(event) {
            event.preventDefault();
            var appSelector = $($(this).data('selector'));
            window.location = appSelector.val();
        });

        $('*[data-role="NewAppButton"]').click(function(event) {
            event.preventDefault();
            var groupId = $(this).data('group-id');

            $("#app_name").val('');
            $("#app_desc").val('');

            if (groupId > 0) {
                $('#app_group option[value="'+groupId+'"]').prop('selected', true);
            } else {
                $('#app_group option:eq(0)').prop('selected', true);
            }

            $('#app-new-modal').modal('show');
        });

        $('button[data-role="app-new-submit-button"]').click(function (event) {
            event.preventDefault();
            var json = $('form[data-role="new-app-form"]').serializeJSON();

            $.ajax({
                url: "/admin/apps/create",
                data: json,
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                statusCode: {
                    201: function() {
                        window.location.reload();
                    }
                },
                error: function (xhr) {
                    if (xhr.status != 201) {
                        console.log(xhr.status);
                        alert('Error');
                    }
                }
            });

        });
    });
})(jQuery, window);
