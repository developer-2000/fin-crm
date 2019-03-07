$(function () {
    $('#change_project').on('submit', changeProject);
});

function changeProject() {
    showMessage('wait', 'Обработка');
    disableButton($('#change'), true);
    $.post('/ajax/orders/change-project', $(this).serialize(), function (json) {
        if (json.success) {
            $('#change').fadeOut(0);
            showMessage('success', 'Данные сохранены');
        } else {
            showMessage('error', 'Данные не сохранены');
        }

        disableButton($('#change'));
    }).fail(function (json) {
        try {
            var response = JSON.parse(json.responseText);
            if (response.errors) {
                var messages = '';
                $.each(response.errors, function (name, value) {
                    var message = '';
                    var fieldName = deleteDote(name);
                    if (fieldName) {
                        $.each(value, function (key, error) {
                            var obj = document.getElementById(fieldName);
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

                            name = name.split('_').join(' ');

                            if (name != 'name') {
                                message = error.replace(name, '<strong>"' + label + '"</strong>');
                            } else {
                                message = error.replace('Название', '<strong>"' + label + '"</strong>');
                            }
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
                    $('#change_project .error-messages').empty();
                    $('#change_project .error-messages').append(messages);
                    $('#change_project .error-messages').slideDown();
                }
                $('.ns-close').click();

            } else {
                showMessage('error', 'Произошла ошибка');
            }
        } catch (e) {
            showMessage('error', 'Произошла ошибка');
        }

        disableButton($('#change'));
    });
    return false;
}