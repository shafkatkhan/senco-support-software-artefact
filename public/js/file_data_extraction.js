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