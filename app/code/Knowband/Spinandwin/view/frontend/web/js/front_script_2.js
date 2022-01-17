function showSpinWheelAndContainer(on_exit)
{
    var on_exit = typeof on_exit !== 'undefined' ? on_exit : true;
    var return_visit_flag = true;
    var new_visit_flag = true;
    var parsed_settings = jQuery.parseJSON(display_settings);
    if (typeof parsed_settings.who_to_show != 'undefined') {
        if (parsed_settings.who_to_show == 'new') {
            if (getCookie('knowband_spinwheel_visitor') == '1') {
                new_visit_flag = false;
            } else {
                var d2 = new Date();
                d2.setTime(d2.getTime() + (30 * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d2.toUTCString();
                document.cookie = "knowband_spinwheel_visitor=1;" + expires + ";path=/";
                new_visit_flag = true;
            }
        } else if (parsed_settings.who_to_show == 'returning') {
            if (getCookie('knowband_spinwheel_visitor') == '1') {
                return_visit_flag = true;
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
                if (getCookie('knowband_spinwheel_interval') != '1') {
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
                            document.cookie = "knowband_spinwheel_interval=1;" + expires + ";path=/";
                        }
                    }
                }
            }
        } else {
            jQuery('#pull_out_tab').show();
        }
    }
}