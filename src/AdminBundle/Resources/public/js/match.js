var timer;
var timeBeforeEdit = 1500;
var editMode = false;

$('td[data-action=editMode]').on("mousedown", function() {
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
                        line.append(
                            $('<input>').addClass('form-control').attr('data-mode', 'edit').val(moment.unix(data.value.date.timestamp).format('DD/MM/YYYY HH:mm'))
                        );
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
                                        select.append($('<option>', {value: item.id, text: item.name}));
                                    });
                                    line.append(
                                        select
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