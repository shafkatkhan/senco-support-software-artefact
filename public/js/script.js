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
        
        // remove required attributes
        $('input[name="prof_first_name"]').removeAttr('required');
        $('input[name="prof_last_name"]').removeAttr('required');
    } else {
        // switch to new professional form
        $('#existing_professional_box').hide();
        $('#new_professional_box').show();
        $('#is_new_professional').val('1');
        $('#professional_id').val('');
        $(this).text('Cancel New Professional');
        
        // add required attributes
        $('input[name="prof_first_name"]').attr('required', true);
        $('input[name="prof_last_name"]').attr('required', true);
    }
});

$(document).ready(function() {
    var datatableConfigs = {
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "initComplete": function() {
            $('div.dt-length select').addClass('form-select form-select-sm');
            $('div.dt-search input').addClass('form-control form-control-sm');
        }
    };
    
    var locale = document.documentElement.lang.split('-')[0] || 'en';

    // translate if not English from DataTables CDN
    if (!locale.startsWith('en')) {
        // mapping to match DataTables CDN file names
        var dtLangMap = {
            'az': 'az-AZ',
            'bs': 'bs-BA',
            'zh-HK': 'zh-HANT',
            'zh-CN': 'zh',
            'zh-TW': 'zh-HANT',
            'nl': 'nl-NL',
            'fr': 'fr-FR',
            'fr-CA': 'fr-FR',
            'fr-CH': 'fr-FR',
            'de': 'de-DE',
            'de-AT': 'de-DE',
            'de-LI': 'de-DE',
            'de-CH': 'de-DE',
            'it': 'it-IT',
            'it-CH': 'it-IT',
            'no': 'no-NO',
            'nb': 'no-NB',
            'nn': 'no-NO',
            'pt': 'pt-PT',
            'mo': 'ro',
            'sh': 'sr-SP',
            'sd': 'snd',
            'es': 'es-ES',
            'es-419': 'es-ES',
            'es-US': 'es-MX',
            'sv': 'sv-SE'
        };
        var dtFileName = dtLangMap[locale] || locale;
        datatableConfigs.language = {
            "url": "https://cdn.datatables.net/plug-ins/2.3.7/i18n/" + dtFileName + ".json"
        };
    }
    $('.table').DataTable(datatableConfigs);
});