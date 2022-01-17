var viewPortWidthVelsof = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
var viewPortHeightVelsof = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

function changeColor(wheel_color)
{
    velsofWheelHexCode = wheel_color;
    var colorRGB = hexToRgb(velsofWheelHexCode);
    var hslColorCode = rgb2hsb(colorRGB.r, colorRGB.g, colorRGB.b);
    document.getElementById("velsof_spinner").style.filter = 'hue-rotate(' + hslColorCode.hue + 'deg) saturate(' + hslColorCode.sat + '%) contrast(1.1)';

}

jQuery(document).ready(function(){
    if (jQuery('#velsof_wheel_main_container').length) {
        
        if (wheel_design == '2') {
            changeColor(wheel_color);
        } else {
            velsofWheelHexCode = "#4497bb";
            var colorRGB = hexToRgb(velsofWheelHexCode);
            var hslColorCode = rgb2hsb(colorRGB.r, colorRGB.g, colorRGB.b);
            jQuery("#velsof_spinner").css('filter', '');
        }
        
        if (jQuery(window).width() > 499) {
            document.getElementById("velsof_wheel_main_container").style.height = document.documentElement.clientHeight + 'px';
            document.getElementById("velsof_wheel_model").style.height = document.documentElement.clientHeight + 'px';
        }
        //document.getElementById("velsof_spinners").style.top = parseInt((viewPortHeightVelsof - 500) / 2) + "px";
        if (velsofWheelHexCode != "") {
            var colorRGB = hexToRgb(velsofWheelHexCode);
            var hslColorCode = rgb2hsb(colorRGB.r, colorRGB.g, colorRGB.b);
            //document.getElementById("velsof_spinner").style.filter = 'hue-rotate(' + hslColorCode.hue + 'deg) saturate(' + hslColorCode.sat + '%) contrast(1.1)';
        }
    }
    codeOnWindowLoad();

jQuery('.spin_toggle').on('click', function () {
        jQuery('#pull_out').hide();
        jQuery('#velsof_wheel_container').show();

        setTimeout(
                function () {
                    jQuery('#velsof_wheel_main_container').addClass('transform');
                }, 500);
    });
});

