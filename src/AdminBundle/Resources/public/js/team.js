$('.table_teams_tbody').on('click', 'button[data-action="delete"]', function() {
    var id = $(this).data('id');
    var modal = $('.modal_alert_confirmation');

    modal.find('.modal-body-more-info').text('La suppression de l\'équipe n\'est possible que si celle-ci n\'a participé à aucun tournoi et ne possède aucun joueur');
    modal.find('.modal-confirmation-yes').data('id', id);
    modal.modal('show');
});

$('.modal-confirmation-yes').on('click', function() {
    var id = $(this).data('id');

    $.ajax({
        type: 'POST',
        url: Routing.generate('admin_teams_ajax_delete'),
        data: {
            id: id
        },
        error: function (request, error) { // Info Debuggage si erreur
            console.log("Erreur : responseText: " + request.responseText);
        },
        success: function (data) {
            if(data.status == 'ok') {
                $('tr[data-id="' + id + '"]').fadeOut(1500, "easeOutExpo", function() {
                    $(this).remove();
                    if($('.table_teams_tbody tr').length == 0) {
                        $('.table_teams_tbody').append(
                            $('<tr>').append(
                                $('<td>').attr('colspan', '6').html('<i>Aucune team inscrite</i>')
                            )
                        );
                    }
                });
                $('.modal_alert_success').modal('show');
                setTimeout(function(){
                    $(".modal_alert_success").modal('hide');
                }, 1700);
            } else {
                $('.modal-body-more-info').html(data.message);
                $('.modal_alert_error').modal('show');

                console.log(data.debug);
            }
        }
    });
});

$('.table_teams_tbody').on('click', 'button[data-action="validate"]', function() {
    var id = $(this).data('id');

    console.log('test');

    $.ajax({
        type: 'POST',
        url: Routing.generate('admin_teams_ajax_validate'),
        data: {
            id: id
        },
        error: function (request, error) { // Info Debuggage si erreur
            console.log("Erreur : responseText: " + request.responseText);
        },
        success: function (data) {
            if(data.status == 'ok') {
                var line = $('tr[data-id="' + id + '"]');
                line.replaceWith(data.return);
                line.effect("highlight", {color: '#c9c9c9'}, 5000);

                $('.modal_alert_success').modal('show');
                setTimeout(function(){
                    $(".modal_alert_success").modal('hide');
                }, 1700);
            } else {
                $('.modal-body-more-info').html(data.message);
                $('.modal_alert_error').modal('show');

                console.log(data.debug);
            }
        }
    });
});