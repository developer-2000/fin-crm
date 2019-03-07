$(function() {
    $.post('/online/', {}, function(json) {
    }, 'json');
    setInterval(function() {
        $.post('/online/', {}, function(json) {
        }, 'json');
    }, 120000);

    $('a.pop').hover(function () {
        $(this).siblings('.data_popup').fadeToggle(100);

    });
    $('a.pop').on('click', function () {
        return false;
    });

    window.targetUserId = $('#user_id').attr('data-id');
    Pace.options.ajax.trackWebSockets = false;

    $('#main_languages .item-language').on('click', changeLanguage);

});
setTimeout(getButtonUp(), 0);

$('.monitoring-menu').click(function ( e ) {
    e.preventDefault();
    if ($('#submenu').css('display') == 'none'){
        $('#submenu').css('display', 'flex');
        $('.drop-icon').removeClass('fa-angle-down').addClass('fa-angle-up');
    }
    else {
        $('#submenu').css('display', 'none');
        $('.drop-icon').removeClass('fa-angle-up').addClass('fa-angle-down');
    }
});

function logout() {
    document.cookie = 'uri=' + window.location.pathname + window.location.search;
    location.href = '/loguot';
}

var bottomPosition;
var topPosition;
function pageUp() {
    bottomPosition = $(window).scrollTop();
    $('.wrapper_up').fadeOut(1);
    $('.block_down').fadeIn(200);
    $('html, body').animate({scrollTop: 0}, 500);
}

function pageDown() {
    $('.block_down').fadeOut(200);
    $('html, body').animate({scrollTop: bottomPosition}, 500);
}

function getButtonUp() {
    $('.wrapper_up').on('click', pageUp);
    $('.block_down').on('click', pageDown);
    var heightMenu = $('.nav-pills').height();
    var heightWindow = $(window).height();
    var minHeight = 0;
    if (heightMenu > heightWindow / 2) {
        minHeight = heightMenu + 100 - heightWindow / 2 ;
    } else {
        minHeight = heightWindow / 2;
    }

    if ($(window).scrollTop() > minHeight) {
        $('.wrapper_up').fadeIn(200);
    }

    var i = $(window).scrollTop();
    $(window).scroll(function () {
        if ($(this).scrollTop() > minHeight) {
            if ($('.block_down').css('display') == 'none') {
                $('.wrapper_up').fadeIn(200);
            }
        } else {
            $('.wrapper_up').fadeOut(200);
        }
        if (i < $(this).scrollTop()) { // скрываем когда скролим вниз
            if ($(this).scrollTop() > minHeight) {
                $('.block_down').fadeOut(200);
            }
        }
        i = $(this).scrollTop();
    });


    //right buttons
    $('.btn_down').on('click', function () {
        topPosition = $(window).scrollTop();
        if (bottomPosition > $(window).scrollTop()) {
            var pos = bottomPosition;
            bottomPosition = $(window).scrollTop();
            $('html, body').animate({scrollTop: pos}, 500);
        } else {
            $('html, body').animate({scrollTop: $(document).height() - $(window).height()}, 500);
        }
    });

    $('.btn_up').on('click', function () {
        bottomPosition = $(window).scrollTop();

        if (topPosition < $(window).scrollTop()) {
            var pos = topPosition;
            topPosition = $(window).scrollTop();
            $('html, body').animate({scrollTop: pos}, 500);
        } else {
            $('html, body').animate({scrollTop: 0}, 500);
        }


    })

}

function disableButton(obj, status) {
    status = status || false;

    obj.prop('disabled', status);
}

function changeLanguage() {
    let code = $(this).attr('data-content');

    $.post('/ajax/user/settings', { language : code, id : window.targetUserId}, function (json) {
        window.location.reload();
    }).fail(function (json) {
        window.location.reload();
    })
}

function showMessage(type, message) {
    $('.ns-box').remove();


    message = message || $('#messages_alert #' + type).text();

    let settings = {
        message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
        layout: 'bar',
        effect: 'slidetop',
        type: type,
        ttl: 3000,
    };

    if (type == 'processing') {
        settings.message = '<span class="fa fa-spinner fa-2x alert_spinner"></span><p>' + message + '</p>';
        settings.ttl = 60000;
        settings.type = 'notice';
    }

    let notification = new NotificationFx(settings);

    notification.show();
}


function setLoader(elem, clas = 'nb-spinner') {
  //Class:
  // nb-spinner , reverse-spinner , multi-spinner
  // https://github.com/CamdenFoucht/LoadLab
  $('#'+elem).html('<div class="'+clas+'"></div>');
}

function getValidationMessages(json) {
    try {
        var response = JSON.parse(json.responseText);
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
                $('.error-messages').empty();
                $('.error-messages').append(messages);
                $('#order_data .error-messages').slideDown();
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


function deleteDote(fieldName) {
    var params = fieldName.split('.');
    var result = fieldName;
    if (params.length > 1) {
        result = '';
        for (var i = 0; i < params.length; i++) {
            if (i == 0) {
                result += params[i] + '[';
            } else if (i + 1 != params.length) {
                result += params[i] + '][';
            } else if (i + 1 == params.length) {
                result += params[i] + ']';
            }
        }
    }
    return result;
}