window.onresize = function () {
    if (jQuery('#velsof_wheel_main_container').length) {
        document.getElementById("velsof_wheel_main_container").style.height = document.documentElement.clientHeight + 'px';
        document.getElementById("velsof_wheel_model").style.height = document.documentElement.clientHeight + 'px';

        if (window.innerHeight > 500) {
            //    document.getElementById("velsof_wheel_model").style.height = window.innerHeight + 'px';
        } else {
            //    document.getElementById("velsof_wheel_model").style.height = '500px';
        }
    }
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

function codeOnWindowLoad()
{
    if (jQuery('#velsof_wheel_main_container').length) {
        jQuery('#pull_out_tab').click(function () {
            jQuery('#velsof_wheel_container').show();
            jQuery('#pull_out_tab').hide();
            setTimeout(function () {
                jQuery('#velsof_wheel_main_container').addClass('transform');
            }, 500);
        });

        jQuery('#spinwheel_exit').click(function () {
            jQuery('#velsof_wheel_main_container').removeClass('transform');
            setTimeout(function () {
                jQuery('#velsof_wheel_container').hide();
                if (show_pull_out == 1) {
                    jQuery('#pull_out').show();
                }
            }, 500);
            var days = 30;
            if (typeof display_interval != 'undefined') {
                var days = parseInt(display_interval);
            }
            var d1 = new Date();
            d1.setTime(d1.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d1.toUTCString();
            document.cookie = "knowband_spinwheel_nolucky=1;" + expires + ";path=/";
        });
        var parsed_settings = jQuery.parseJSON(display_settings);

        applyModuleConfigurations();
        jQuery('#vss_next_time_button').click(function () {
            jQuery('#velsof_wheel_main_container').removeClass('transform');
            setTimeout(function () {
                jQuery('#velsof_wheel_container').hide();
            }, 500);
        });


        jQuery('#spinwheel_continue_btn').click(function () {
            if (typeof coupon_display_options != 'undefined' && coupon_display_options != 2) {
                jQuery('.tooltiptext').html(coupon_copied);
                jQuery('.tooltiptext').show();
                setTimeout(function () {
                    jQuery('.tooltiptext').hide();
                    jQuery('#velsof_wheel_main_container').removeClass('transform');
                    setTimeout(function () {
                        jQuery('#velsof_wheel_container').hide();
                    }, 500);
                }, 1000);
            } else {
                jQuery('#velsof_wheel_main_container').removeClass('transform');
                setTimeout(function () {
                    jQuery('#velsof_wheel_container').hide();
                }, 500);
            }
        });
        
        /* Position of function call changed for fixing issue by Paras (On the display setting tab-->In the minimum screen size-->When user select Desktop (1600*1080) then spin and wheel should not be shown on Front end) */
        applyAllModuleConfigurations(parsed_settings);
        
        if (typeof coupon_display_options != 'undefined' && coupon_display_options != 2) {
            document.getElementById("spinwheel_continue_btn").addEventListener("click", function() {
                copyToClipboard(document.getElementById("vss_combined_input_field"));
            });
        }
    }
}

/*
 * Function to apply all module configuration to the module's wheel
 * 
 * @param {type} parsed_settings
 * 
 */
function applyAllModuleConfigurations(parsed_settings)
{
    /* Code Modified for fixing issue by Paras (On the display setting tab-->In the hide spin wheel after-->when user select the time 10 sec or 20 sec then on the frontend side-->Popup is being hidden after 20 or 30 sec respectively. I have calculated the time using Stopwatch) */
    if (typeof parsed_settings.hide_after != 'undefined') {
        if (parsed_settings.hide_after != '1') {
            setTimeout(function () {
                hideSpinWheelAndContainer();
            }, (parseInt(parsed_settings.hide_after) - 1)  * 10 * 1000);
        }
    }

    if (typeof parsed_settings.screen_size != 'undefined') {
        var screen = parsed_settings.screen_size.split('_');
        var width = screen[0];
        var height = screen[1];
        if (window.screen.width < width) {
            hideSpinWheelAndContainer(false);
        }
    }
}

/*
 * Function to hide spin wheel with container in the front end
 * 
 */
function hideSpinWheelAndContainer(with_timeout)
{
    var with_timeout = typeof with_timeout !== 'undefined' ? with_timeout : true;
    jQuery('#velsof_wheel_main_container').removeClass('transform');
    if (with_timeout) {
        setTimeout(function () {
            jQuery("#velsof_wheel_container").hide();
            jQuery('#pull_out_tab').show();
            jQuery('.spin_toggle').hide();
        }, 500);
    } else {
        jQuery("#velsof_wheel_container").hide();
        jQuery('#pull_out_tab').show();
        jQuery('.spin_toggle').hide();
    }
}

/*
 * 
 * @param {type} on_exit
 * @returns {undefined}
 */

function del_cookie(name) {
    var expires = "expires=Thu, 01-Jan-70 00:00:01 GMT";
    document.cookie = "knowband_spinwheel_nolucky=1;" + expires + ";path=/";
} 

/*
 * Function to show spin wheel with container in the front end
 * 
 */
function showSpinWheelAndContainer(on_exit)
{ 
    var on_exit = typeof on_exit !== 'undefined' ? on_exit : true;
    var return_visit_flag = true;
    var new_visit_flag = true;
    var parsed_settings = jQuery.parseJSON(display_settings);
    if (typeof parsed_settings.whom_to_show != 'undefined') {
        if (parsed_settings.whom_to_show == '2') {
            if (getCookie('knowband_spinwheel_visitor') == '1') {
                new_visit_flag = false;
            } else {
                var d2 = new Date();
                d2.setTime(d2.getTime() + (30 * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d2.toUTCString();
                document.cookie = "knowband_spinwheel_visitor=1;" + expires + ";path=/";
                new_visit_flag = true;
            }
        } else if (parsed_settings.whom_to_show == '3') {
            if (getCookie('knowband_spinwheel_visitor') == '1') {
                return_visit_flag = true;
                //document.cookie = "knowband_spinwheel_visitor=1;" + expires + ";path=/";
            } else {
                var d3 = new Date();
                d3.setTime(d3.getTime() + (30 * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d3.toUTCString();
                document.cookie = "knowband_spinwheel_visitor=1;" + expires + ";path=/";
                return_visit_flag = false;
            }
        }
    }
    if (return_visit_flag && new_visit_flag) {
        if (getCookie('knowband_spinwheel_nolucky') != '1') {
            if (getCookie('knowband_spinwheel_frequency') != '1') {
                    if (on_exit) {
                        jQuery('#velsof_wheel_container').show();
                    }
                    jQuery('#pull_out_tab').hide();
                    setTimeout(function () {
                        jQuery('#velsof_wheel_main_container').addClass('transform');
                    }, 500);
                    if (typeof parsed_settings.display_frequency != 'undefined') {
                        if (parsed_settings.display_frequency != '1') {
                            if (getCookie('knowband_spinwheel_frequency') == '1') {
                                /* Do Nothing */
                            } else {
                                var d = new Date();
                                if (parsed_settings.display_frequency == '2') {  //hour
                                    d.setTime(d.getTime() + (1 * 60 * 60 * 1000));
                                } else if (parsed_settings.display_frequency == '3') {  //day
                                    d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
                                } else if (parsed_settings.display_frequency == '4') {  //week
                                    d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));
                                } else if (parsed_settings.display_frequency == '5') {  //month
                                    d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
                                }
                                var expires = "expires=" + d.toUTCString();
                                    document.cookie = "knowband_spinwheel_frequency=1;" + expires + ";path=/";
                            }
                        }
                    }
                    if (typeof display_interval != 'undefined') {
                        if (getCookie('knowband_spinwheel_interval') == '1') {
                            /* Do Nothing */
                        } else {
                            var d1 = new Date();
                            var days = parseInt(display_interval);
                            d1.setTime(d1.getTime() + (days * 24 * 60 * 60 * 1000));
                            var expires = "expires=" + d1.toUTCString();
                            document.cookie = "knowband_spinwheel_interval="+display_interval+";" + expires + ";path=/";
                        }
                    }
            }
        } else {
            if (getCookie('knowband_spinwheel_interval') != '1') {
                if (typeof display_interval != 'undefined') {
                    if (getCookie('knowband_spinwheel_interval') == '1') {
                        /* Do Nothing */
                    } else {
                        var d1 = new Date();
                        var days = parseInt(display_interval);
                        d1.setTime(d1.getTime() + (days * 24 * 60 * 60 * 1000));
                        var expires = "expires=" + d1.toUTCString();
                        document.cookie = "knowband_spinwheel_interval="+display_interval+";" + expires + ";path=/";
                    }
                }
                jQuery('#pull_out').show();
            }
        }
    }
}

function playWheelSound(){
    if (wheel_sound == '1') {
        setTimeout(function () {
            var audio = new Audio(wheel_sound_file_path);
            audio.play();
        }, 100);
    }  
}


/*
 * Function to handle Ajax requests for generating Voucher and sending the email
 */
function rotateWheel(event)
{
    
    event.stopImmediatePropagation();
    var firstname = '';
    var lastname = '';
    var email = jQuery('#vss_combined_input_field').val().trim();
    
    if(require_name == 'first'){
         firstname = jQuery('#vss_firstname_input_field').val();
    }
    
    if(require_name == 'last'){
         lastname = jQuery('#vss_lastname_input_field').val();
    }
    
    if(require_name == 'both'){
         firstname = jQuery('#vss_firstname_input_field').val();
         lastname = jQuery('#vss_lastname_input_field').val();
    } 
    
    var error = false;
    var errmsg = '';
    if (email.trim() == "") {
        error = true;
        errmsg = email_blank_error;
    } else if (!isEmail(email.trim())) {
        error = true;
        errmsg = email_error;
    }
    
    if(require_name == 'first'){
        if (firstname == "") {
            error = true;
            errmsg = fname_blank_error;
        }
    }
    if(require_name == 'last'){
        if (lastname == "") {
            error = true;
            errmsg = lname_blank_error;
        }
    }
    
    if(require_name == 'both'){
        if (lastname == "") {
            error = true;
            errmsg = lname_blank_error;
        }
        
        if (firstname == "") {
            error = true;
            errmsg = fname_blank_error;
        }
    }
    

    

    if (!error) {
        var cus_email = jQuery('#vss_combined_input_field').val();
        var cus_firstname = jQuery('#vss_firstname_input_field').val();
        var cus_lastname = jQuery('#vss_lastname_input_field').val();
        jQuery.ajax({
            type: "POST",
            url: urlFrontAjax,
            dataType: "JSON",
            data: {ajax: true, customer_email: cus_email, customer_fname: cus_firstname, customer_lname: cus_lastname, method: 'checkWinningAndGenerateCoupon'},
            beforeSend: function () {
                jQuery('#velsof_offer_main_container').fadeOut('slow');
                jQuery('#vss_loader').fadeIn('slow');
            },
            success: function (json) {
                
                del_cookie('knowband_spinwheel_nolucky');
                if (typeof json['email_recheck_failed'] != 'undefined') {
                    jQuery('#velsof_offer_main_container').fadeIn('fast');
                    jQuery('#vss_loader').fadeOut('fast');
                    jQuery('.tooltiptext').html(json['email_recheck_failed']);
                    jQuery('.tooltiptext').show();
                    setTimeout(function () {
                        jQuery('.tooltiptext').hide();
                    }, 5000);
                } else if (typeof json['win'] != 'undefined' && json['win'] == true) {
                    document.getElementById('velsof_spinner').style.animationName = 'spinwheel_' + json['slice_to_select'];
                    playWheelSound();
                    setTimeout(function () {
                        jQuery('#vss_loader').fadeOut('slow');
                        jQuery('#velsof_main_header').hide();
                        jQuery('#spinwheeltitle').hide();
                        jQuery('.velsof_ul').hide();
                        jQuery('#vss_try_your_luck_button').hide();
                        jQuery('#spinwheel_exit').hide();
                        jQuery('#vss_firstname_input_field').hide();
                        jQuery('#vss_lastname_input_field').hide();
                        jQuery('.progressBar').hide();
                        if (json['coupon_code']) {
                            if(show_fireworks == '1'){
                                jQuery('#velsof_wheel_main_container').fireworks();
                            }
                            var replaced = jQuery("#spin_successmessage").html().replace('{discount}', json['label']);
                        } else {
                            var replaced = jQuery("#spin_successmessage").html().replace('{discount}', coupon_discount_ph);
                        }
                        jQuery('#spin_successmessage').html(replaced);
                        jQuery('#spin_successmessage').show();
                        jQuery('#sucmsgdes').show();
                        jQuery('#spinwheel_continue_btn').show();
                        if (typeof coupon_display_options != 'undefined' && coupon_display_options != '2') {
                            jQuery('#vss_combined_input_field').val(json['coupon_code']);
                            jQuery('#vss_combined_input_field').attr('readonly', true);
                        } else {
                            jQuery('#vss_combined_input_field').hide();
                            jQuery('#spin_sent_on_email').show();
                        }
                        jQuery('#velsof_offer_main_container').fadeIn('fast');
                    }, 6000);
                    jQuery.ajax({
                        type: "POST",
                        url: urlFrontAjax,
                        dataType: "JSON",
                        data: {ajax: true, method: 'sendEmailWithCouponCode', coupon_code: json['coupon_code'], customer_email: cus_email},
                        success: function (json) {
                            if (typeof json['error'] != 'undefined' && json['error'] == true) {
                                alert(json['error']);
                            }
                        }
                    });
//                    var d1 = new Date();
//                    d1.setTime(d1.getTime() + (30 * 24 * 60 * 60 * 1000));
//                    var expires = "expires=" + d1.toUTCString();
//                    document.cookie = "knowband_spinwheel_nolucky=1;" + expires + ";path=/";
                } else {
                    document.getElementById('velsof_spinner').style.animationName = 'spinwheel_' + json['slice_to_select'];
                    playWheelSound();
                    setTimeout(function () {
                        jQuery('#vss_loader').fadeOut('slow');
                        if (json['error']) {
                            jQuery('#spin_falemsg').html(json['error']);
                        }
                        jQuery('#velsof_main_header').hide();
                        jQuery('#spinwheeltitle').hide();
                        jQuery('.velsof_ul').hide();
                        jQuery('#vss_try_your_luck_button').hide();
                        jQuery('#spinwheel_exit').hide();
                        jQuery('#vss_combined_input_field').hide();
                        jQuery('#vss_firstname_input_field').hide();
                        jQuery('#vss_lastname_input_field').hide();
                        jQuery('.progressBar').hide();
                        jQuery('#spin_falemsg').show();
                        jQuery('#vss_next_time_button').show();
                        jQuery('#velsof_offer_main_container').fadeIn('fast');
                    }, 6000);
                    
                }
            }
        });
    } else {
        jQuery('.tooltiptext').html(errmsg);
        jQuery('.tooltiptext').show();
        setTimeout(function () {
            jQuery('.tooltiptext').hide();
        }, 2000);
    }
}

/*
 * Apply module configurations to the front end Spin Wheel of the module 
 */
function applyModuleConfigurations()
{
    if (time_display !== '') {
        var time = Number(time_display) * 1000;
        setTimeout(function () {
            showSpinWheelAndContainer();
        }, time);
    } else if (scroll_display !== '') {
        window.displayed_through_scroll = false;
        jQuery(document).on("scroll", function () {
            if (!window.displayed_through_scroll) {
                var s = jQuery(window).scrollTop(),
                        d = jQuery(document).height(),
                        c = jQuery(window).height();
                var scrollPercent = (s / (d - c)) * 100;
                if (scrollPercent >= scroll_display) {
                    showSpinWheelAndContainer();
                    window.displayed_through_scroll = true;
                }
            }
        });
    } else if (when_exit == true) {
        if (IsPopupVisibleForExitEvent()) {
            setTimeout(function () {
                var popup = ouibounce(document.getElementById("velsof_wheel_container"), {
                    aggressive: true,
                    timer: 0,
                    callback: function () {
                        showSpinWheelAndContainer(false);
                    }
                });
            }, 500);
        }
    } else {
        showSpinWheelAndContainer();
    }
}

function IsPopupVisibleForExitEvent() {
        if (getCookie('knowband_spinwheel_nolucky') != '1') {
            if (getCookie('knowband_spinwheel_frequency') != '1') {
                return true;    
            }
        } else {
            if (getCookie('knowband_spinwheel_interval') != '1') {
                return true;
            }
        }
    return false;
}

/*
 * Hide image if not exist
 */
function hideImageBlock(ele)
{
    jQuery(ele).parent().hide();
}

/*
 * Function to get the value of a Cookie
 */
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

/*
 * Function to validate email address
 */
function isEmail(email) {
    var regex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
    if (!regex.test(email)) {
        return regex.test(email);
    } else {
        var splChars = "+-,/*";
        var splChars_exist = false;
        for (var i = 0; i < email.length; i++) {
            if (splChars.indexOf(email.charAt(i)) != -1) {
                splChars_exist = true;
                break;
            }
        }
        if (splChars_exist) {
            return false;
        }
    }
    return true;
}

/*
 * Function for copying the generated coupon code to clipboard
 */
function copyToClipboard(elem) {
          // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;
    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
    }
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);

    // copy the selection
    var succeed;
    try {
          succeed = document.execCommand("copy");
    } catch(e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }

    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    return succeed;
}

