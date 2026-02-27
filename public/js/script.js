$(document).on('click', '.delete_icon', function () {
    var url = $(this).data('url');
    var name = $(this).data('name');
    $('#delete_modal_name').text(name);
    $('#deleteForm').attr('action', url);
});


$(document).ready(function() {
    $('.new_button').addClass('button_styled');

    // apply saved view preference on page load
    applyViewPreference();

    $('#toggleViewBtn').click(function() {
        var currentPref = localStorage.getItem('sen_view_preference') || 'card';
        var newPref = (currentPref == 'table') ? 'card' : 'table';
        localStorage.setItem('sen_view_preference', newPref);
        applyViewPreference();
    });

    function applyViewPreference() {
        var pref = localStorage.getItem('sen_view_preference') || 'card';
        
        $('html').removeClass('view-pref-card view-pref-table').addClass('view-pref-' + pref);
        
        var $btn = $('#toggleViewBtn');

        if (pref == 'card') {
            if ($('#pupilsGrid').length) {
                $btn.text('View Less Information');
            } else {
                $btn.text('Toggle Table View');
            }
        } else {
            if ($('#pupilsGrid').length) {
                $btn.text('View More Information');
            } else {
                $btn.text('Toggle Card View');
            }
        }
    }
});