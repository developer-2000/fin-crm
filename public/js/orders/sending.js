$(function () {
    $('.result-sending .target').on('change', changeTarget);

    $('#cancel_send').on('click', cancelSend);

    $('#order_data').on('click', '#delivery_note_create', createNewDeliveryNote);
    $('#order_data').on('click', '#delivery_note_edit', editExistDeliveryNote);
    $('#order_data').on('click', '#delivery_note_delete', deleteExistDeliveryNote);


    //for russian post
    if ($('option:selected', $('#procStatus')).attr('data-action-alias') != 'ready_send'
        || $('option:selected', $('#procStatus')).val() == 3) {
        $('#get_sticker2').addClass('linkIsDisabled');
        $('#get_blank_113').addClass('linkIsDisabled');
        $('#get_blank_107').addClass('linkIsDisabled');
        $('#get_blank_7').addClass('linkIsDisabled');
    } else {
        $('#get_sticker2').removeClass('linkIsDisabled');
        $('#get_blank_113').removeClass('linkIsDisabled');
        $('#get_blank_107').removeClass('linkIsDisabled');
        $('#get_blank_7').removeClass('linkIsDisabled');
    }

    /*change proc status and sub status*/
    $('#procStatus').change(function () {
        var procStatus = $(this).val();
        //for russian post
        if ($('option:selected', this).attr('data-action-alias') != 'ready_send') {
            $('#get_sticker2').addClass('linkIsDisabled');
            $('#get_blank_113').addClass('linkIsDisabled');
            $('#get_blank_107').addClass('linkIsDisabled');
            $('#get_blank_7').addClass('linkIsDisabled');
        } else {
            $('#get_sticker2').removeClass('linkIsDisabled');
            $('#get_blank_113').removeClass('linkIsDisabled');
            $('#get_blank_107').removeClass('linkIsDisabled');
            $('#get_blank_7').removeClass('linkIsDisabled');
        }
        var orderId = $('.order_id').text();
        if (procStatus) {
            $.post('/ajax/orders/proc-status2-load/' + procStatus + '/' + orderId, function (data) {
                if (data.html) {
                    $('.proc-statuses2').empty();
                    $('.proc-statuses2').append(data.html);
                }
            }).fail(function (data) {
                var errors = [];
                $.each(JSON.parse(data.responseText), function (e, obj) {
                    errors.push(obj + '<br>');
                });
                showMessage('error', errors);
            });
        }
    });
    $(document).on('click', '.sent-to-print', function () {
        $.post('/ajax/run-action', $.param({
                orders: [$('.order_id').text()],
                action: 'to_print',
                project_id: $('#project_id').val(),
                status: Number($(this).attr('data-proc-status'))
            }),
            function (data) {
                if (data.success) {
                    showMessage('success', data.message);
                    $('.sent-to-print').prop('disabled', true);
                }
                if (data.exist_in_pass) {
                    showMessage('warning', data.message);
                    $('.sent-to-print').prop('disabled', true);
                }
            }).fail(function (data) {
            var errors = [];
            $.each(JSON.parse(data.responseText), function (e, obj) {
                errors.push(obj + '<br>');
            });
            showMessage('error', errors);
        });
    });

    $('#statuses_update').click(function () {
        if (procStatus) {
            $.post('/ajax/orders/proc-statuses/update', $.param({
                orderId: $('.order_id').text(),
                procStatus: $('#procStatus').val(),
                procStatus2: $('#procStatus2').val()
            }), function (data) {
                if (data.success) {
                    showMessage('success', data.message);
                }
            }).fail(function (data) {
                var errors = [];
                $.each(JSON.parse(data.responseText), function (e, obj) {
                    errors.push(obj + '<br>');
                });
                showMessage('error', errors);
            });
        }
    });
});

function changeTarget() {
    var targetId = $(this).val();
    var targetName = $(this).attr('name');
    var parent = $('.fields');

    $.post('/ajax/order/change-target-in-order', {
        targetId: targetId,
        orderId: window.orderId,
        targetName: targetName,
        sending: true
    }, function (json) {
        if (json.success) {
            $('#other_target_fields').empty();
            parent.empty();
            parent.append(json.html);

            if (json.html2) {
                $('#other_target_fields').append(json.html2);
            }

            showMessage('success', json.message);

        } else {
            showMessage('error', json.message);
        }
    }).fail(function (json) {
        showMessage('error');
    });
}

function createNewDeliveryNote() {
    var btn = $(this);
    var deliveryData = $('#order_data').serialize();
    window.checkMessage = false;
    disableButton(btn, true);
    showMessage('processing');
    $('#order_data .has-error').removeClass('has-error');

    $.post('/ajax/delivery-note-create/', deliveryData, function (json) {
        getSuccessMessage(json);

        $('.alert .close').on('click', setStyleErrorBlock);
        if (json.integration && json.integration.success) {
            showMessage('success', json.integration.message);
            $('.sent-to-print').removeClass('hidden').removeClass('disabled');
        } else if (json.errors) {
            showMessage('error', json.errors);
        } else if (json.integration && !json.integration.success) {
            showMessage('error', json.integration.message);
        }
        disableButton(btn);
    }).fail(function (json) {
        getErrorMessage(json);
        disableButton(btn);
    });
}

