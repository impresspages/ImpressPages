
$(document).ready(function() {
    $('body').append(ipModuleDesignConfiguration);
    $('a').off('click').on('click', ipDesign.openLink);
    $('.ipModuleDesignConfig .ipsForm').on('submit', ipDesign.apply);
});


var ipDesign = new function() {

    this.openLink = function (e) {
        e.preventDefault();
        var href = $(e.currentTarget).attr('href');
        href = href + '?ipDesignPreview=1';
        window.location = href;
    }

    this.apply = function (e) {
        e.preventDefault();
        $form = $(this);

        $.ajax({
            url: ip.baseUrl,
            dataType: 'json',
            type : 'POST',
            data: $form.serialize(),
            success: function (response){
                if (response.status && response.status == 'success') {
                    if (typeof ipWidgetIpFormSuccess == 'function'){ //custom handler exists
                        ipWidgetIpFormSuccess($ipForm);
                    } else { //default handler
                        $ipForm.find('.ipwSuccess').show();
                        $ipForm.find('.ipwForm').hide();
                    }
                } else {
                    if (response.errors) {
                        form.data("validator").invalidate(response.errors);
                    }
                }
            }
        });
    }

};