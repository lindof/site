require([
    'jquery',
    'mage/storage',
    'Biztech_Deliverydate/js/fullLoader'
], function ($, storage, fullLoader) {

    $(document).on('click', '.cal-nav', function (event) {
        event.preventDefault();

        fullLoader.startLoader();

        var url = $(this).data('url');

        return storage.get(url).done(function (transport) {
            $('#result').html(transport);
            fullLoader.stopLoader(true);
        }).fail(function () {
            $('#result').html('transport');
            fullLoader.stopLoader(true);
        });
    });

});