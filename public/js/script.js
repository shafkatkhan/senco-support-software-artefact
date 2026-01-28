$(document).on('click', '.delete_icon', function () {
    var url = $(this).data('url');
    var name = $(this).data('name');
    $('#delete_modal_name').text(name);
    $('#deleteForm').attr('action', url);
});

$(document).ready(function() {
    $('.new_button').addClass('button_styled');

    $('#toggleViewBtn').click(function() {
        if ($('#toggleViewTable').is(':visible')) {
            $('#toggleViewTable').hide();
            $('#toggleViewGrid').css('display', 'flex');
            $('#toggleViewBtn').text('Toggle Table View');
        } else {
            $('#toggleViewTable').show();
            $('#toggleViewGrid').hide();
            $('#toggleViewBtn').text('Toggle Card View');
        }
    });
});