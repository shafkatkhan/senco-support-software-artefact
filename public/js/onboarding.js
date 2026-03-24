function toggleField(elementId, show) {
    const $element = $('#' + elementId);
    if (show) {
        $element.show();
    } else {
        // check if there is an open inline professional form, and reset it if so
        $element.find('button.text-danger.toggle_professional_btn').trigger('click');

        $element.hide();

        // clear all inputs/selects/textareas
        $element.find('input, select, textarea').each(function() {
            const $input = $(this);
            if ($input.is(':checkbox') || $input.is(':radio')) {
                $input.prop('checked', false);
            } else {
                $input.val('');
            }
        });
    }
}

// remove form row button
const removeBtn = `
    <div class="remove_btn_wrapper">
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.onboarding_inner_form').remove()"><i class="fas fa-times"></i></button>
    </div>
`;

function getInlineProfessionalForm(prefix, uid) {
    return `
        <div class="justify-between-flexbox mb-2" style="display: flex; justify-content: space-between; align-items: center;">
            <label class="form-label mb-0">` + __('Professional') + `</label>
            <button type="button" class="btn btn-sm btn-link p-0 toggle_professional_btn" data-uid="${uid}">${__('Add New Professional')}</button>
        </div>
        <div id="existing_professional_box_${uid}">
            <select class="form-select" name="${prefix}[professional_id]">
                <option value="" selected disabled>${__('None / Not Applicable')}</option>
                ${window.professionalOptions}
            </select>
        </div>
        <div id="new_professional_box_${uid}" style="display: none; background: #fdfdfd; border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; margin-top: 5px;">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <input type="text" class="form-control form-control-sm" name="${prefix}[prof_title]" placeholder="${__('Title')}">
                </div>
                <div class="col-md-4 mb-2">
                    <input type="text" class="form-control form-control-sm prof_first_name_input" name="${prefix}[prof_first_name]" placeholder="${__('First Name')}*">
                </div>
                <div class="col-md-5 mb-2">
                    <input type="text" class="form-control form-control-sm prof_last_name_input" name="${prefix}[prof_last_name]" placeholder="${__('Last Name')}*">
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" class="form-control form-control-sm" name="${prefix}[prof_role]" placeholder="${__('Role')}">
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" class="form-control form-control-sm" name="${prefix}[prof_agency]" placeholder="${__('Agency')}">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm" name="${prefix}[prof_phone]" placeholder="${__('Phone')}">
                </div>
                <div class="col-md-6">
                    <input type="email" class="form-control form-control-sm" name="${prefix}[prof_email]" placeholder="${__('Email')}">
                </div>
            </div>
            <input type="hidden" name="${prefix}[is_new_professional]" id="is_new_professional_${uid}" value="0">
        </div>
    `;
}

$(document).on('click', '.toggle_professional_btn', function() {
    const uid = $(this).data('uid');
    const existingBox = $('#existing_professional_box_' + uid);
    const newBox = $('#new_professional_box_' + uid);
    const isNewInput = $('#is_new_professional_' + uid);

    if (newBox.is(':hidden')) {
        // switch to new professional form
        existingBox.hide();
        newBox.show();
        isNewInput.val('1');
        $(this).text(__('Cancel')).addClass('text-danger');

        // clear existing professional selection
        existingBox.find('select').val('');

        // add required attributes
        newBox.find('.prof_first_name_input').attr('required', true);
        newBox.find('.prof_last_name_input').attr('required', true);
    } else {
        // switch to existing professional dropdown
        existingBox.show();
        newBox.hide();
        isNewInput.val('0');
        $(this).text('+ ' + __('Add New Professional')).removeClass('text-danger');

        // remove required attributes
        newBox.find('.prof_first_name_input').removeAttr('required');
        newBox.find('.prof_last_name_input').removeAttr('required');
        // clear fields
        newBox.find('input:not([type="hidden"])').val('');
    }
});

// initialise inline professional forms for social services and probation
$('#social_worker_form_container').html(getInlineProfessionalForm('social_worker', 'social_worker'));
$('#probation_officer_form_container').html(getInlineProfessionalForm('probation_officer', 'probation_officer'));

let familyMemberIdx = 0;
function addFamilyMemberRow() {
    const html = `
        <div class="onboarding_inner_form">
            ${removeBtn}
            <div class="row pt-2">
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('First Name')} <span class="text-danger">*</span></label>
                    <input type="text" name="family_members[${familyMemberIdx}][first_name]" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Last Name')} <span class="text-danger">*</span></label>
                    <input type="text" name="family_members[${familyMemberIdx}][last_name]" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Relation')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][relation]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Date of Birth')}</label>
                    <input type="date" name="family_members[${familyMemberIdx}][dob]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Phone')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][phone]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Email')}</label>
                    <input type="email" name="family_members[${familyMemberIdx}][email]" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Address Line 1')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][address_line_1]" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Address Line 2')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][address_line_2]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Town/City')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][locality]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Postcode')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][postcode]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Country')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][country]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Marital Status')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][marital_status]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Highest Education')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][highest_education]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Financial Status')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][financial_status]" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Occupation')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][occupation]" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('State Support/Benefits')}</label>
                    <input type="text" name="family_members[${familyMemberIdx}][state_support]" class="form-control">
                </div>
            </div>
        </div>
    `;
    $('.family_member_rows').append(html);
    familyMemberIdx++;
}

