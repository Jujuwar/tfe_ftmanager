var timer;
var timeBeforeEdit = 1200;
var editMode = false;

$('#table_matchs').on("mousedown", 'td[data-action=editMode]', function() {
    if(!editMode) {
        var column = $(this).data('column');
        var id = $(this).parent().data('id');

        timer = setTimeout(function () {
            inlineEdit(id, column);
            editMode = true;
        }, timeBeforeEdit);
    }
}).on("mouseup mouseleave", function() {
    clearTimeout(timer);
});

function inlineEdit(id, column) {
    timer = 0;

    $.ajax({
        type: 'POST',
        url: Routing.generate('admin_matchs_ajax_get'),
        data: {
            id: id
        },
        error: function (request, error) { // Info Debuggage si erreur
            console.log("Erreur : responseText: " + request.responseText);
        },
        success: function (data) {
            if (data.status == 'ok') {
                var line = $('tr[data-id=' + id + '] td[data-column=' + column + ']');

                switch(column) {
                    case 'date':
                        line.empty();
                        var container = $('<div>').attr('style', 'position:relative;');
                        if(data.value.date != null)
                            var input = $('<input>').addClass('form-control').attr('data-mode', 'edit').data('old', moment.unix(data.value.date.timestamp).format('DD/MM/YYYY HH:mm')).val(moment.unix(data.value.date.timestamp).format('DD/MM/YYYY HH:mm'));
                        else
                            var input = $('<input>').addClass('form-control').attr('data-mode', 'edit').data('old', 'Aucune date sélectionnée');

                        $(container).append(input);
                        line.append(container);

                        $(input).datetimepicker({
                            locale: 'fr',
                            format: 'DD/MM/YYYY HH:mm',
                            useCurrent: false,
                            icons: {
                                time: 'fa fa-clock-o',
                                date: 'fa fa-calendar',
                                up: 'fa fa-chevron-up',
                                down: 'fa fa-chevron-down',
                                previous: 'fa fa-chevron-left',
                                next: 'fa fa-chevron-right',
                                today: 'fa fa-screenshot',
                                clear: 'fa fa-trash',
                                close: 'fa fa-remove'
                            }
                        });
                        break;

                    case 'field':
                        var select = $('<select>').addClass('form-control').attr('data-mode', 'edit');

                        $.ajax({
                            type: 'POST',
                            url: Routing.generate('admin_matchs_ajax_getfields'),
                            async: false,
                            error: function (request, error) { // Info Debuggage si erreur
                                console.log("Erreur : responseText: " + request.responseText);
                            },
                            success: function (data2) {
                                if (data2.status == 'ok') {
                                    line.empty();
                                    $.each( data2.values, function(i, item) {
                                        var option = $('<option>', {value: item.id, text: item.name});

                                        if(data.value.field != null && data.value.field.id == item.id)
                                            option.attr('selected', 'selected');

                                        select.append(option);
                                    });
                                    line.append(select.data('old', data.value.field != null ? data.value.field.name : 'Aucun terrain sélectionné'));
                                } else {
                                    $('.modal-body-more-info').html(data2.message);
                                    $('.modal_alert_error').modal('show');

                                    console.log(data2.debug);
                                }
                            }
                        });
                        break;

                    case 'referee':
                        var select = $('<select>').addClass('form-control').attr('data-mode', 'edit');

                        $.ajax({
                            type: 'POST',
                            url: Routing.generate('admin_matchs_ajax_getreferee'),
                            async: false,
                            error: function (request, error) { // Info Debuggage si erreur
                                console.log("Erreur : responseText: " + request.responseText);
                            },
                            success: function (data2) {
                                if (data2.status == 'ok') {
                                    line.empty();
                                    $.each( data2.values, function(i, item) {
                                        var option = $('<option>', {value: item.id, text: item.username});

                                        if(data.value.referee != null && data.value.referee.id == item.id)
                                            option.attr('selected', 'selected');

                                        select.append(option);
                                    });
                                    line.append(
                                        select.data('old', data.value.referee != null ? data.value.referee.username : 'Aucun arbitre sélectionné')
                                    );
                                } else {
                                    $('.modal-body-more-info').html(data2.message);
                                    $('.modal_alert_error').modal('show');

                                    console.log(data2.debug);
                                }
                            }
                        });
                        break;
                }

                line.append(
                    $('<button>').addClass('btn btn-primary btn-sm m-t-1').text('Valider').data('column', column).attr('data-action', 'edit')
                ).append(
                    $('<button>').addClass('btn btn-default btn-sm m-t-1 m-l-1').text('Annuler').data('column', column).attr('data-action', 'cancel')
                );
            } else {
                $('.modal-body-more-info').html(data.message);
                $('.modal_alert_error').modal('show');

                console.log(data.debug);
            }
        }
    });
}

$('.table_matchs_tbody').on('click', 'button[data-action="edit"]', function() {
    var id = $(this).parent().parent().data('id');

    var date;
    var field;
    var referee;


    switch($(this).data('column')) {
        case 'date':
            date = $('input[data-mode=edit]').val();
            break;

        case 'referee':
            referee = $('select[data-mode=edit]').val();
            break;

        case 'field':
            field = $('select[data-mode=edit]').val();
    }

    updateMatch(id, date, field, referee);

    editMode = false;
});

$('.table_matchs_tbody').on('click', 'button[data-action="cancel"]', function() {
    var id = $(this).parent().parent().data('id');

    var date = '';
    var field = '';
    var referee = '';


    switch($(this).data('column')) {
        case 'date':
            date = $('input[data-mode=edit]').data('old');
            break;

        case 'referee':
            referee = $('select[data-mode=edit]').data('old');
            break;

        case 'field':
            field = $('select[data-mode=edit]').data('old');
    }

    cancelEdit(id, date, field, referee);
});

function updateMatch(id, date, field, referee) {
    $.ajax({
        type: 'POST',
        url: Routing.generate('admin_matchs_ajax_edit'),
        data: {
            id: id,
            date: date,
            field: field,
            referee: referee
        },
        error: function (request, error) { // Info Debuggage si erreur
            console.log("Erreur : responseText: " + request.responseText);
        },
        success: function (data) {
            if (data.status == 'ok') {
                var line = $('tr[data-id="' + id + '"]');
                line.replaceWith(data.return);
                line = $('tr[data-id="' + id + '"]');
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
}

function cancelEdit(id, date, field, referee) {
    if(date != '')
        $('tr[data-id = ' + id + '] td[data-column=date]').empty().append(date);

    if(field != '')
        $('tr[data-id = ' + id + '] td[data-column=field]').empty().append(field);

    if(referee != '')
        $('tr[data-id = ' + id + '] td[data-column=referee]').empty().append(referee);

    editMode = false;
}
