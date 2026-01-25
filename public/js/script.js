$(document).on('click', '.delete_icon', function () {
    var url = $(this).data('url');
    var name = $(this).data('name');
    $('#delete_modal_name').text(name);
    $('#deleteForm').attr('action', url);
});

$(document).on('click', '.edit_icon', function () {
    var url = $(this).data('url');
    var name = $(this).data('name');
    var description = $(this).data('description');
    
    $('#editForm').attr('action', url);
    $('#edit_name').val(name);
    $('#edit_description').val(description);
});