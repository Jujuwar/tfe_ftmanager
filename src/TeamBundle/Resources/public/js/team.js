$('.teamNotRegistred .btn-danger').on('click', function() {
    var button = $(this);

    button.addClass('disabled').prepend($('<i>').addClass('fa fa-spinner fa-spin'));

    var team = button.data('id');

    $.ajax({
        type: 'POST',
        url: Routing.generate('team_ajax_setregistered'),
        data: {
            id: team
        },
        error: function (request, error) { // Info Debuggage si erreur
            console.log("Erreur : responseText: " + request.responseText);
        },
        success: function (data) {
            if (data.status == 'ok') {
                $('.teamNotRegistred').fadeOut(1500, "easeOutExpo", function() {
                    $(this).remove();
                });

                $('.modal_alert_success').modal('show');
                setTimeout(function () {
                    $(".modal_alert_success").modal('hide');
                }, 1700);
            } else {
                $('.modal-body-more-info').html(data.message);
                $('.modal_alert_error').modal('show');
                console.log(data.debug);

                button.removeClass('disabled').remove($('<i>'));
            }
        }
    });
});