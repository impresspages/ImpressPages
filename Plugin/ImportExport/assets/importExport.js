(function($){

    var ipExportImport = new function () {
        "use strict";

        var $this = $(this);


        this.init = function () {
    //        $('form').validator(validatorConfig);
            $('.ipsImportExportForm').on('ipSubmitResponse', processResponse);
            $('.ipsImportExportForm').on('submit', showProgressIndicator);

        };

        var showProgressIndicator = function () {
            $('.ipsLoading').removeClass('hidden');
            $('.ipsFileContainer').addClass('hidden');
            $('.ipsImportExportSubmit').addClass('hidden');
        }

        var processResponse = function (event, response) {
            if (response.status && response.status == 'success') {
                //form has been successfully submitted.


                $('.ipsLoading').addClass('hidden');

                var toClone = $('.ipsLogRecord').first();

                response.log.forEach(function (logRecord) {

                    var newClone = toClone.clone();

                    newClone.html(logRecord.message);
                    switch (logRecord.status) {
                        case 'danger':
                            newClone.addClass('alert-danger');
                            break;
                        case 'info':
                            newClone.addClass('alert-info');
                            break;
                        case 'success':
                            newClone.addClass('alert-success');
                            break;
                        case 'warning':
                            newClone.addClass('alert-warning');
                            break;
                        default:
                            newClone.addClass('alert-warning');
                    }

                    $('.ipsLog').append(newClone);


                });

                $('.ipsLogRecord').first().remove();

                $('.ipsLog').removeClass('hidden');
                $('.ipsLogRecord').removeClass('hidden');

            } else {
                //PHP controller says there are some errors
                if (response.errors) {
                    form.data("validator").invalidate(response.errors);
                }
            }
        };

        var showError = function (response) {
            alert(response);
        }


    };


    $(document).ready(function () {
        "use strict";
        ipExportImport.init();
    });

})(ip.jQuery);