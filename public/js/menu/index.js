$(function () {
    $('.nestable').each(function () {
        $(this).nestable({
            group: $(this).attr('data-group'),
            maxDepth : 3
        } );
    })
        .on('change', updateOutput);

    $(document).on('click', '.delete_item_menu', deleteMenu);
    $(document).on('click', '.edit_menu', getEditView);
    $(document).on('submit', '#edit_form', updateMenu);
    $('#create_menu #form_menu').on('submit', createMenu);
});

function updateMenu() {
    let form = $(this);

    showMessage('processing');
    $.ajax({
        url: form.attr('action'),
        data: form.serialize(),
        type: 'PUT',
        async: true,
        success: function (json) {
            if (json.success) {
                showMessage('success', json.message);
            } else {
                showMessage('error', json.message);
            }
        },
        error : function (result) {
            getValidationError(result, form);
        }
    });

    return false;
}

function getEditView() {
    let id = $(this).data('id');
    let block = $('#edit_block');
    block.fadeOut(200);
    block.empty();
    $.get('/menu/edit/' + id, function (view) {
        block.append(view);
        block.fadeIn(300);
    });

    return false;
}

function createMenu() {
    let form = $(this);

    showMessage('processing');

    $.post(form.attr('action'), form.serialize(), function (json) {
        if (json.success) {
            showMessage('success', json.message);
            $('.md-close.close').click();
            let list = $('.nestable .dd-list:first');
            list.append(json.html);
            form[0].reset();
        } else {
            showMessage('error', json.message);
        }
    }).fail(function (json) {
        getValidationError(json, form);
    });

    return false;
}

function updateOutput(e) {
    var list = e.length ? e : $(e.target),
        output = list.data('output');
    if (window.JSON) {
        var jsonData = window.JSON.stringify(list.nestable('serialize'));
        if (list.nestable('serialize').length) {
            $.post('menu/change-position', {json : jsonData}, function (json) {
                if (!json.success) {
                    showMessage('error', json.message);
                }
            })
        }
    }
    else {
        output.val('JSON browser support required for this demo.');
    }
}

function deleteMenu() {
    var item = $(this).parents('.dd-item:first');
    var id = $(this).attr('data-id');
    var parent = item.parents('.dd-item:first');

    showMessage('processing');
    $.ajax({
        url: 'menu/' + id,
        type: 'DELETE',
        success: function(json) {
            if (json.success) {
                showMessage('success', json.message);
                item.fadeOut(300);
                setTimeout(function () {
                    if (parent.length && +parent.find('.dd-item').length <= 1) {
                        parent.children('button').remove();
                    }
                    item.remove();
                }, 300);
            } else {
                showMessage('error', json.message);
            }
        },
        fail : function (result) {
            showMessage('error', result)
        }
    });

    return false;
}

function getValidationError(json, form) {
    try {
        form.find('.error_messages').empty();
        var response = JSON.parse(json.responseText);
        if (response.errors) {
            var messages = '';
            $.each(response.errors, function (name, value) {
                var message = '';
                var fieldName = deleteDote(name);
                if (fieldName) {
                    $.each(value, function (key, error) {
                        var obj = form.find('[name="' + fieldName + '"]');
                        var parent = $(obj).parents('.form-group');
                        var label = form.find('label[for="' + fieldName + '"]').text();

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
                form.find('.error_messages').append(messages);
                form.find('.error_messages').slideDown();
            }
            $('.ns-close').click();
        } else {
            showMessage('error');
        }
    } catch (e) {
        console.log(e);
        showMessage('error');
    }
}