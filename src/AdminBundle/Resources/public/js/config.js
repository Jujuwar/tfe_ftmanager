$(function () {
    $('#date_start_inscription').datetimepicker({
        locale : 'fr',
        format: 'DD/MM/YYYY HH:mm',
        useCurrent : false,
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

    $('#date_end_registration').datetimepicker({
        locale : 'fr',
        format: 'DD/MM/YYYY HH:mm',
        useCurrent : false,
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

    $("#date_start_inscription").on("dp.change, dp.show", function (e) {
        $('#date_end_registration').data("DateTimePicker").minDate(e.date);
    });

    $("#date_end_registration").on("dp.change, dp.show", function (e) {
        $('#date_start_inscription').data("DateTimePicker").maxDate(e.date);
    });

    $("#date_start_inscription").on("dp.hide", function(e) {
        updateDate("start_registration", $(this).val())
    });

    $("#date_end_registration").on("dp.hide", function(e) {
        updateDate("end_registration", $(this).val())
    });
});

function updateDate(column, date) {
    console.log(event.data);
    $.ajax({
        type: 'POST',
        url: Routing.generate('admin_config_ajax_editDate'),
        data: {
            column: column,
            value: date
        },
        error: function (request, error) { // Info Debuggage si erreur
            console.log("Erreur : responseText: " + request.responseText);
        },
        success: function (data) {
            console.log(data);
            if (data.status == 'ok') {
                if(data.return) {
                    $('.modal_alert_success').modal('show');
                    setTimeout(function () {
                        $(".modal_alert_success").modal('hide');
                    }, 1700);
                }
            } else {
                $('.modal-body-more-info').html(data.message);
                $('.modal_alert_error').modal('show');

                console.log(data.debug);
            }
        }
    });
}