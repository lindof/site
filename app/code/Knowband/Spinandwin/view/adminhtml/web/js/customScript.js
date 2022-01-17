function validateConfigurationsForm()
{
    var is_error = false;
    var general_settings_tab = 0;
    var display_setting_tab = 0;
    var look_and_feel_tab = 0;
    var wheel_setting_tab = 0;
    var email_marketing_tab = 0;
    var email_setting_tab = 0;
    var text_setting_tab = 0;
    jQuery('.kb_error_message').remove();
    jQuery('.spin_error').remove();
    jQuery('#spinandwin_view input').removeClass('kb_error_field');
    jQuery('#spinandwin_view input').parent().removeClass('kb_error_field');
    jQuery('#spinandwin_view textarea').removeClass('kb_error_field');
    jQuery("span.admin__page-nav-item-message._error").hide();
    /*Knowband validation start*/
    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#spinandwin_displayinterval'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#spinandwin_displayinterval').addClass('kb_error_field');
        jQuery('#spinandwin_displayinterval').parent().after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        general_settings_tab = 1;
    } else {
        var disint_positive_err = velovalidation.isNumeric(jQuery('#spinandwin_displayinterval'), true);
        if (disint_positive_err != true) {
            is_error = true;
            jQuery('#spinandwin_displayinterval').addClass('kb_error_field');
            jQuery('#spinandwin_displayinterval').parent().after('<span class="kb_error_message">' + disint_positive_err + '</span>');
            general_settings_tab = 1;
        }
    }

    //Knowband Validation starts for pull out image
    if (jQuery('#spinandwin_pullouttab').is(':checked')) {
        var check_pullout_image = jQuery('#pullout_image').val();
        var get_preview_file = jQuery('#pullout_image_preview').attr('src');
        if (check_pullout_image != '') {
            var disint_mandatory_err = velovalidation.checkImage(jQuery('#pullout_image'), 2097152, 'b');
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#pullout_image').addClass('kb_error_field');
                jQuery('#pullout_image').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            }
            general_settings_tab = 1;
        }
    }
    //Knowband Validation endss for pull out image


    if (jQuery('#spinandwin_enable_progress_bar').is(':checked')) {
        var progress_percentage_mand = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[general_settings][progress_percentage]']"));
        if (progress_percentage_mand !== true) {
            is_error = true;
            jQuery("input[name='vss_spinandwin[general_settings][progress_percentage]']").addClass('kb_error_field');
            jQuery("input[name='vss_spinandwin[general_settings][progress_percentage]']").after(jQuery('<p class="kb_error_message"">' + progress_percentage_mand + '</p>'));
            general_settings_tab = 1;
        } else {
            var progress_percentage_mand = velovalidation.checkPercentage(jQuery("input[name='vss_spinandwin[general_settings][progress_percentage]']"));
            if (progress_percentage_mand !== true) {
                is_error = true;
                jQuery("input[name='vss_spinandwin[general_settings][progress_percentage]']").addClass('kb_error_field');
                jQuery("input[name='vss_spinandwin[general_settings][progress_percentage]']").after(jQuery('<p class="kb_error_message"">' + progress_percentage_mand + '</p>'));
                general_settings_tab = 1;
            }
        }

        var progress_text_mand = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[general_settings][progress_text]']"));
        if (progress_text_mand !== true) {
            is_error = true;
            jQuery("input[name='vss_spinandwin[general_settings][progress_text]']").addClass('kb_error_field');
            jQuery("input[name='vss_spinandwin[general_settings][progress_text]']").after(jQuery('<p class="kb_error_message"">' + progress_text_mand + '</p>'));
            general_settings_tab = 1;
        }
    }

    var customcss_tags_err = velovalidation.checkTags(jQuery('#spinandwin_customcss'));
    if (customcss_tags_err != true)
    {
        is_error = true;
        jQuery('#spinandwin_customcss').addClass('kb_error_field');
        jQuery('#spinandwin_customcss').after('<span class="kb_error_message">' + customcss_tags_err + '</span>');
        general_settings_tab = 1;
    }

    var customcss_tags_err = velovalidation.checkTags(jQuery('#spinandwin_customjs'));
    if (customcss_tags_err != true)
    {
        is_error = true;
        jQuery('#spinandwin_customjs').addClass('kb_error_field');
        jQuery('#spinandwin_customjs').after('<span class="kb_error_message">' + customcss_tags_err + '</span>');
        general_settings_tab = 1;
    }

    if (jQuery('#spinandwin_fix_time').is(':checked')) {
        var date_error = false;
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#spinandwin_active_date'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#spinandwin_active_date').addClass('kb_error_field');
            jQuery('#spinandwin_active_date').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            display_setting_tab = 1;
            date_error = true;
        }
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#spinandwin_expire_date'));
        if (disint_mandatory_err != true) {
            is_error = true;
            jQuery('#spinandwin_expire_date').addClass('kb_error_field');
            jQuery('#spinandwin_expire_date').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            display_setting_tab = 1;
            date_error = true;
        }
        if (!date_error) {
            /*Knowband button validation start*/
            var activation_date = Date.parse(jQuery('#spinandwin_active_date').val());
            var deactivation_date = Date.parse(jQuery('#spinandwin_expire_date').val());
            if (parseInt(deactivation_date) < parseInt(activation_date)) {
                is_error = true;
                jQuery('#spinandwin_expire_date').addClass('kb_error_field');
                jQuery('#spinandwin_expire_date').next('button').after('<span class="kb_error_message" style="margin-left: 0px;float: left;">' + date_range_error + '</span>');
                display_setting_tab = 1;
            }
        }

    }

