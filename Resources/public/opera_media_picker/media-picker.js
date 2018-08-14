var selectMedia = function(FormVarsId) {
    $("#mediaPickerModal" + FormVarsId).appendTo(document.body);

    $(document).on('click', '#mediaPickerModal' + FormVarsId + ' [data-select-media]', function (event) {
        event.preventDefault();

        $("#" + FormVarsId).val($(this).data('select-media'));
        $("#" + FormVarsId).parent().find('img').attr('src', $(this).data('select-media-preview'));

        $("#mediaPickerModal" + FormVarsId).modal('hide');
    });
}

var ajaxMediaLink = function(FormVarsId, callback = null) {
    $(document).on('click', '#mediaPickerModal' + FormVarsId + ' [data-ajax-link]', function (event) {
        event.preventDefault();

        $.ajax({
            url: $(this).attr('href'),
            success: function (data) {
                $(document).find('.modal-body').html($(data).find('#main').html());
                if (callback) {
                    callback();
                }
            }
        });

    });
}

var preventDefaultSubmit = function(FormVarsId, callback = null) {
    $(document).on('submit', '#mediaPickerModal' + FormVarsId + ' .modal-body form', function (event) {
        event.preventDefault();

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