let schoolIdx = 0;
function addSchoolRow() {
    const html = `
        <div class="onboarding_inner_form">
            ${removeBtn}
            <div class="row pt-2">
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('School Name')} <span class="text-danger">*</span></label>
                    <input type="text" name="school_histories[${schoolIdx}][school_name]" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">${__('School Type')}</label>
                    <input type="text" name="school_histories[${schoolIdx}][school_type]" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">${__('Type of Class')}</label>
                    <input type="text" name="school_histories[${schoolIdx}][class_type]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Years Attended')}</label>
                    <input type="number" step="0.1" name="school_histories[${schoolIdx}][years_attended]" class="form-control">
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">${__('Reason for Transition')}</label>
                    <input type="text" name="school_histories[${schoolIdx}][transition_reason]" class="form-control">
                </div>
            </div>
        </div>
    `;
    $('.school_rows').append(html);
    schoolIdx++;
}

let diagnosisIdx = 0;
function addDiagnosisRow() {
    const html = `
        <div class="onboarding_inner_form">
            ${removeBtn}
            <div class="row pt-2">
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Diagnosis Name')} <span class="text-danger">*</span></label>
                    <input type="text" name="diagnoses[${diagnosisIdx}][name]" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Date Diagnosed')}</label>
                    <input type="date" name="diagnoses[${diagnosisIdx}][date]" class="form-control">
                </div>
                <div class="col-md-12 mb-3">
                    ${getInlineProfessionalForm(`diagnoses[${diagnosisIdx}]`, `diagnosis_${diagnosisIdx}`)}
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">${__('Description')}</label>
                    <textarea name="diagnoses[${diagnosisIdx}][description]" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">${__('Recommendations')}</label>
                    <textarea name="diagnoses[${diagnosisIdx}][recommendations]" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
    `;
    $('.diagnosis_rows').append(html);
    diagnosisIdx++;
}

let medicationIdx = 0;
function addMedicationRow() {
    const html = `
        <div class="onboarding_inner_form">
            ${removeBtn}
            <div class="row pt-2">
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Medication Name')} <span class="text-danger">*</span></label>
                    <input type="text" name="medications[${medicationIdx}][name]" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Dosage')}</label>
                    <input type="text" name="medications[${medicationIdx}][dosage]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">${__('Frequency')} <span class="text-danger">*</span></label>
                    <input type="text" name="medications[${medicationIdx}][frequency]" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Time of Day')}</label>
                    <input type="text" name="medications[${medicationIdx}][time_of_day]" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Method of Administration')}</label>
                    <input type="text" name="medications[${medicationIdx}][administration_method]" class="form-control">
                    <div class="form-text text-muted">e.g. oral, inhalation, etc.</div>
                </div>
                <div class="col-md-4 mb-3">
                <label class="form-label">${__('Start Date')} <span class="text-danger">*</span></label>
                <input type="date" name="medications[${medicationIdx}][start_date]" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                <label class="form-label">${__('End Date')}</label>
                <input type="date" name="medications[${medicationIdx}][end_date]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                <label class="form-label">${__('Expiry Date')}</label>
                <input type="date" name="medications[${medicationIdx}][expiry_date]" class="form-control">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">${__('Storage Instructions')}</label>
                    <textarea name="medications[${medicationIdx}][storage_instructions]" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input id="self_administer_${medicationIdx}" class="form-check-input" type="checkbox" name="medications[${medicationIdx}][self_administer]" value="1">
                        <label class="form-check-label" for="self_administer_${medicationIdx}">${__('Pupil can self-administer?')}</label>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('.medication_rows').append(html);
    medicationIdx++;
}

let recordIdx = 0;
function addRecordRow() {
    const html = `
        <div class="onboarding_inner_form">
            ${removeBtn}
            <div class="row pt-2">
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Record Type')} <span class="text-danger">*</span></label>
                    <select name="records[${recordIdx}][record_type_id]" class="form-select" required>
                        <option value="" selected disabled>--- ${__('Choose Type')} ---</option>
                        ${window.recordTypeOptions}
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Record Title')}</label>
                    <input type="text" name="records[${recordIdx}][title]" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Date')}</label>
                    <input type="date" name="records[${recordIdx}][date]" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">${__('Reference #')}</label>
                    <input type="text" name="records[${recordIdx}][reference_number]" class="form-control">
                </div>
                <div class="col-md-12 mb-3">
                    ${getInlineProfessionalForm(`records[${recordIdx}]`, `record_${recordIdx}`)}
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">${__('Description')} <span class="text-danger">*</span></label>
                    <textarea name="records[${recordIdx}][description]" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">${__('Outcome / Next Steps')}</label>
                    <input type="text" name="records[${recordIdx}][outcome]" class="form-control">
                </div>
            </div>
        </div>
    `;
    $('.record_rows').append(html);
    recordIdx++;
}