function editExistDeliveryNote() {
    var btn = $(this);
    var deliveryData = $('#order_data').serialize();
    window.checkMessage = false;
    disableButton(btn, true);
    showMessage('processing');
    $('#order_data .has-error').removeClass('has-error');
    var formData = $('form#order_data').serialize();
    $.post('/ajax/delivery-note-edit', formData + '&' + $.param({
        delivery_note_ref: $('#delivery_note_ref').val()
    }), function (json) {
        getSuccessMessage(json);

        $('.alert .close').on('click', setStyleErrorBlock);
        // if (json.integration) {
        //     showMessage('success', 'Экспресс накладная успешно изменена!');
        // } else if (json.errors) {
        //     showMessage('error', json.errors);
        // } else if (!json.integration.success) {
        //     showMessage('error', 'Произошла ошибка, накладная не изменена!');
        // }


        if (json.integration && !json.integration.errors) {
            showMessage('success', json.integration.message);
        } else if (json.integration && json.integration.errors) {
            var errors = [];
            $.each(json.integration.errors, function (e, obj) {
                errors.push(obj + '<br>');
            });
            showMessage('error', errors);
        }

        disableButton(btn);
    }).fail(function (json) {
        getErrorMessage(json);
        disableButton(btn);
    });
}

function deleteExistDeliveryNote() {
    var btn = $(this);
    window.checkMessage = false;
    disableButton(btn, true);
    showMessage('processing');
    $('#order_data .has-error').removeClass('has-error');
    var formData = $('form#order_data').serialize();
    $.post('/ajax/delivery-note-delete', formData, function (json) {
        $('.alert .close').on('click', setStyleErrorBlock);
        if (typeof window['deleveryNoteDeleteProcessed'] == 'function') {
            deleveryNoteDeleteProcessed(json);
        }

        if (json.integration && !json.integration.errors) {
            showMessage('success', json.integration.message);
            $('#trackVal').val('');
        } else if (json.integration && (json.integration.errors || !json.integration.success)) {

            if (json.integration.errors) {
                var errors = [];
                $.each(json.integration.errors, function (e, obj) {
                    errors.push(obj + '<br>');
                });
                showMessage('error', errors);
            }
        }
        // else if (!json.integration) {
        //     showMessage('error', 'Произошла ошибка, накладная не удалена!');
        // }
        disableButton(btn);
    }).fail(function (json) {
        getErrorMessage(json);
        disableButton(btn);
    });
}

function getErrorMessage(json) {
    try {
        var response = JSON.parse(json.responseText) ? JSON.parse(json.responseText) : json;
        getSuccessMessage(response);
        if (response.errors) {
            var messages = '';
            $.each(response.errors, function (name, value) {
                var message = '';
                var fieldName = deleteDote(name);
                if (fieldName) {
                    $.each(value, function (key, error) {
                        var obj = document.getElementsByName(fieldName);
                        var parent = $(obj).parents('.form-group');
                        var label = $('label[for="' + fieldName + '"]').text();

                        if (!label.length) {
                            label = parent.find('label').text();
                        }
                        if (!label.length) {
                            label = name;
                        }

                        parent.find('.help-block').remove();
                        parent.addClass('has-error');

                        message = error.replace(name, '<strong>"' + label + '"</strong>');

                        if (message.length) {
                            messages += '<div class="alert alert-danger fade in"> ' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> ' +
                                '<i class="fa fa-times-circle fa-fw fa-lg"></i> ' + message +
                                '</div>';
                        }
                    });
                }
            });
            if (messages.length) {
                if (!window.checkMessage) {
                    $('.error-messages').empty();
                    $('.error-messages').append(messages);
                    $('#order_data .error-messages').slideDown();
                    window.checkMessage = true;
                } else {
                    $('.error-messages').append(messages);
                }
            }
            $('.ns-close').click();

            $('.alert .close').on('click', setStyleErrorBlock);
        } else {
            showMessage('error');
        }
    } catch (e) {
        showMessage('error');
    }
}

function getSuccessMessage(json) {
    if (typeof window['processResponse'] == 'function') {
        processResponse(json);
    }

    var messages = '';
    if (json.success) {
        messages = createAlertsHtml(json.success);
        $('#order_data').find('has-error').removeClass('has-error');
        $('.ns-close').click();
    } else {
        showMessage('error')
    }
    if (messages.length) {
        if (!window.checkMessage) {
            $('.error-messages').empty();
            $('.error-messages').append(messages);
            $('#order_data .error-messages').slideDown();
            window.checkMessage = true;
        } else {
            $('.error-messages').append(messages);
        }
    }

}

function cancelSend() {
    disableButton($(this), true);
    $.post('/ajax/orders/cancel-send', {id: $(this).attr('data-id')}, function (json) {
        if (json.success) {
            location.reload();
        } else {
            showMessage('error');
        }
        disableButton($(this));
    });
}

