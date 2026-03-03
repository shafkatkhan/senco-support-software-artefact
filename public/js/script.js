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

    $('#toggleNavBtn').click(function(e) {
        e.preventDefault();
        $('html').toggleClass('nav-hidden');
        var isHidden = $('html').hasClass('nav-hidden');
        localStorage.setItem('sen_nav_hidden', isHidden);
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

$('#toggle_professional_btn').click(function() {
    var isNew = $('#is_new_professional').val() == '1';
    if (isNew) {
        // switch to existing professional dropdown
        $('#new_professional_box').hide();
        $('#existing_professional_box').show();
        $('#is_new_professional').val('0');
        $(this).text('+ Add New Professional');
    } else {
        // switch to new professional form
        $('#existing_professional_box').hide();
        $('#new_professional_box').show();
        $('#is_new_professional').val('1');
        $('#professional_id').val('');
        $(this).text('Cancel New Professional');
    }
});