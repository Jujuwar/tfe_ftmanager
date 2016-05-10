$('.addPlayer').on('click', function() {
    var modal = $('#addPlayer');

    var button = $(this);

    var team = button.data('team');
    var number = modal.find('#addPlayer_Number').val();
    var name = modal.find('#addPlayer_Name').val();
    var surname = modal.find('#addPlayer_Surname').val();
    // TODO : Datetime
    var birthday = modal.find('#addPlayer_Birthday').val();

    button.attr('disabled', 'disabled');

    button.find('.fa').removeClass('fa-pencil fa-plus').addClass('fa-spinner fa-pulse');

    if(button.data('edit')) {
        var id = button.data('id');

        $.ajax({
            type: 'POST',
            url: Routing.generate('admin_news_ajax_edit'),
            data: {
                id: id,
                title: title,
                message: message
            },
            error: function (request, error) { // Info Debuggage si erreur
                console.log("Erreur : responseText: " + request.responseText);
            },
            success: function (data) {
                if (data.status == 'ok') {
                    modal.modal('hide');

                    var line = $('tr[data-id="' + id + '"]');
                    line.effect("highlight", {color: '#c9c9c9'}, 5000);
                    line.children("td:nth-child(2)").html(title);

                    $('.modal_alert_success').modal('show');
                    setTimeout(function () {
                        $(".modal_alert_success").modal('hide');
                    }, 1700);
                } else {
                    $('.addNewError').html(data.debug);
                }
            }
        });
    } else {
        $.ajax({
            type: 'POST',
            url: Routing.generate('team_player_ajax_add'),
            data: {
                team: team,
                number: number,
                name: name,
                surname: surname,
                birthday: birthday
            },
            error: function (request, error) { // Info Debuggage si erreur
                console.log("Erreur : responseText: " + request.responseText);
            },
            success: function (data) {
                if (data.status == 'ok') {
                    modal.modal('hide');

                    if ($('.table_players_tbody').find('tr td').length == 1)
                        $('.table_players_tbody').find('tr td').remove();

                    $('.table_players_tbody').append(data.return);

                    $('.modal_alert_success').modal('show');
                    setTimeout(function () {
                        $(".modal_alert_success").modal('hide');
                    }, 1700);
                } else {
                    $('.addPlayerError').html(data.debug);
                }
            }
        });
    }
});

$('.modal-confirmation-yes').on('click', function() {
    var id = $(this).data('id');

    $.ajax({
        type: 'POST',
        url: Routing.generate('team_player_ajax_delete'),
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
                    if($('.table_players_tbody tr').length == 0) {
                        $('.table_players_tbody').append(
                            $('<tr>').append(
                                $('<td>').attr('colspan', '4').html('<i>Aucun joueur</i>')
                            )
                        );
                    }
                });
                $('.modal_alert_success').modal('show');
                setTimeout(function(){
                    $(".modal_alert_success").modal('hide');
                }, 1700);
            } else {
                $('.modal-body-more-info').html(data.debug);
                $('.modal_alert_error').modal('show');
            }
        }
    });
});

$('.table_players_tbody').on('click', 'button[data-action="delete"]', function() {
    var id = $(this).data('id');
    var modal = $('.modal_alert_confirmation');

    modal.find('.modal-confirmation-yes').data('id', id);
    modal.modal('show');
});