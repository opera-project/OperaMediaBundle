var selectMedia = function (FormVarsId) {
    $("#mediaPickerModal" + FormVarsId).appendTo(document.body);

    $(document).on('click', '#mediaPickerModal' + FormVarsId + ' [data-select-media]', function (event) {
        event.preventDefault();
        /**
         * Prevent "click=>refresh/change page" when selecting the media
         */

        $("#" + FormVarsId).val($(this).data('select-media'));
        $("#" + FormVarsId).parent().find('img').attr('src', $(this).data('select-media-preview'));

        $("#mediaPickerRemove" + FormVarsId).show();
        $("#mediaPickerModal" + FormVarsId).modal('hide');
    });

    $("#mediaPickerRemove" + FormVarsId).on('click', function (event) {
        event.preventDefault();
        $("#" + FormVarsId).val(null);
        $("#" + FormVarsId).parent().find('img').removeAttr('src');
        $(this).hide();
    });
}

var ajaxMediaLink = function (FormVarsId, callback = null) {
    $(document).on('click', '#mediaPickerModal' + FormVarsId + ' [data-ajax-link]', function (event) {
        event.preventDefault();
        /**
         * Prevent the "click=>refresh/change page" when changing folder
         */

        $.ajax({
            url: $(this).attr('href'),
            success: function (data) {
                $('#mediaPickerModal' + FormVarsId).find('.modal-body').html($(data).find('#main').html());
                
                // addDataAjaxLinkToPagination
                $('.pagination a').each(function(e) {
                    $(this).attr('data-ajax-link', '');
                })


                if (callback) {
                    callback();
                }
            }
        });

    });
}

var preventDefaultSubmit = function (FormVarsId, callback = null) {
    $(document).on('submit', '#mediaPickerModal' + FormVarsId + ' .modal-body form', function (event) {
        event.preventDefault();
        /**
         * Prevent the "click=>refresh/change page" when creating a folder or media
         */

        var options = {
            url: event.currentTarget.action,
            method: 'POST',
            data: $(event.currentTarget).serialize(),
            cache: false,
            success: function (data) {
                $(document).find('.modal-body').html($(data).find('#main').html());
                if (callback) {
                    callback();
                }
            }
        };

        if (event.currentTarget.enctype) {
            options.contentType = false;
            var formData = new FormData($(event.currentTarget)[0]);
            options.data = formData;
            options.processData = false;
        }

        $.ajax(options);
    });
}

var getMediapickerModalContent = function (mediaPickerModalId, title) {
    let urlParams = new URLSearchParams(document.querySelector('script[src*=opera_media_picker]').attributes.src.value.split('?')[1]);
    let baseUrl = urlParams.get('base_url') || '/admin';

    $.ajax({
        url: baseUrl + "/view-modal",
        success: function (data) {
            html = '';
            /**
             * add modal of the mediateque at the end of the body
             */
            html += '<div class="modal fade media_entity_modal" id="mediaPickerModal'+mediaPickerModalId+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel'+mediaPickerModalId+'">';
            html +=     '<div class="modal-dialog modal-lg" role="document">';
            html +=         '<div class="modal-content">';
            html +=             '<div class="modal-header">';
            html +=                 '<h4 class="modal-title" id="myModalLabel'+mediaPickerModalId+'">'+title+'</h4>';
            html +=                 '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            html +=             '</div>';
            html +=             '<div class="modal-body">';
            html +=                 data;
            html +=             '</div>';
            html +=         '</div>';
            html +=     '</div>';
            html += '</div>';
            $('body').append(html)
        }
    });
}