//	if (jQuery('#spinandwin_when_to_show').val() == '2') {
//		var disint_mandatory_err = velovalidation.isNumeric(jQuery('#spinandwin_time_display'), true);
//		if (disint_mandatory_err != true) {
//			is_error = true;
//			jQuery('#spinandwin_time_display').addClass('kb_error_field');
//			jQuery('#spinandwin_time_display').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
//                        display_setting_tab = 1;
//		}		
//	}
//	
//	if (jQuery('#spinandwin_when_to_show').val() == '3') {
//		var disint_mandatory_err = velovalidation.checkPercentage(jQuery('#spinandwin_scroll_display'), true);
//		if (disint_mandatory_err != true) {
//			is_error = true;
//			jQuery('#spinandwin_scroll_display').addClass('kb_error_field');
//			jQuery('#spinandwin_scroll_display').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
//                        display_setting_tab = 1;
//		}		
//	}

    if (jQuery('#spinandwin_geo_location').val() == '2') {
        var selected_country = jQuery("#spinandwin_selected_country").val();
        if (selected_country === null) {
            is_error = true;
            jQuery("#spinandwin_selected_country").addClass('kb_error_field');
            jQuery("#spinandwin_selected_country").after(jQuery('<p class="selected_country spin_error"></p>'));
            jQuery('.selected_country').text(selected_country_error);
            display_setting_tab = 1;
        }
    }
    if (jQuery('#spinandwin_geo_location').val() == '3') {
        var selected_country = jQuery("#spinandwin_selected_country").val();
        if (selected_country === null) {
            is_error = true;
            jQuery("#spinandwin_selected_country").addClass('kb_error_field');
            jQuery("#spinandwin_selected_country").after(jQuery('<p class="selected_country spin_error"></p>'));
            jQuery('.selected_country').text(selected_country_error);
            display_setting_tab = 1;
        }
    }

    if (jQuery('#spinandwin_when_to_show').val() === '2') {
        var time_display = velovalidation.checkMandatory(jQuery("#spinandwin_time_display"));
        if (time_display !== true) {
            is_error = true;
            jQuery("#spinandwin_time_display").addClass('kb_error_field');
            jQuery("#spinandwin_time_display").after(jQuery('<p class="time_display spin_error"></p>'));
            jQuery('.time_display').html(time_display);
            display_setting_tab = 1;
        } else {
            var time_display_num = velovalidation.isNumeric(jQuery("#spinandwin_time_display"));
            if (time_display_num != true) {
                is_error = true;
                jQuery("#spinandwin_time_display").addClass('kb_error_field');
                jQuery("#spinandwin_time_display").after(jQuery('<p class="time_display spin_error">' + time_display_num + '</p>'));
                display_setting_tab = 1;
            }
        }
    }

    if (jQuery('#spinandwin_when_to_show').val() === '3') {
        var scroll_display = velovalidation.checkMandatory(jQuery("#spinandwin_scroll_display"));
        if (scroll_display !== true) {
            is_error = true;
            jQuery("#spinandwin_scroll_display").addClass('error_field');
            jQuery("#spinandwin_scroll_display").after(jQuery('<p class="scroll_display spin_error"></p>'));
            jQuery('.scroll_display').html(scroll_display);
            display_setting_tab = 1;
        } else {
            var scroll_display_num = velovalidation.checkPercentage(jQuery("#spinandwin_scroll_display"));
            if (scroll_display_num != true) {
                is_error = true;
                jQuery("#spinandwin_scroll_display").addClass('error_field');
                jQuery("#spinandwin_scroll_display").after(jQuery('<p class="scroll_display spin_error">' + scroll_display_num + '</p>'));
            }
        }
    }

    /*Knowband validation start*/
    if (jQuery('[name="vss_spinandwin[email_marketing][mailchimp_enable]"]').is(":checked")) {
        var mailchimp_man_err = velovalidation.checkMandatory(jQuery('[name="vss_spinandwin[email_marketing][mailchimp_api]"]'));
        var list_val = jQuery("[name='vss_spinandwin[email_marketing][mailchimp_list]']").val();
        if (mailchimp_man_err != true)
        {
            is_error = true;
            email_marketing_error = true;
            jQuery('[name="vss_spinandwin[email_marketing][mailchimp_api]"]').addClass('kb_error_field');
            jQuery('[name="vss_spinandwin[email_marketing][mailchimp_api]"]').after('<span class="kb_error_message">' + mailchimp_man_err + '</span>');
            email_marketing_tab = 1;
        } else if (list_val == 'no_list') {
            is_error = true;

            jQuery("input[name='vss_spinandwin[email_marketing][mailchimp_api]']").addClass('error_field');
            jQuery("input[name='vss_spinandwin[email_marketing][mailchimp_api]']").after(jQuery('<p class="mailchimp_api_mand spin_error"></p>'));
            jQuery('.mailchimp_api_mand').html(no_list_mailchimp);
            email_marketing_tab = 1;
        }
    }

    if (jQuery('[name="vss_spinandwin[email_marketing][klaviyo_enable]"]').is(":checked")) {
        var klaviyo_man_err = velovalidation.checkMandatory(jQuery('[name="vss_spinandwin[email_marketing][klaviyo_api]"]'));
        var list_val = jQuery("[name='vss_spinandwin[email_marketing][klaviyo_list]']").val();
        if (klaviyo_man_err != true)
        {
            is_error = true;
            email_marketing_error = true;
            jQuery('[name="vss_spinandwin[email_marketing][klaviyo_api]"]').addClass('kb_error_field');
            jQuery('[name="vss_spinandwin[email_marketing][klaviyo_api]"]').after('<span class="kb_error_message">' + klaviyo_man_err + '</span>');
            email_marketing_tab = 1;
        } else if (list_val == 'no_list') {
            is_error = true;

            jQuery("input[name='vss_spinandwin[email_marketing][klaviyo_api]']").addClass('error_field');
            jQuery("input[name='vss_spinandwin[email_marketing][klaviyo_api]']").after(jQuery('<p class="mailchimp_api_mand spin_error"></p>'));
            jQuery('.mailchimp_api_mand').html(no_list_mailchimp);
            email_marketing_tab = 1;
        }
    }

    if (jQuery('[name="vss_spinandwin[email_marketing][constant_contact_enable]"]').is(":checked")) {
        var constant_contact_man_err = velovalidation.checkMandatory(jQuery('[name="vss_spinandwin[email_marketing][constant_contact_api]"]'));
        var list_val = jQuery("[name='vss_spinandwin[email_marketing][constant_contact_list]']").val();
        if (constant_contact_man_err != true)
        {
            is_error = true;
            email_marketing_error = true;
            jQuery('[name="vss_spinandwin[email_marketing][constant_contact_api]"]').addClass('kb_error_field');
            jQuery('[name="vss_spinandwin[email_marketing][constant_contact_api]"]').after('<span class="kb_error_message">' + constant_contact_man_err + '</span>');
            email_marketing_tab = 1;
        } else if (list_val == 'no_list') {
            is_error = true;

            jQuery("input[name='vss_spinandwin[email_marketing][constant_contact_api]']").addClass('error_field');
            jQuery("input[name='vss_spinandwin[email_marketing][constant_contact_api]']").after(jQuery('<p class="mailchimp_api_mand spin_error"></p>'));
            jQuery('.mailchimp_api_mand').html(no_list_mailchimp);
            email_marketing_tab = 1;
        }
    }

    if (jQuery('[name="vss_spinandwin[email_marketing][constant_contact_token]"]').is(":checked")) {
        var constant_contact_man_err = velovalidation.checkMandatory(jQuery('[name="vss_spinandwin[email_marketing][constant_contact_token]"]'));
        if (constant_contact_man_err != true)
        {
            is_error = true;
            email_marketing_error = true;
            jQuery('[name="vss_spinandwin[email_marketing][constant_contact_token]"]').addClass('kb_error_field');
            jQuery('[name="vss_spinandwin[email_marketing][constant_contact_token]"]').after('<span class="kb_error_message">' + constant_contact_man_err + '</span>');
            email_marketing_tab = 1;
        }
    }

    var enter_text_mand = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_spin]']"));
    var enter_text_color_check = velovalidation.isColor(jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_spin]']"));
    if (enter_text_mand !== true) {
        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_spin]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_spin]']").next('.mColorPickerTrigger').after(jQuery('<p class="enter_text_mand spin_error""></p>'));
        jQuery('.enter_text_mand').html(enter_text_mand);
        look_and_feel_tab = 1;
    } else if (enter_text_color_check !== true) {
        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_spin]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_spin]']").next('.mColorPickerTrigger').after(jQuery('<p class="enter_text_color_check spin_error""></p>'));
        jQuery('.enter_text_color_check').html(enter_text_color_check);
        look_and_feel_tab = 1;
    }

    var enter_text_mand_cancel = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_cancel]']"));
    var enter_text_color_check_cancel = velovalidation.isColor(jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_cancel]']"));
    if (enter_text_mand_cancel !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_cancel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_cancel]']").next('.mColorPickerTrigger').after(jQuery('<p class="enter_text_mand_cancel spin_error""></p>'));
        jQuery('.enter_text_mand_cancel').html(enter_text_mand_cancel);
        look_and_feel_tab = 1;
    } else if (enter_text_color_check_cancel !== true) {
        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_cancel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_cancel]']").next('.mColorPickerTrigger').after(jQuery('<p class="enter_text_color_check_cancel spin_error"></p>'));
        jQuery('.enter_text_color_check_cancel').html(enter_text_color_check_cancel);
        look_and_feel_tab = 1;
    }

    // Added by Dhruw For validation

    if (jQuery("#spinandwin_wheel_design").val() == '2') {
        var wheel_color_mdt = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_color]']"));
        var wheel_color_err = velovalidation.isColor(jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_color]']"));
        if (wheel_color_mdt !== true) {
            is_error = true;
            jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_color]']").addClass('kb_error_field');
            jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="wheel_color_mdt spin_error""></p>'));
            jQuery('.wheel_color_mdt').html(wheel_color_mdt);
            look_and_feel_tab = 1;
        } else if (wheel_color_err !== true) {
            //            jQuery('.enter_text_color_check_cancel').remove();
            is_error = true;
            jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_color]']").addClass('kb_error_field');
            jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="wheel_color_err spin_error""></p>'));
            jQuery('.wheel_color_err').html(wheel_text_color);
            look_and_feel_tab = 1;
        }
    }

    var wheel_text = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_text_color]']"));
    var wheel_text_color = velovalidation.isColor(jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_text_color]']"));
    if (wheel_text !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_text_color]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_text_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="wheel_text spin_error""></p>'));
        jQuery('.wheel_text').html(wheel_text);
        look_and_feel_tab = 1;
    } else if (wheel_text_color !== true) {
//            jQuery('.enter_text_color_check_cancel').remove();
        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_text_color]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][wheel_text_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="wheel_text_color spin_error""></p>'));
        jQuery('.wheel_text_color').html(wheel_text_color);
        look_and_feel_tab = 1;
    }

    var try_luck_text = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[lookandfeel_settings][try_luck_text_color]']"));
    var try_luck_text_color = velovalidation.isColor(jQuery("input[name='vss_spinandwin[lookandfeel_settings][try_luck_text_color]']"));
    if (try_luck_text !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][try_luck_text_color]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][try_luck_text_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="try_luck_text spin_error""></p>'));
        jQuery('.try_luck_text').html(try_luck_text);
        look_and_feel_tab = 1;
    } else if (try_luck_text_color !== true) {
//            jQuery('.enter_text_color_check_cancel').remove();
        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][try_luck_text_color]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][try_luck_text_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="try_luck_text_color spin_error""></p>'));
        jQuery('.try_luck_text_color').html(try_luck_text_color);
        look_and_feel_tab = 1;
    }
    //Ends Validation

    var background_wheel = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_wheel]']"));
    var background_wheel_color = velovalidation.isColor(jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_wheel]']"));
    if (background_wheel !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_wheel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_wheel]']").next('.mColorPickerTrigger').after(jQuery('<p class="background_wheel spin_error""></p>'));
        jQuery('.background_wheel').html(background_wheel);
        look_and_feel_tab = 1;
    } else if (background_wheel_color !== true) {
        jQuery('.enter_text_color_check_cancel').remove();

        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_wheel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][background_color_wheel]']").next('.mColorPickerTrigger').after(jQuery('<p class="background_wheel_color spin_error""></p>'));
        jQuery('.background_wheel_color').html(background_wheel_color);
        look_and_feel_tab = 1;
    }

    var text_color_mand = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[lookandfeel_settings][text_color_wheel]']"));
    var text_color_check = velovalidation.isColor(jQuery("input[name='vss_spinandwin[lookandfeel_settings][text_color_wheel]']"));
    if (text_color_mand !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][text_color_wheel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][text_color_wheel]']").next('.mColorPickerTrigger').after(jQuery('<p class="text_color_mand spin_error""></p>'));
        jQuery('.text_color_mand').html(text_color_mand);
        look_and_feel_tab = 1;
    } else if (text_color_check !== true) {


        is_error = true;
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][text_color_wheel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[lookandfeel_settings][text_color_wheel]']").next('.mColorPickerTrigger').after(jQuery('<p class="text_color_check spin_error""></p>'));
        jQuery('.text_color_check').html(text_color_check);
        look_and_feel_tab = 1;
    }
    /*Knowband validation end*/
    if (jQuery('#spinandwin_logo_enable').is(':checked')) {
        var check_logo_image = jQuery('#logo_image').val();
        var get_preview_file = jQuery('#logo_image_preview').attr('src');
        if (get_preview_file.indexOf('no_image') > 0 && check_logo_image == '') {
            is_error = true;
            jQuery('#logo_image').addClass('kb_error_field');
            jQuery('#logo_image').after('<span class="kb_error_message">' + image_empty + '</span>');
            look_and_feel_tab = 1;
        } else if (check_logo_image != '') {
            var disint_mandatory_err = velovalidation.checkImage(jQuery('#logo_image'), 2097152, 'b');
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#logo_image').addClass('kb_error_field');
                jQuery('#logo_image').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            }
            look_and_feel_tab = 1;
        }
    }
    /*Knowband validation end*/


    /*Knowband validation start*/
    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#spinandwin_title_text_1'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#spinandwin_title_text_1').addClass('kb_error_field');
        jQuery('#spinandwin_title_text_1').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        text_setting_tab = 1;
    }
    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#spinandwin_title_text_2'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#spinandwin_title_text_2').addClass('kb_error_field');
        jQuery('#spinandwin_title_text_2').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        text_setting_tab = 1;
    }
    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#spinandwin_title_text_3'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#spinandwin_title_text_3').addClass('kb_error_field');
        jQuery('#spinandwin_title_text_3').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        text_setting_tab = 1;
    }
    /*Knowband validation end*/



    /*Knowband validation start*/
    jQuery('#slice-settings input').each(function (i, obj) {
        if (jQuery(this).hasClass('vss_slice_label')) {
            var slice_label_man_err = velovalidation.checkMandatory(jQuery(this));
            if (slice_label_man_err != true)
            {
                is_error = true;
                slice_setting_error = true;
                jQuery(this).addClass('kb_error_field');
                jQuery(this).after('<span class="kb_error_message" style="display: inline-block;">' + slice_label_man_err + '</span>');
                wheel_setting_tab = 1;
            }
        } else if (jQuery(this).hasClass('vss_slice_value') || jQuery(this).hasClass('vss_slice_gravity')) {
            var slice_man_err = velovalidation.checkMandatory(jQuery(this));
            if (slice_man_err != true)
            {
                is_error = true;
                slice_setting_error = true;
                jQuery(this).addClass('kb_error_field');
                jQuery(this).after('<span class="kb_error_message" style="display: inline-block;">' + slice_man_err + '</span>');
                wheel_setting_tab = 1;
            } else
            {
                var slice_positive_err = velovalidation.isNumeric(jQuery(this), true);
                if (slice_positive_err != true)
                {
                    is_error = true;
                    slice_setting_error = true;
                    jQuery(this).addClass('kb_error_field');
                    jQuery(this).after('<span class="kb_error_message" style="display: inline-block;">' + slice_positive_err + '</span>');
                    wheel_setting_tab = 1;
                } else
                {
                    if (jQuery(this).hasClass('vss_slice_value')) {
                        if (jQuery(this).parent().parent().find('select').val() == 'P') {
                            var slice_bet_err = velovalidation.isBetween(jQuery(this), 0, 100);
                            if (slice_bet_err != true)
                            {
                                is_error = true;
                                slice_setting_error = true;
                                jQuery(this).addClass('kb_error_field');
                                jQuery(this).after('<span class="kb_error_message" style="display: inline-block;">' + slice_bet_err + '</span>');
                                wheel_setting_tab = 1;
                            }
                        }
                    } else {
                        var slice_bet_err = velovalidation.isBetween(jQuery(this), 0, 100);
                        if (slice_bet_err != true)
                        {
                            is_error = true;
                            slice_setting_error = true;
                            jQuery(this).addClass('kb_error_field');
                            jQuery(this).after('<span class="kb_error_message" style="display: inline-block;">' + slice_bet_err + '</span>');
                            wheel_setting_tab = 1;
                        }
                    }
                }
            }
        }
    });
    var gravity_total = 0;
    var number_of_slices = parseInt(jQuery("#spinandwin_number_of_slices").val());
    var count = parseInt(1);
    jQuery('#slice-settings input.vss_slice_gravity').each(function (i, obj) {

        if (count <= number_of_slices) {
            gravity_total += parseInt(jQuery(this).val());
            count++;
        }
    });

    if (gravity_total < 1 || gravity_total > 100) {
        is_error = true;
        slice_setting_error = true;
        jQuery('.vss_slice_gravity_td').addClass('kb_error_field');
        jQuery('#vss_slice_settings_table').after('<span class="kb_error_message">' + gravity_range_error + gravity_total + '</span>');
        wheel_setting_tab = 1;
    }
    /*Knowband validation end*/

