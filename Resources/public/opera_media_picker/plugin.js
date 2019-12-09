

CKEDITOR.plugins.add( 'opera_media_picker', {
	icons: 'opera_media_picker',
    init: function(editor) {
		/**
		 * On <select> of image filter change: add image inside the ckeditor with the selected liip filter
		 */
		$(document).on('change', '#mediaPickerModal' + editor.id + ' .select-media-drop-down select', function (event) {
			imgUrlWithFilter = this.dataset.selectMediaPreview; // liip imagine filter url

			var regex = new RegExp('FILTER_SET_TO_REPLACE', "g");
            imgUrlWithFilter = imgUrlWithFilter.replace(regex, this.value);

			var img = editor.document.createElement('img');
			img.setAttribute("src", imgUrlWithFilter);
			editor.insertElement(img);

			this.selectedIndex = 0;
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

		/**
		 * Add the image icon in the ckeditor toolbar.
		 */
		editor.ui.addButton('mediaPickerButton', {
			label: "Media Picker",
			command: 'mediaPicker',
			toolbar: 'insert',
			icon: this.path + '/images/icon.png'
		});
	
		CKEDITOR.scriptLoader.load( this.path + '/media-picker.js' );

		var html = '<div class="modal fade" id="mediaPickerModal' + editor.id + '" tabindex="-1" role="dialog" aria-labelledby="mediaPickerModal">';
		html +=     	'<div class="modal-dialog modal-lg" role="document">'
		html +=     		'<div class="modal-content modal-lg">'
		html +=     			'<div class="modal-header">'
		html +=     				'<h4 class="modal-title" id="mediaPickerModal">Choose Media (ckeditor)</h4>'
		html +=     				'<button type="button" class="close" data-dismiss="modal" aria-label="Close">'
		html +=     					'<span aria-hidden="true">&times;</span>'
		html +=     				'</button>'
		html +=     			'</div>'
		html +=     			'<div class="modal-body">'
		html +=     			'</div>'
		html +=     		'</div>'
		html +=     	'</div>'
		html +=     '</div>'
		var modal = document.createElement('div');
		modal.innerHTML = html;

		document.body.appendChild(modal.querySelector('#mediaPickerModal' + editor.id));

		let urlParams = new URLSearchParams(document.querySelector('script[src*=opera_media_picker]').attributes.src.value.split('?')[1]);
		let baseUrl = urlParams.get('base_url') || '/admin';

		$.ajax({
			/**
			 * Ckedit always want the different image formats
			 */
            url: baseUrl + "/media?with_formats=1",
            success: function (data) {
				$('#mediaPickerModal' + editor.id).find('.modal-body').html($(data).find('#main').html());
				$('#mediaPickerModal' + editor.id + ' .select-media-button').remove();
            }
        });
    }
});
