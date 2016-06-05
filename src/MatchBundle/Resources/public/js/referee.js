$('.table_matchs_tbody').on('click', '.btn_editPrestations', function() {
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


                $.each(data.value.team1.players, function(i, item) {
                    var result = $.grep(data.value.prestations, function(e){ return e.player.id == item.id; });
                    if(result.length == 1)
                        insertLine(div_team1, id, item.id, item.name, item.surname, result[0].enterTime, result[0].leaveTime, result[0].yellowCards, result[0].redCards, result[0].buts);
                    else
                        insertLine(div_team1, id, item.id, item.name, item.surname);
                });

                $.each(data.value.team2.players, function(i, item) {
                    var result = $.grep(data.value.prestations, function(e){ return e.player.id == item.id; });
                    if(result.length == 1)
                        insertLine(div_team2, id, item.id, item.name, item.surname, result[0].enterTime, result[0].leaveTime, result[0].yellowCards, result[0].redCards, result[0].buts);
                    else
                        insertLine(div_team2, id, item.id, item.name, item.surname);
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

function insertLine(div, idMatch, idPlayer, name, surname, enterTime, leaveTime, yellowCards, redCards, buts) {
    div.find('.team_players').append(
        $('<tr>').data('id', idPlayer).append(
            $('<td>').addClass('col-xs-4').html(name + ' ' + surname)
        ).append(
            $('<td>').addClass('col-xs-2').append(
                $('<input>').attr('type', 'number').addClass('form-control enterTime').val(enterTime)
            )
        ).append(
            $('<td>').addClass('col-xs-2').append(
                $('<input>').attr('type', 'number').addClass('form-control leaveTime').val(leaveTime)
            )
        ).append(
            $('<td>').addClass('col-xs-1').append(
                $('<input>').attr('type', 'number').addClass('form-control yellowCards').val(yellowCards)
            )
        ).append(
            $('<td>').addClass('col-xs-1').append(
                $('<input>').attr('type', 'number').addClass('form-control redCards').val(redCards)
            )
        ).append(
            $('<td>').addClass('col-xs-2').append(
                $('<input>').attr('type', 'number').addClass('form-control buts').val(buts)
            )
        )
    );

    $('.editPrestations').data('id', idMatch)
}

$('#editPrestations').on('hide.bs.modal', function() {
    var div_team1 = $('#modal_div_team1');
    var div_team2 = $('#modal_div_team2');

    div_team1.find('.team_players').empty();
    div_team2.find('.team_players').empty();
});

$('.editPrestations').on('click', function() {
    var idMatch = $(this).data('id');

    var div_team1 = $('#modal_div_team1');
    var div_team2 = $('#modal_div_team2');

    var prestations = [];

    div_team1.find('.team_players').find('tr').each(function (i, item) {
        var id = $(item).data('id');
        var enterTime = $(item).find('.enterTime').val();
        var leaveTime = $(item).find('.leaveTime').val();
        var yellowCards = $(item).find('.yellowCards').val();
        var redCards = $(item).find('.redCards').val();
        var buts = $(item).find('.buts').val();

        prestations.push({ id: id, enterTime: enterTime, leaveTime: leaveTime, yellowCards: yellowCards, redCards: redCards, buts: buts });
    });

    div_team2.find('.team_players').find('tr').each(function (i, item) {
        var id = $(item).data('id');
        var enterTime = $(item).find('.enterTime').val();
        var leaveTime = $(item).find('.leaveTime').val();
        var yellowCards = $(item).find('.yellowCards').val();
        var redCards = $(item).find('.redCards').val();
        var buts = $(item).find('.buts').val();

        prestations.push({ id: id, enterTime: enterTime, leaveTime: leaveTime, yellowCards: yellowCards, redCards: redCards, buts: buts });
    });

    $.ajax({
        type: 'POST',
        url: Routing.generate('match_ajax_updateprestations'),
        data: {
            id: idMatch,
            prestations: JSON.stringify(prestations)
        },
        error: function (request, error) { // Info Debuggage si erreur
            console.log("Erreur : responseText: " + request.responseText);
        },
        success: function (data) {
            if (data.status == 'ok') {
                $('#editPrestations').modal('hide');

                var line = $('tr[data-id="' + idMatch + '"]');
                line.replaceWith(data.return);
                line = $('tr[data-id="' + idMatch + '"]');
                line.effect("highlight", {color: '#c9c9c9'}, 5000);

                $('.modal_alert_success').modal('show');
                setTimeout(function(){
                    $(".modal_alert_success").modal('hide');
                }, 1700);
            } else {
                $('.editPrestationsError').html(data.message);

                console.log(data.debug);
            }
        }
    });
});