//	if (jQuery('#spinandwin_logo_enable').is(':checked')) {
//		var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#logo_image'));
//		
//		if (disint_mandatory_err != true) {
//			is_error = true;
//			jQuery('#logo_image').addClass('kb_error_field');
//			jQuery('#logo_image').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
//		}else{
//			var disint_mandatory_err = velovalidation.checkImage(jQuery('#logo_image'), 2097152);
//			if (disint_mandatory_err != true) {
//				is_error = true;
//				jQuery('#logo_image').addClass('kb_error_field');
//				jQuery('#logo_image').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
//			}
//		}		
//	}

    if (jQuery('#spinandwin_coupon_display_options').val() != '1') {
        var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#spinandwin_email_subject'));
        if (disint_mandatory_err != true)
        {
            is_error = true;
            email_setting_tab = 1;
            jQuery('#spinandwin_email_subject').addClass('kb_error_field');
            jQuery('#spinandwin_email_subject').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        }
        var email_err1 = tinyMCE.get('spinandwin_email_content').getContent().trim();
        if (email_err1 == '') {
            jQuery('.admin__field-control.admin__control-wysiwig').addClass('kb_error_field');
            jQuery('.admin__field-control.admin__control-wysiwig').after('<span class="kb_error_message" style="margin-left: 270px;">' + velovalidation.error('empty_field') + '</span>');
            email_setting_tab = 1;
            is_error = true;
        }
    }


    if (is_error) {
        if (general_settings_tab === 1) {
            jQuery("#spinandwin_view_tabs_spinandwin_general > span.admin__page-nav-item-messages > span.admin__page-nav-item-message._error").show();
        }
        if (display_setting_tab === 1) {
            jQuery("#spinandwin_view_tabs_spinandwin_display > span.admin__page-nav-item-messages > span.admin__page-nav-item-message._error").show();
        }
        if (look_and_feel_tab === 1) {
            jQuery("#spinandwin_view_tabs_spinandwin_look_and_feel > span.admin__page-nav-item-messages > span.admin__page-nav-item-message._error").show();
        }
        if (text_setting_tab === 1) {
            jQuery("#spinandwin_view_tabs_spinandwin_text_settings > span.admin__page-nav-item-messages > span.admin__page-nav-item-message._error").show();
        }
        if (wheel_setting_tab === 1) {
            jQuery("#spinandwin_view_tabs_spinandwin_slice_settings > span.admin__page-nav-item-messages > span.admin__page-nav-item-message._error").show();
        }
        if (email_marketing_tab === 1) {
            jQuery("#spinandwin_view_tabs_spinandwin_email_marketing > span.admin__page-nav-item-messages > span.admin__page-nav-item-message._error").show();
        }
        if (email_setting_tab === 1) {
            jQuery("#spinandwin_view_tabs_spinandwin_email_settings > span.admin__page-nav-item-messages > span.admin__page-nav-item-message._error").show();
        }
        return false;
    }

    /*Knowband button validation start*/
    jQuery('#save-spinandwin').attr('disabled', 'disabled');
    /*Knowband button validation end*/

    return true;
}

