

CKEDITOR.plugins.add( 'opera_media_picker', {
	icons: 'opera_media_picker',
    init: function(editor) {
		$(document).on('click', '#mediaPickerModal' + editor.id + ' [data-select-media]', function (event) {
			event.preventDefault();
				
			var img = editor.document.createElement('img');
			img.setAttribute("src", $(this).data('select-media-preview'));
			editor.insertElement(img);

			$("#mediaPickerModal" + editor.id).modal('hide');
		});

        editor.addCommand("mediaPicker", {
			exec: function(edt) {	
				ajaxMediaLink(editor.id, () => {
					$('#mediaPickerModal' + editor.id + ' .select-media-button').remove();
				});
				preventDefaultSubmit(editor.id, () => {
					$('#mediaPickerModal' + editor.id + ' .select-media-button').remove();
				});

				$("#mediaPickerModal" + editor.id).modal('show');
			}
		});
		editor.ui.addButton('mediaPickerButton', {
			label: "Media Picker",
			command: 'mediaPicker',
			toolbar: 'insert',
			icon: this.path + '/images/icon.png'
		});
	
		CKEDITOR.scriptLoader.load( this.path + '/media-picker.js' );

		var html = '<div class="modal fade" id="mediaPickerModal' + editor.id + '" tabindex="-1" role="dialog" aria-labelledby="mediaPickerModal">';
		html +=     	'<div class="modal-dialog" role="document">'
		html +=     		'<div class="modal-content modal-lg">'
		html +=     			'<div class="modal-header">'
		html +=     				'<button type="button" class="close" data-dismiss="modal" aria-label="Close">'
		html +=     					'<span aria-hidden="true">&times;</span>'
		html +=     				'</button>'
		html +=     				'<h4 class="modal-title" id="mediaPickerModal">Choose Media</h4>'
		html +=     			'</div>'
		html +=     			'<div class="modal-body">'
		html +=     			'</div>'
		html +=     		'</div>'
		html +=     	'</div>'
		html +=     '</div>'
		var modal = document.createElement('div');
		modal.innerHTML = html;

		document.body.appendChild(modal.querySelector('#mediaPickerModal' + editor.id));

		$.ajax({
            url: "/admin/media/",
            success: function (data) {
				$('#mediaPickerModal' + editor.id).find('.modal-body').html($(data).find('#main').html());
				$('#mediaPickerModal' + editor.id + ' .select-media-button').remove();
            }
        });
    }
});
