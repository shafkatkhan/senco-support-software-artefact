var selectedFile = null;
function setFile(file) {
    selectedFile = file;
    $('.file_extraction_box .filename').text(file.name);
    $('.file_extraction_box button').prop('disabled', false);
    $('.file_extraction_box .status').text('').removeClass('text-danger text-success');
}

// click to open file picker
$('.file_extraction_box').on('click', function (e) {
    if ($(e.target).closest('.file_extraction_box button').length) return;
    var input = $('<input type="file">').on('change', function () {
        if (this.files.length) setFile(this.files[0]);
    });
    input.click();
});

// drag and drop
$('.file_extraction_box').on('dragover', function (e) {
    e.preventDefault();
    $(this).addClass('dragover');
}).on('dragleave drop', function (e) {
    e.preventDefault();
    $(this).removeClass('dragover');
    if (e.type === 'drop' && e.originalEvent.dataTransfer.files.length) {
        setFile(e.originalEvent.dataTransfer.files[0]);
    }
});

/**
 * Setup the extraction button click handler for file extraction
 * @param {string} url - The route to send the file to
 * @param {string} token - CSRF token
 * @param {function} successCallback - Function to execute on successful extraction
 */
function setupFileExtraction(url, token, successCallback) {
    $('.file_extraction_box button').off('click').on('click', function () {
        if (!selectedFile) return;

        var formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('_token', token);

        var btn = $(this);
        var status = btn.closest('.file_extraction_box').find('.status');

        btn.prop('disabled', true);
        status.html('<span class="spinner-border" role="status"></span> Extracting data...').removeClass('text-danger text-success');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                successCallback(response.data);
                status.text('Fields populated successfully.').addClass('text-success');
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Extraction failed.';
                status.text(msg).addClass('text-danger');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
}