function validateScheduleForm()
{
    var is_error = false;

    jQuery('.kb_error_message').remove();
    jQuery('.spin_error').remove();
    jQuery('#spinandwin_view input').removeClass('kb_error_field');
    jQuery('#spinandwin_view input').parent().removeClass('kb_error_field');
    jQuery('#spinandwin_view textarea').removeClass('kb_error_field');
    jQuery("span.admin__page-nav-item-message._error").hide();


    var date_error = false;
    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#spinandwin_from_date'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#spinandwin_from_date').addClass('kb_error_field');
        jQuery('#spinandwin_from_date').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        display_setting_tab = 1;
        date_error = true;
    }
    var disint_mandatory_err = velovalidation.checkMandatory(jQuery('#spinandwin_to_date'));
    if (disint_mandatory_err != true) {
        is_error = true;
        jQuery('#spinandwin_to_date').addClass('kb_error_field');
        jQuery('#spinandwin_to_date').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
        display_setting_tab = 1;
        date_error = true;
    }
    if (!date_error) {
        /*Knowband button validation start*/
        var activation_date = Date.parse(jQuery('#spinandwin_from_date').val());
        var deactivation_date = Date.parse(jQuery('#spinandwin_to_date').val());
        if (parseInt(deactivation_date) < parseInt(activation_date)) {
            is_error = true;
            jQuery('#spinandwin_from_date').addClass('kb_error_field');
            jQuery('#spinandwin_from_date').parent().append('<span class="kb_error_message">' + date_range_error + '</span>');
            display_setting_tab = 1;
        }
    }


    var enter_text_mand = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[background_color_spin]']"));
    var enter_text_color_check = velovalidation.isColor(jQuery("input[name='vss_spinandwin[background_color_spin]']"));
    if (enter_text_mand !== true) {
        is_error = true;
        jQuery("input[name='vss_spinandwin[background_color_spin]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[background_color_spin]']").next('.mColorPickerTrigger').after(jQuery('<p class="enter_text_mand spin_error""></p>'));
        jQuery('.enter_text_mand').html(enter_text_mand);

    } else if (enter_text_color_check !== true) {
        is_error = true;
        jQuery("input[name='vss_spinandwin[background_color_spin]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[background_color_spin]']").next('.mColorPickerTrigger').after(jQuery('<p class="enter_text_color_check spin_error""></p>'));
        jQuery('.enter_text_color_check').html(enter_text_color_check);

    }

    var enter_text_mand_cancel = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[background_color_cancel]']"));
    var enter_text_color_check_cancel = velovalidation.isColor(jQuery("input[name='vss_spinandwin[background_color_cancel]']"));
    if (enter_text_mand_cancel !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[background_color_cancel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[background_color_cancel]']").next('.mColorPickerTrigger').after(jQuery('<p class="enter_text_mand_cancel spin_error""></p>'));
        jQuery('.enter_text_mand_cancel').html(enter_text_mand_cancel);

    } else if (enter_text_color_check_cancel !== true) {
        is_error = true;
        jQuery("input[name='vss_spinandwin[background_color_cancel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[background_color_cancel]']").next('.mColorPickerTrigger').after(jQuery('<p class="enter_text_color_check_cancel spin_error"></p>'));
        jQuery('.enter_text_color_check_cancel').html(enter_text_color_check_cancel);

    }

    // Added by Dhruw For validation

    if (jQuery("#spinandwin_wheel_design").val() == '2') {
        var wheel_color_mdt = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[wheel_color]']"));
        var wheel_color_err = velovalidation.isColor(jQuery("input[name='vss_spinandwin[wheel_color]']"));
        if (wheel_color_mdt !== true) {
            is_error = true;
            jQuery("input[name='vss_spinandwin[wheel_color]']").addClass('kb_error_field');
            jQuery("input[name='vss_spinandwin[wheel_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="wheel_color_mdt spin_error""></p>'));
            jQuery('.wheel_color_mdt').html(wheel_color_mdt);

        } else if (wheel_color_err !== true) {

            is_error = true;
            jQuery("input[name='vss_spinandwin[wheel_color]']").addClass('kb_error_field');
            jQuery("input[name='vss_spinandwin[wheel_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="wheel_color_err spin_error""></p>'));
            jQuery('.wheel_color_err').html(wheel_text_color);

        }
    }

    var wheel_text = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[wheel_text_color]']"));
    var wheel_text_color = velovalidation.isColor(jQuery("input[name='vss_spinandwin[wheel_text_color]']"));
    if (wheel_text !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[wheel_text_color]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[wheel_text_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="wheel_text spin_error""></p>'));
        jQuery('.wheel_text').html(wheel_text);

    } else if (wheel_text_color !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[wheel_text_color]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[wheel_text_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="wheel_text_color spin_error""></p>'));
        jQuery('.wheel_text_color').html(wheel_text_color);

    }

    var try_luck_text = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[try_luck_text_color]']"));
    var try_luck_text_color = velovalidation.isColor(jQuery("input[name='vss_spinandwin[try_luck_text_color]']"));
    if (try_luck_text !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[try_luck_text_color]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[try_luck_text_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="try_luck_text spin_error""></p>'));
        jQuery('.try_luck_text').html(try_luck_text);

    } else if (try_luck_text_color !== true) {
        is_error = true;
        jQuery("input[name='vss_spinandwin[try_luck_text_color]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[try_luck_text_color]']").next('.mColorPickerTrigger').after(jQuery('<p class="try_luck_text_color spin_error""></p>'));
        jQuery('.try_luck_text_color').html(try_luck_text_color);

    }
    //Ends Validation

    var background_wheel = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[background_color_wheel]']"));
    var background_wheel_color = velovalidation.isColor(jQuery("input[name='vss_spinandwin[background_color_wheel]']"));
    if (background_wheel !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[background_color_wheel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[background_color_wheel]']").next('.mColorPickerTrigger').after(jQuery('<p class="background_wheel spin_error""></p>'));
        jQuery('.background_wheel').html(background_wheel);

    } else if (background_wheel_color !== true) {
        jQuery('.enter_text_color_check_cancel').remove();

        is_error = true;
        jQuery("input[name='vss_spinandwin[background_color_wheel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[background_color_wheel]']").next('.mColorPickerTrigger').after(jQuery('<p class="background_wheel_color spin_error""></p>'));
        jQuery('.background_wheel_color').html(background_wheel_color);

    }

    var text_color_mand = velovalidation.checkMandatory(jQuery("input[name='vss_spinandwin[text_color_wheel]']"));
    var text_color_check = velovalidation.isColor(jQuery("input[name='vss_spinandwin[text_color_wheel]']"));
    if (text_color_mand !== true) {

        is_error = true;
        jQuery("input[name='vss_spinandwin[text_color_wheel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[text_color_wheel]']").next('.mColorPickerTrigger').after(jQuery('<p class="text_color_mand spin_error""></p>'));
        jQuery('.text_color_mand').html(text_color_mand);

    } else if (text_color_check !== true) {


        is_error = true;
        jQuery("input[name='vss_spinandwin[text_color_wheel]']").addClass('kb_error_field');
        jQuery("input[name='vss_spinandwin[text_color_wheel]']").next('.mColorPickerTrigger').after(jQuery('<p class="text_color_check spin_error""></p>'));
        jQuery('.text_color_check').html(text_color_check);

    }
    /*Knowband validation end*/
    if (jQuery('#spinandwin_logo_enable').is(':checked')) {
        var check_logo_image = jQuery('#logo_image').val();
        var get_preview_file = jQuery('#logo_image_preview').attr('src');
        if (get_preview_file.indexOf('no_image') > 0 && check_logo_image == '') {
            is_error = true;
            jQuery('#logo_image').addClass('kb_error_field');
            jQuery('#logo_image').after('<span class="kb_error_message">' + image_empty + '</span>');

        } else if (check_logo_image != '') {
            var disint_mandatory_err = velovalidation.checkImage(jQuery('#logo_image'), 2097152, 'b');
            if (disint_mandatory_err != true) {
                is_error = true;
                jQuery('#logo_image').addClass('kb_error_field');
                jQuery('#logo_image').after('<span class="kb_error_message">' + disint_mandatory_err + '</span>');
            }

        }
    }

    /*Knowband validation end*/




    if (is_error) {
        return false;
    }
    jQuery.ajax({
        url: validate_schedule_url,
        type: 'post',
        data: jQuery("#spinandwin_schedule_view").serialize(),
        dataType: 'json',
        showLoader: true,
        success: function (response) {
            if (!response.error) {
                jQuery("#spinandwin_schedule_view").submit();
                jQuery("body").loader("show");
            } else {
                if (response.from_date_error) {
                    jQuery('#spinandwin_from_date').addClass('kb_error_field');
                    jQuery('#spinandwin_from_date').parent().append('<span class="kb_error_message">' + from_date_error + '</span>');
                }

                if (response.to_date_error) {
                    jQuery('#spinandwin_to_date').addClass('kb_error_field');
                    jQuery('#spinandwin_to_date').parent().append('<span class="kb_error_message">' + to_date_error + '</span>');
                }
                
                if (response.slot_already_available) {
                    jQuery('#spinandwin_to_date').addClass('kb_error_field');
                    jQuery('#spinandwin_to_date').parent().append('<span class="kb_error_message">' + slot_already_available_error + '</span>');
                }

            }
        }
    });
}

function disableCouponValue(i) {
    var getval = jQuery('select[name="vss_spinandwin[slice_settings][slice_' + i + '][coupon_type]"]').val();
    if (getval == 'S') {
        jQuery('input[name="vss_spinandwin[slice_settings][slice_' + i + '][coupon_value]"]').val(0);
        jQuery('input[name="vss_spinandwin[slice_settings][slice_' + i + '][coupon_value]"]').prop('readonly', true);
    } else {
        jQuery('input[name="vss_spinandwin[slice_settings][slice_' + i + '][coupon_value]"]').prop('readonly', false);
    }
}
/*
 * Function for change color of wheel
 * @returns {undefined}
 */
function changeColor(wheel_color)
{
    if (wheel_color == "") {
        document.getElementById("wheel_preview").style.filter = "";
    } else {
        velsofWheelHexCode = wheel_color;
        var colorRGB = hexToRgb(velsofWheelHexCode);
        var hslColorCode = rgb2hsb(colorRGB.r, colorRGB.g, colorRGB.b);
        document.getElementById("wheel_preview").style.filter = 'hue-rotate(' + hslColorCode.hue + 'deg) saturate(' + hslColorCode.sat + '%) contrast(1.1)';
    }
}

function handleMailChimpApiSelection() {

    jQuery('.spin_error').remove();
    var api_key = jQuery("#spinandwin_mailchimp_api").val().trim();
    var clickmailchimphtml = '';
    if (api_key != '') {
        jQuery.ajax({
            url: lists_ajax_url,
            type: 'post',
            data: 'ajax=true&method=mailchimp&api_key=' + api_key,
            dataType: 'json',
            showLoader: true,
            success: function (json) {
                if (json['error'] !== undefined) {
                    jQuery("#spinandwin_mailchimp_list").html('<option value="no_list">' + json['error'][0]['label'] + '</option>');
                    jQuery("#spinandwin_mailchimp_list").css('border', '1px solid #ff0000');
                } else {
                    jQuery.each(json['success'], function (index, element) {
                        if (email_marketing_values['mailchimp_list'] == element.value) {
                            clickmailchimphtml += '<option value="' + element.value + '" selected>' + element.label + '</option>';
                        } else {
                            clickmailchimphtml += '<option value="' + element.value + '">' + element.label + '</option>';
                        }
                    });
                    jQuery("#spinandwin_mailchimp_list").html(clickmailchimphtml);
                    jQuery("#spinandwin_mailchimp_list").css('border', '');
                }
            }
        });
    }
}

function handleKlaviyoApiSelection() {
    jQuery('.spin_error').remove();
    var api_key = jQuery("#spinandwin_klaviyo_api").val().trim();
    var clickmailchimphtml = '';
    if (api_key != '') {
        jQuery.ajax({
            url: lists_ajax_url,
            type: 'post',
            data: 'ajax=true&method=klaviyo&api_key=' + api_key,
            dataType: 'json',
            showLoader: true,
            success: function (json) {
                if (json['error'] !== undefined) {
                    jQuery("#spinandwin_klaviyo_list").html('<option value="no_list">' + json['error'][0]['label'] + '</option>');
                    jQuery("#spinandwin_klaviyo_list").css('border', '1px solid #ff0000');
                } else {
                    jQuery.each(json['success'], function (index, element) {
                        if (email_marketing_values['klaviyo_list'] == element.value) {
                            clickmailchimphtml += '<option value="' + element.value + '" selected>' + element.label + '</option>';
                        } else {
                            clickmailchimphtml += '<option value="' + element.value + '">' + element.label + '</option>';
                        }
                    });
                    jQuery("#spinandwin_klaviyo_list").html(clickmailchimphtml);
                    jQuery("#spinandwin_klaviyo_list").css('border', '');
                }
            }
        });
    }
}

function handleConstantContactApiSelection() {
    jQuery('.spin_error').remove();
    var api_key = jQuery("#spinandwin_constant_contact_api").val().trim();
    var token = jQuery("#spinandwin_constant_contact_token").val().trim();
    var clickmailchimphtml = '';
    if (api_key != '' && token != '') {
        jQuery.ajax({
            url: lists_ajax_url,
            type: 'post',
            data: 'ajax=true&method=constant_contact&api_key=' + api_key + '&token=' + token,
            dataType: 'json',
            showLoader: true,
            success: function (json) {
                if (json['error'] !== undefined) {
                    jQuery("#spinandwin_constant_contact_list").html('<option value="no_list">' + json['error'][0]['label'] + '</option>');
                    jQuery("#spinandwin_constant_contact_list").css('border', '1px solid #ff0000');
                } else {
                    jQuery.each(json['success'], function (index, element) {
                        if (email_marketing_values['constant_contact_list'] == element.value) {
                            clickmailchimphtml += '<option value="' + element.value + '" selected>' + element.label + '</option>';
                        } else {
                            clickmailchimphtml += '<option value="' + element.value + '">' + element.label + '</option>';
                        }
                    });
                    jQuery("#spinandwin_constant_contact_list").html(clickmailchimphtml);
                    jQuery("#spinandwin_constant_contact_list").css('border', '');
                }
            }
        });
    }
}

jQuery(document).ready(function () {
    jQuery("#spinandwin_mailchimp_api").on('blur', function () {
        handleMailChimpApiSelection();
    });
    if (typeof email_marketing_values != 'undefined' && email_marketing_values != null) {
        if (typeof email_marketing_values['mailchimp_enable'] != 'undefined' && email_marketing_values['mailchimp_enable'] == 1) {
            handleMailChimpApiSelection();
        }

        jQuery("#spinandwin_klaviyo_api").on('blur', function () {
            handleKlaviyoApiSelection();
        });
        if (typeof email_marketing_values['klaviyo_enable'] != 'undefined' && email_marketing_values['klaviyo_enable'] == 1) {
            handleKlaviyoApiSelection();
        }

        jQuery("#spinandwin_constant_contact_api").on('blur', function () {
            handleConstantContactApiSelection();
        });
        jQuery("#spinandwin_constant_contact_token").on('blur', function () {
            handleConstantContactApiSelection();
        });
        if (typeof email_marketing_values['constant_contact_enable'] != 'undefined' && email_marketing_values['constant_contact_enable'] == 1) {
            handleConstantContactApiSelection();
        }
    }

    jQuery("li[data-ui-id='spinandwin-tabs-tab-item-spinandwin-statistics']").click(function () {
        jQuery('#generateLineChartButton').trigger('click');
        jQuery('#vss_spinandwin_graph_stats').html('<div class="loader-1"><img id="logo_image_preview" src="' + loader_image + '" style="margin: 0px 2px 3px 0px;width: 200px;border: 1px solid whitesmoke;" align="absmiddle"></div>');
    });
    /* Start - Code for Charts */
    if (typeof device_data != 'undefined' && !jQuery.isEmptyObject(device_data)) {
        var data_device = [];
        jQuery.each(device_data, function (index, element) {
            data_device.push({
                label: element.device,
                data: parseInt(element.device_count),
                color: getRandomColor()
            });
        });
        generatePieChart('#vss_spinandwin_device_stats', data_device);
    }
    if (typeof country_data != 'undefined' && !jQuery.isEmptyObject(country_data)) {
        var data_country = [];
        jQuery.each(country_data, function (index, element) {
            data_country.push({
                label: element.country,
                data: parseInt(element.country_count),
                color: getRandomColor()
            });
        });
        generatePieChart('#vss_spinandwin_country_stats', data_country);
    }

    /* End - Code for Charts */
    var previousPoint = null, previousLabel = null;
    jQuery.fn.CreateVerticalGraphToolTip = function () {
        jQuery(this).bind("plothover", function (event, pos, item) {
            if (item) {
                if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                    previousPoint = item.dataIndex;
                    previousLabel = item.series.label;
                    jQuery("#tooltip").remove();

                    var x = item.datapoint[0];
                    var y = item.datapoint[1];

                    var color = item.series.color;
                    OlGraph_showTooltip(item.pageX, item.pageY, color,
                            "<strong>" + item.series.xaxis.ticks[x].label + "</strong><br><strong>" + item.series.label + "</strong>" +
                            " : <strong>" + y + "</strong> ");
                }
            } else {
                jQuery("#tooltip").remove();
                previousPoint = null;
            }
        });
    };
});

/*
 * Function to generate the Line graph in the statistics tab
 * @returns {Boolean}
 */
function generateLineChart()
{
    var is_error = false;
    jQuery('.kb_error_message').remove();
    jQuery('#statistics input').removeClass('kb_error_field');
    var start_date_man_err = velovalidation.checkMandatory(jQuery('input[name="vss_spinandwin[statistics][start_date]"]'));
    if (start_date_man_err != true)
    {
        is_error = true;
        jQuery('input[name="vss_spinandwin[statistics][start_date]"]').addClass('kb_error_field');
        jQuery('input[name="vss_spinandwin[statistics][start_date]"]').after('<span class="kb_error_field">' + start_date_man_err + '</span>');
    } else
    {
        var start_date_format_err = velovalidation.checkDatemmddyy(jQuery('input[name="vss_spinandwin[statistics][start_date]"]'));
        if (start_date_format_err != true)
        {
            is_error = true;
            jQuery('input[name="vss_spinandwin[statistics][start_date]"]').addClass('kb_error_field');
            jQuery('input[name="vss_spinandwin[statistics][start_date]"]').after('<span class="kb_error_field">' + start_date_format_err + '</span>');
        }
    }

    var end_date_man_err = velovalidation.checkMandatory(jQuery('input[name="vss_spinandwin[statistics][end_date]"]'));
    if (end_date_man_err != true)
    {
        is_error = true;
        jQuery('input[name="vss_spinandwin[statistics][end_date]"]').addClass('kb_error_field');
        jQuery('input[name="vss_spinandwin[statistics][end_date]"]').after('<span class="kb_error_field">' + end_date_man_err + '</span>');
    } else
    {
        var end_date_format_err = velovalidation.checkDatemmddyy(jQuery('input[name="vss_spinandwin[statistics][end_date]"]'));
        if (end_date_format_err != true)
        {
            is_error = true;
            jQuery('input[name="vss_spinandwin[statistics][end_date]"]').addClass('kb_error_field');
            jQuery('input[name="vss_spinandwin[statistics][end_date]"]').after('<span class="kb_error_message">' + end_date_format_err + '</span>');
        }
    }

    var deactivation_date = Date.parse(jQuery('input[name="vss_spinandwin[statistics][end_date]"]').val());
    var activation_date = Date.parse(jQuery('input[name="vss_spinandwin[statistics][start_date]"]').val());
    if (parseInt(deactivation_date) < parseInt(activation_date)) {
        is_error = true;
        jQuery('input[name="vss_spinandwin[statistics][end_date]"]').addClass('kb_error_field');
//        jQuery('input[name="vss_spinandwin[statistics][end_date]"]').after('<span class="kb_error_message">' + stats_date_range_error + '</span>');
        jQuery('#generateLineChartButton').after('<span class="kb_error_message" style="margin-left: 0px;float: left;">' + stats_date_range_error + '</span>');
    }
    if (is_error) {
        return false;
    } else {
        jQuery('#vss_spinandwin_graph_stats').html('<div class="loader-1"><img id="logo_image_preview" src="' + loader_image + '" style="margin: 0px 2px 3px 0px;width: 200px;border: 1px solid whitesmoke;" align="absmiddle"></div>');
        jQuery.ajax({
            url: stats_ajax_url,
            type: 'post',
            data: 'start_date=' + jQuery('input[name="vss_spinandwin[statistics][start_date]"]').val() + '&end_date=' + jQuery('input[name="vss_spinandwin[statistics][end_date]"]').val(),
            dataType: 'json',
            success: function (retjson) {
                drawChart(retjson);
            }
        });
    }
}

function drawChart(json_data)
{
    var data1 = [];
    var data2 = [];
    var data3 = [];

    var dataObj1 = json_data['total_generated'];
    var dataObj2 = json_data['total_unused'];
    var dataObj3 = json_data['total_used'];
    var dataObj4 = json_data['ticks'];

    var tickss = [];
    for (var i in dataObj4)
    {
        tickss.push([i, dataObj4[i]]);
        data1.push([i, dataObj1[i]]);
        data2.push([i, dataObj3[i]]);
        data3.push([i, dataObj2[i]]);
    }

    var dataset = [
        {
            label: c_g_label,
            data: data1,
            points: {fillColor: "#0062FF", show: true},
            lines: {show: true, fillColor: '#DA4C4C'}
        },
        {
            label: c_u_label,
            data: data2,
            points: {fillColor: "#FF0000", show: true},
            lines: {show: true, fillColor: '#DA4C4C'}
        },
        {
            label: c_un_label,
            data: data3,
            points: {fillColor: "#b000df", show: true},
            lines: {show: true, fillColor: '#b000df'}
        }
    ];

    var options = {
        series: {
            lines: {
                show: true
            },
            points: {
                radius: 3,
                fill: true,
                show: true
            }
        },
        xaxis: {
            ticks: tickss,
            axisLabel: text_date,
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 10
        },
        yaxes: [{
                axisLabel: number_times,
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 3,
            }, {
                position: "right",
                axisLabel: "Change",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 3
            }
        ],
        legend: {
            noColumns: 0,
            labelBoxBorderColor: "#000000",
            position: "nw"
        },
        grid: {
            hoverable: true,
            borderWidth: 2,
            borderColor: "#633200",
            backgroundColor: {colors: ["#ffffff", "#EDF5FF"]}
        },
        colors: ["#FF0000", "#0022FF"]
    };
    jQuery.plot(jQuery('#vss_spinandwin_graph_stats'), dataset, options);
    jQuery('#vss_spinandwin_graph_stats').CreateVerticalGraphToolTip();
}


/*
 * Function for handling tooltip for the module's line chart
 * @param {type} x
 * @param {type} y
 * @param {type} color
 * @param {type} contents
 * @returns {undefined}
 */
function OlGraph_showTooltip(x, y, color, contents) {
    jQuery('<div id="tooltip">' + contents + '</div>').css({
        position: 'absolute',
        display: 'none',
        top: y - 40,
        left: x - 20,
        border: '1px solid ' + color,
        padding: '3px',
        'font-size': '11px',
        'border-radius': '5px',
        'background-color': '#fff',
        'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
        opacity: 0.9
    }).appendTo("body").fadeIn(200);
}

/*
 * Function for getting a Random Color code
 */
function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

/*
 * Function for changing the time to desired format
 */
function changetime(time) {
    var r = time.split('/');
    var t = r[2].split(' ');
    return t[0] + "-" + r[0] + "-" + r[1] + " " + t[1];
}

/*
 * Function for generating the Pie Charts in Statistics tab
 * @returns {undefined}
 */
function generatePieChart(selector, data)
{
    jQuery.plot(selector, data, {
        series: {
            pie: {
                show: true,
                label: {
                    show: true,
                    radius: 0.8,
                    formatter: function (label, series) {
                        return '<div style="border:1px solid grey;font-size:11px;text-align:center;padding:4px;color:white;background:black;opacity:0.5;">' +
                                label + ' : ' +
                                Math.round(series.percent) +
                                '% (' + series.data[0][1] + ')</div>';
                    }
                }
            }
        },
        legend: {
            show: false
        }
    });
}

function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function (m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function rgb2hsb(r, g, b)
{
    r /= 255;
    g /= 255;
    b /= 255; // Scale to unity.   
    var minVal = Math.min(r, g, b),
            maxVal = Math.max(r, g, b),
            delta = maxVal - minVal,
            HSB = {hue: 0, sat: 0, bri: maxVal},
            del_R, del_G, del_B;

    if (delta !== 0)
    {
        HSB.sat = delta / maxVal;
        del_R = (((maxVal - r) / 6) + (delta / 2)) / delta;
        del_G = (((maxVal - g) / 6) + (delta / 2)) / delta;
        del_B = (((maxVal - b) / 6) + (delta / 2)) / delta;

        if (r === maxVal) {
            HSB.hue = del_B - del_G;
        } else if (g === maxVal) {
            HSB.hue = (1 / 3) + del_R - del_B;
        } else if (b === maxVal) {
            HSB.hue = (2 / 3) + del_G - del_R;
        }

        if (HSB.hue < 0) {
            HSB.hue += 1;
        }
        if (HSB.hue > 1) {
            HSB.hue -= 1;
        }
    }

    HSB.hue *= 360;
    HSB.sat *= 100;
    HSB.bri *= 100;
    return HSB;
}
;

