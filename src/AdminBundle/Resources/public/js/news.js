// TODO : Editeur WYSIWYG ?

$('.addNews').on('click', function() {
    var modal = $('#addNews');

    var button = $(this);

    var title = modal.find('#addNew_Title').val();
    var message = modal.find('#addNew_Message').val();
    var date = modal.find('#addNew_Date').val();

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
                message: message,
                date: date
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
            url: Routing.generate('admin_news_ajax_add'),
            data: {
                title: title,
                message: message,
                date: date
            },
            error: function (request, error) { // Info Debuggage si erreur
                console.log("Erreur : responseText: " + request.responseText);
            },
            success: function (data) {
                if (data.status == 'ok') {
                    modal.modal('hide');

                    if ($('.table_news_tbody').find('tr td').length == 1)
                        $('.table_news_tbody').find('tr td').remove();

                    $('.table_news_tbody').append(data.return);

                    $('.modal_alert_success').modal('show');
                    setTimeout(function () {
                        $(".modal_alert_success").modal('hide');
                    }, 1700);
                } else {
                    $('.addNewError').html(data.debug);
                }
            }
        });
    }
});

$('#addNews').on('hide.bs.modal', function() {
    var button = $(this).find('.addNews');

    $(this).find('#addNew_Title').val('');
    $(this).find('#addNew_Message').val('');
    $(this).find('.modal-title').html("Ajouter une news");

    button.find('.fa').removeClass('fa-pencil fa-pulse fa-spinner').addClass('fa-plus');
    button.removeAttr('disabled');
    button.removeData('edit');
});

$('.table_news_tbody').on('click', 'button[data-action="delete"]', function() {
    var id = $(this).data('id');
    var modal = $('.modal_alert_confirmation');

    modal.find('.modal-confirmation-yes').data('id', id);
    modal.modal('show');
});

$('.modal-confirmation-yes').on('click', function() {
    var id = $(this).data('id');

    $.ajax({
        type: 'POST',
        url: Routing.generate('admin_news_ajax_delete'),
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
                    if($('.table_news_tbody tr').length == 0) {
                        $('.table_news_tbody').append(
                            $('<tr>').append(
                                $('<td>').attr('colspan', '6').html('<i>Aucune news</i>')
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

$('.table_news_tbody').on('click', 'button[data-action="edit"]', function() {
    var id = $(this).data('id');
    var modal = $('#addNews');

    $.ajax({
        type: 'POST',
        url: Routing.generate('admin_news_ajax_get'),
        data: {
            id: id
        },
        error: function (request, error) { // Info Debuggage si erreur
            console.log("Erreur : responseText: " + request.responseText);
        },
        success: function (data) {
            if(data.status == 'ok') {
                modal.find('#addNew_Title').val(data.news.title);
                modal.find('#addNew_Message').val(data.news.message);
                modal.find('#addNew_Date').val(moment.unix(data.news.publishDate.timestamp).format("DD/MM/YYYY HH:MM"));

                modal.find('.modal-title').html("Modification d'une news");
                modal.find('.fa-plus').removeClass('fa-plus').addClass('fa-pencil');

                modal.find('.addNews').data('id', id).data('edit', '1');
                modal.modal('show');
            } else {
                $('.modal-body-more-info').html(data.debug);
                $('.modal_alert_error').modal('show');
            }
        }
    });
});