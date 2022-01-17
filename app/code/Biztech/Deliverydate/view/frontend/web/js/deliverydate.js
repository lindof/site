define(['Magento_Ui/js/form/element/abstract', 'calendar', 'mageUtils', 'moment', 'Magento_Checkout/js/model/quote', 'ko', 'jquery','mousewheel',
    'jquery/ui',
    'jquery/jquery-ui-timepicker-addon','mage/translate'
], function (Component, calendar, utils, moment, quote, ko, $, mousewheel) {
    $(document).ready(function() {
            var setValueflag = true;
            $(document).on('change', '#date-picker', function(ele) {
                if(setValueflag == true) {
                    ele.target.value = '';
                    setValueflag = false;
                }
            });
    });
    return Component.extend({
        defaults: {
            template: window.deliverydateConfig.templateConfig.template,
            options: {
                buttonImage: window.deliverydateConfig.calendar.options.buttonImage,
                buttonImageOnly: true,
                buttonText: window.deliverydateConfig.calendar.options.buttonText,
                showsTime: window.deliverydateConfig.calendar.options.showsTime,
                closeText: $.mage.__('Done'),
                currentText: $.mage.__('Today'),
                showOn: "both",
                isRTL: window.deliverydateConfig.calendar.options.isRTL,
                showAnim: window.deliverydateConfig.calendar.options.showAnim,
                showButtonPanel: window.deliverydateConfig.calendar.options.showButtonPanel,
                dayNamesShort: [$.mage.__('Sun'), $.mage.__('Mon'), $.mage.__('Tue'), $.mage.__('Wed'), $.mage.__('Thu'), $.mage.__('Fri'), $.mage.__('Sat')],
                dayNamesMin: [$.mage.__('Sun'), $.mage.__('Mon'), $.mage.__('Tue'), $.mage.__('Wed'), $.mage.__('Thu'), $.mage.__('Fri'), $.mage.__('Sat')],
                onSelect: function () {
                    selectedDate = $(this).datepicker('getDate');
                    today = new Date()
                    this.visiblesamedayCharges = (selectedDate.getDate() == today.getDate() &&
                        selectedDate.getMonth() == today.getMonth() &&
                        selectedDate.getFullYear() == today.getFullYear());
                    if(this.visiblesamedayCharges && window.deliverydateConfig.general.sameDayCharges){
                        $("#delivery_charges_day").fadeIn(); 
                    }else{
                        $("#delivery_charges_day").fadeOut(); 
                    }
                    $('#shipping_arrival_date').val(jQuery.datepicker.formatDate('dd-mm-yy', selectedDate));
                },
                beforeShowDay: function (date) {
                    var currentdate = jQuery.datepicker.formatDate('dd-mm-yy', date);
                    var day = date.getDay();
                    var disableddates = window.deliverydateConfig.general.disabledDates;
                    var dayOffs = window.deliverydateConfig.general.dayOffs;
                    var dday = true;

                    if (dayOffs != '' && dayOffs != null && dayOffs != undefined) {
                        var d = dayOffs.split(',');
                        
                        $.each(d, function(index, val) {
                            if (day == val) {
                                dday = false;
                            }
                        });
                    }

                    return [window.deliverydateConfig.general.disabledDates.indexOf(currentdate) == -1 && dday];
                },
                onClose: function() {
                    selectedDate = $(this).datepicker('getDate');
                    window.localStorage.setItem('selecteddeliverydate', jQuery.datepicker.formatDate('dd-mm-yy', selectedDate));
                    jQuery('#shipping_arrival_date').val(jQuery.datepicker.formatDate('dd-mm-yy', selectedDate));
                },
                dateFormat: window.deliverydateConfig.general.dateFormat,
                timeFormat: window.deliverydateConfig.general.timeFormat,
                pickerTimeFormat : window.deliverydateConfig.general.timeFormat,
                hideIfNoPrevNext: true,
            },
            shiftedValue: '',
            timeOffset: 0,
            validationParams: {
                dateFormat: '${ $.outputDateFormat }'
            },
            outputDateFormat: 'MM/dd/y',
            inputDateFormat: 'y-MM-dd',
            pickerDateTimeFormat: '',
            pickerDefaultDateFormat: 'MM/dd/y',
            // ICU Date Format
            pickerDefaultTimeFormat: 'h:mm a',
            // ICU Time Format
            elementTmpl: 'ui/form/element/date',
            listens: {
                'value': 'onValueChange',
                'shiftedValue': 'onShiftedValueChange'
            },
        },
        getShowHtml: function () {
            return window.deliverydateConfig.templateConfig.showHtml;
        },
        getMinDate: function () {

            if(this.options.showsTime && window.deliverydateConfig.calendar.options.interval !== 0){
                var now = new Date( (new Date()).getTime() + 3600 * window.deliverydateConfig.calendar.options.interval * 1000 )
            }else{
                var now = new Date();
            }
            var dayDiff = window.deliverydateConfig.general.dayDiff;

            if (dayDiff > 0) {
                now.setDate(now.getDate() + dayDiff);
            }

            var currentdate = jQuery.datepicker.formatDate('dd-mm-yy', now);
            var dayoffset = 0;

            $.each(window.deliverydateConfig.general.disabledDates, function(index, val) {
                if (val == currentdate) {
                    now.setDate(now.getDate() + 1);
                    currentdate = jQuery.datepicker.formatDate('dd-mm-yy', now);
                }
            });

            return now;
        },
        isSelected: ko.computed(function () {
            return true;
        }
        ),
        clearHidden: function () {
            jQuery('#shipping_arrival_date').val('');
            jQuery('#shipping_arrival_slot').val('');
            jQuery('#delivery_charges').val('');
            return true;
        },
        initialize: function () {
            this._super();
            this.deliverydateEnabled = window.deliverydateConfig.general.enabled;
            

            this.isRtl = (window.deliverydateConfig.calendar.options.isRTL) ? "datepicker-rtl" : "";
            this.isMandatory = '';
            var timeSlot = window.deliverydateConfig.timeslot.enabled_timeslots;

            this.samedayCharges = false;
            this.visiblesamedayCharges = ko.observable(false);
            this.showcharges = "";
            if(window.deliverydateConfig.general.sameDayCharges > 0 ){
                this.samedayCharges = window.deliverydateConfig.general.sameDayCharges;
                this.showcharges = $.mage.__('Same day delivery charges are ' + window.deliverydateConfig.general.sameDayChargesWithCurruncy);
            }
            if (window.deliverydateConfig.general.isMandatory == 1) {
                this.isMandatory = 'required';
            }
            this.calenderView = false; 
            this.deliverydateLabel = window.deliverydateConfig.templateConfig.deliverydateLabel;
            this.deliverydatecommentLabel = window.deliverydateConfig.templateConfig.deliverydateComments;
            this.displayHtml = window.deliverydateConfig.templateConfig.displayHtml;

            var finalTimeSlotArray = $.map(timeSlot, function (el) {
                return el
            });

            this.timeSlots = ko.observableArray(finalTimeSlotArray);
            this.timeslotTableLabel = window.deliverydateConfig.timeslot.timeslotTableLabel;
            if(window.deliverydateConfig.templateConfig.enabledMethod=="1") {
                this.calenderView = true; 
            }
            this.deliverydatecallMeLabel = "";
            this.callfordelivery = ko.observable(false),
            this.useCallMe = false;
            if(window.deliverydateConfig.general.useCallFeature === 1){
                this.deliverydatecallMeLabel = window.deliverydateConfig.general.callMeLabel;
                this.useCallMe = true;
            }

            this.checkslot = ko.observable(true);
        },
        initConfig: function () {
            this._super();

            if (!this.options.dateFormat) {
                this.options.dateFormat = this.pickerDefaultDateFormat;
            }

            if (!this.options.timeFormat) {
                this.options.timeFormat = this.pickerDefaultTimeFormat;
            }

            if (!this.options.minDate) {
                this.options.minDate = this.getMinDate();
            }

            if (!this.options.buttonText) {
                this.options.buttonText = 'Select Date';
            }

            if(window.deliverydateConfig.calendar.options.maxDate !== 0 && window.deliverydateConfig.calendar.options.maxDate !== "" && window.deliverydateConfig.calendar.options.maxDate != undefined){
                this.options.maxDate = "+" + window.deliverydateConfig.calendar.options.maxDate - 1 + "d";
            }

            /*if (!this.options.defaultDate) {
                this.options.defaultDate = this.getDefaultDate()
            }*/

            this.prepareDateTimeFormats();
            return this;
        },
        initObservable: function () {
            return this._super().observe(['shiftedValue']);
        },
        onValueChange: function (value) {
            var dateFormat,
                shiftedValue;

            if (value) {
                if (this.options.showsTime) {
                    shiftedValue = moment.utc(value).add(this.timeOffset, 'seconds');
                } else {
                    dateFormat = this.shiftedValue() ? this.outputDateFormat : this.inputDateFormat;

                    shiftedValue = moment(value, dateFormat);
                }

                shiftedValue = shiftedValue.format(this.pickerDateTimeFormat);
            } else {
                shiftedValue = '';
            }

            if (shiftedValue !== this.shiftedValue()) {
                this.shiftedValue(shiftedValue);
            }
        },

        /**
         * Prepares and sets date/time value that will be sent
         * to the server.
         *
         * @param {String} shiftedValue
         */
        onShiftedValueChange: function (shiftedValue) {
            var value;
            if (shiftedValue) {
                if (this.options.showsTime) {
                    value = moment.utc(shiftedValue, this.pickerDateTimeFormat);
                    value = value.subtract(this.timeOffset, 'seconds').toISOString();
                } else {
                    value = moment(shiftedValue, this.pickerDateTimeFormat);
                    value = value.format(this.outputDateFormat);
                }
            } else {
                value = '';
            }

            if (value !== this.value()) {
                this.value(value);
            }
        },
        prepareDateTimeFormats: function () {
            this.pickerDateTimeFormat = this.options.dateFormat;
            if (this.options.showsTime) {
                this.pickerDateTimeFormat += ' ' + this.options.timeFormat;
            }
            this.pickerDateTimeFormat = utils.normalizeDate(this.pickerDateTimeFormat);
            if (this.dateFormat) {
                this.inputDateFormat = this.dateFormat;
            }
            this.inputDateFormat = utils.normalizeDate(this.inputDateFormat);
            this.outputDateFormat = utils.normalizeDate(this.outputDateFormat);
            this.validationParams.dateFormat = this.outputDateFormat;
        },
        loadScrollBooster: function(){
            jQuery('.table-checkout-delivery-method').mousewheel(function(e, delta) {
                this.scrollLeft -= (delta * 40);
                e.preventDefault();
            });
        }
    });
});