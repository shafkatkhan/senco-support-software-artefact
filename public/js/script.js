$(document).on('click', '.delete_icon', function () {
    var url = $(this).data('url');
    var name = $(this).data('name');
    $('#delete_modal_name').text(name);
    $('#deleteForm').attr('action', url);
});

// delete attachment modal population
$(document).on('click', '.delete_attachment_icon', function () {
    var url = $(this).data('url');
    var name = $(this).data('name');
    $('#deleteAttachment_modal_name').text(name);
    $('#deleteAttachmentForm').attr('action', url);
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

    $('.dropdown_nav_btn').click(function(e) {
        e.preventDefault();
        var $dropdown = $(this).next('.dropdown_items');
        var $icon = $(this).find('i');
        
        $dropdown.slideToggle(300);
        $icon.toggleClass('rotate');
    });
    // open dropdown if any child is active on page load
    $('.dropdown_items').each(function() {
        if ($(this).find('.activenav').length > 0) {
            $(this).show();
            $(this).prev('.dropdown_nav_btn').find('i').addClass('rotate');
        }
    });

    function applyViewPreference() {
        var pref = localStorage.getItem('sen_view_preference') || 'card';
        
        $('html').removeClass('view-pref-card view-pref-table').addClass('view-pref-' + pref);
        
        var $btn = $('#toggleViewBtn');

        if (pref == 'card') {
            if ($('#pupilsGrid').length) {
                $btn.text(__('View Less Information'));
            } else {
                $btn.text(__('Toggle Table View'));
            }
        } else {
            if ($('#pupilsGrid').length) {
                $btn.text(__('View More Information'));
            } else {
                $btn.text(__('Toggle Card View'));
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
        $(this).text('+' + __('Add New Professional')).removeClass('text-danger');
        
        // remove required attributes
        $('input[name="prof_first_name"]').removeAttr('required');
        $('input[name="prof_last_name"]').removeAttr('required');
    } else {
        // switch to new professional form
        $('#existing_professional_box').hide();
        $('#new_professional_box').show();
        $('#is_new_professional').val('1');
        $('#professional_id').val('');
        $(this).text(__('Cancel')).addClass('text-danger');
        
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

    var datatableConfigsNoFilters = {
        "paging": false,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
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
    $.fn.dataTable.ext.errMode = 'throw';
    $('.table:not(.no-datatable-filters)').each(function() {
        var $this = $(this);
        var config = $.extend(true, {}, datatableConfigs);
        
        if ($this.data('page-length')) {
            config.pageLength = $this.data('page-length');
        }
        
        $this.DataTable(config);
    });
    $('.table.no-datatable-filters').DataTable(datatableConfigsNoFilters);
});

function niceAlert(type, title, message) {
    let alertBox = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <strong>${title}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // inject into alert container and show
    $("#alert-container").html(alertBox).fadeIn();

    // auto-hide after 3 seconds
    setTimeout(function() {
        $("#alert-container").fadeOut();
    }, 3000);
}

$(document).ready(function() {
    $('.select2_multi_select').each(function() {
        var $this = $(this);
        $this.select2({
            width: '100%',
            placeholder: $this.data('placeholder'),
            closeOnSelect: false,
            dropdownParent: $('#' + $this.data('dropdown_parent'))
        });
    });

    // clear hover highlight when pointer leaves an open select2 results list
    $(document).on('select2:open', function() {
        var $results = $('.select2-container--open .select2-results__options');
        $results.off('mouseleave.select2clear').on('mouseleave.select2clear', function() {
            $(this)
                .find('.select2-results__option--highlighted.select2-results__option--selectable')
                .removeClass('select2-results__option--highlighted')
                .removeAttr('aria-selected');

            $('.select2-search__field').removeAttr('aria-activedescendant');
        });
    });
});

$(function() {
    $('.nav_disabled a').removeAttr('href');
});

// update attachment consent state based on file uploads
$(document).on('change', 'input[name="llm_attachment"], input[name="additional_attachments[]"]', function () {
    var $form = $(this).closest('form');
    if (!$form.length) return;

    var hasAudioVideo = false;

    $form.find('input[name="llm_attachment"], input[name="additional_attachments[]"]').each(function () {
        if (!this.files) return;

        for (var i = 0; i < this.files.length; i++) {
            var type = this.files[i].type;

            if (type.startsWith('audio/') || type.startsWith('video/')) {
                hasAudioVideo = true;
                return false; // break loop if audio/video file is found
            }
        }
    });

    $('.attachment_consent_checkbox').prop('disabled', !hasAudioVideo).prop('required', hasAudioVideo);
    
    if (!hasAudioVideo) {
        $('.attachment_consent_checkbox').prop('checked', false);
    }
});

$(document).on('click', '#toggle_treatment_plan_updates_btn', function() {
    const $container = $('#hidden_treatment_plan_updates');
    const $fade = $('#hidden_treatment_plan_updates .fade');
    const $btn = $(this);
    const $hiddenUpdates = $('.hidden_update');

    if ($container.css('max-height') == '65px') {
        $container.css('max-height', $container[0].scrollHeight + 'px');
        $fade.css('opacity', '0');
        $hiddenUpdates.css('opacity', '1');
        $btn.html('Show fewer updates <i class="fas fa-chevron-up ms-1"></i>');

        setTimeout(function () {
            if ($container.css('max-height') != '65px') {
                $container.css('max-height', 'none');
            }
        }, 300);
    } else {
        $container.css('max-height', $container[0].scrollHeight + 'px');
        $container[0].offsetWidth;
        $container.css('max-height', '65px');
        $fade.css('opacity', '1');
        $hiddenUpdates.css('opacity', '0.4');
        $btn.html('See all ' + $('#treatment_plan_updates_count').val() + ' updates <i class="fas fa-chevron-down ms-1"></i>');
    }
});