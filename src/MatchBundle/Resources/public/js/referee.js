$('#btn_editPrestations').on('click', function() {
   var id = $(this).data('id');

    $.ajax({
        type: 'POST',
        url: Routing.generate('match_ajax_get'),
        data: {
            id: id
        },
        error: function (request, error) { // Info Debuggage si erreur
            console.log("Erreur : responseText: " + request.responseText);
        },
        success: function (data) {
            if (data.status == 'ok') {
                var div_team1 = $('#modal_div_team1');
                var div_team2 = $('#modal_div_team2');

                div_team1.find('.team_title').html(data.value.team1.name);
                div_team2.find('.team_title').html(data.value.team2.name);


                $.each(data.value.prestations, function(i, item) {
                    console.log(item);
                    if(item.player.team.id == data.value.team1.id) {
                        div_team1.find('.team_players').append(
                            $('<tr>').append(
                                $('<td>').addClass('col-xs-4').html(item.player.name + ' ' + item.player.surname)
                            ).append(
                                $('<td>').addClass('col-xs-2').append(
                                    $('<input>').attr('type', 'number').addClass('form-control')
                                )
                            ).append(
                                $('<td>').addClass('col-xs-2').append(
                                    $('<input>').attr('type', 'number').addClass('form-control')
                                )
                            ).append(
                                $('<td>').addClass('col-xs-1').append(
                                    $('<input>').attr('type', 'number').addClass('form-control')
                                )
                            ).append(
                                $('<td>').addClass('col-xs-1').append(
                                    $('<input>').attr('type', 'number').addClass('form-control')
                                )
                            ).append(
                                $('<td>').addClass('col-xs-2').append(
                                    $('<input>').attr('type', 'number').addClass('form-control')
                                )
                            )
                        );
                    } else  {
                        console.log("test2");
                    }
                });

                $('#editPrestations').modal('show');
            } else {
                $('.modal-body-more-info').html(data.message);
                $('.modal_alert_error').modal('show');

                console.log(data.debug);
            }
        }
    });
});

$('#editPrestations').on('hide.bs.modal', function() {
    var div_team1 = $('#modal_div_team1');
    var div_team2 = $('#modal_div_team2');

    div_team1.find('.team_players').empty();
    div_team2.find('.team_players').empty();
});