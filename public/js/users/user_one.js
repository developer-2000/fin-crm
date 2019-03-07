$(function () {
    /*ID user*/
    window.id = $('#current_user_id').attr('data-id');
    $('.delete_ip').on('click', deleteIp);
    $('#new_input').on('click', addNewInput);
    $('#generate_pas_two').on('click', function () {
        $('#password_sip').val(generate());
    });
    $('#generate_pas').on('click', function () {
        $('#password').val(generate());
    });
    // $('#add_new_password').on('click', newPassword);
    // $('#add_new_password_two').on('click', newPasswordTwo);
    // $('#block').on('click', block);
    // $('#nat').on('click', nat);
    // $('#save_IP').on('click', saveIP);
    // $('#update_role').on('click', updateRole);
    // $('#user_group').on('click', updateGroup);
    // $('#fine_btn').on('click', fine);
    // $('#change_name').on('click', changeNameSurname);
    $('#data_crm').on('submit', changeDataUser);
    $('#data_sip').on('submit', changeDataSip);
    $('#create_sip').on('click', slideForm);

    // $('#role').on('change', getRole);


    $('#roles').select2({
        placeholder: "",
        minimumInputLength: 0,
        multiple: true,
        ajax: {
            method: 'get',
            url: '/roles/find',
            dataType:
                'json',
            data:
                function (params) {
                    return {
                        q: $.trim(params),
                        project_id: $('#project').val() ? $('#project').val().split(",") : ''
                    };
                },
            results: function (data) {
                return {
                    results: data,
                    "pagination": {
                        "more": true
                    }
                };
            }
        },
    });
    if ($('#roles').attr('data-roles')) {
        var arrayForSelect2 = [];
        $.each(JSON.parse($('#roles').attr('data-roles')), function (element, value) {
            arrayForSelect2.push(value);
        });
        $("#roles").select2('data', arrayForSelect2);
    }
    $('#project_id').select2({
        ajax: {
            url: '/projects/find',
            dataType: 'json',
            data: function (params) {
                return {
                    query: $.trim(params)
                };
            },
            results: function (data) {
                return {
                    results: data
                };
            }
        },
        width : '100%'
    });

    $('#sub_project_id').select2({
        ajax: {
            url: '/sub_projects/find',
            dataType: 'json',
            data: function (params) {
                return {
                    query: $.trim(params),
                    project_id : $('#project_id').val() ? $('#project_id').val() : 0
                };
            },
            results: function (data) {
                return {
                    results: data
                };
            }
        },
        width : '100%'
    });
    setDefaultValue();

    /**
     * выбор фотки
     */
    $(document).on('change', ':file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });
    $(document).ready( function() {
        $(':file').on('fileselect', function(event, numFiles, label) {

            var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;

            if( input.length ) {
                input.val(log);
            } else {
                if( log ) alert(log);
            }

        });
    });

    $('#password').pwstrength();
    $("#birthday").mask("99/99/9999");
    $('#time_zone').select2({width : '100%'});
});

function setDefaultValue() {
    var defData = $('#sub_project_id').attr('data-content');
    var defaultProject = $('#project_id').attr('data-content');

    if (defData) {
        $('#sub_project_id').select2("data", JSON.parse(defData));
    }
    if (defaultProject) {
        $('#project_id').select2("data", JSON.parse(defaultProject));
    }
}

function changeDataUser() {
    var files = $('input[type=file]').prop('files')[0];
    var data ={};
        $(this).find('input,select').not('[type="submit"]').each(function() {
        var name = $(this).attr('name');
        data[name] = $(this).val();
        if (name == 'block') {
            data[name] = $(this).prop('checked') ? 1 : 0;
        }
    });
    var form_data = new FormData();
    form_data.append('file', files);
    form_data.append('inputs', JSON.stringify(data));
    $.ajax({
        type : 'post',
        url : '/change-data-users-ajax/' + window.id,
        data : form_data,
        cache : false,
        contentType : false,
        processData : false,
        beforeSend : function () {
            getMessage("wait", 'Обработка');
        },
        success:  function (json) {
            $('#data_crm').find('.has-error').removeClass('has-error');
            if (json.errors) {
                for (key in json.errors) {
                    $('#' + key).parents('.form-group').addClass('has-error');
                }
                getMessage('error', 'Ошибка');
            }
            if(json.status) {
                getMessage('success', 'Данные изменены');
            }
        }
    });
    return false;
}

// function getRole() {
//     var currentSelect = $(this).find('option:selected');
//     var requireCompany = currentSelect.attr('data-id');
//     if (requireCompany == 1) {
//         $('#company').parents('.form-group').fadeIn(200);
//     } else {
//         $('#company').parents('.form-group').fadeOut(200);
//         $('#company').val(0);
//     }
//
//     if (+currentSelect.attr('data-project')) {
//         $('#current_project_id').attr('data-id', +currentSelect.attr('data-project'));
//         $('#sub_project_id').parents('.form-group').fadeIn(200);
//     } else {
//         $('#sub_project_id').parents('.form-group').fadeOut(200);
//         $('#sub_project_id').select2("val", "");
//         $('#current_project_id').attr('data-id',0);
//     }
//
//     //показываем/скрываем ранг для роли
//     var role = $(this).val();
//     var ranks = $('#rank');
//     var ranksOptions = ranks.find('option[data-id="' + role + '"]');
//     var ranksOptionsHidden = ranks.find('option[data-id!="' + role + '"]');
//     if (ranksOptions.length) {
//         ranks.parents('.form-group').fadeIn(200);
//         ranksOptions.each(function () {
//             $(this).css('display', 'block');
//         });
//     } else {
//         ranks.parents('.form-group').fadeOut(200);
//         ranks.val(0);
//     }
//     ranksOptionsHidden.each(function () {
//         $(this).css('display', 'none');
//     });
// }

function slideForm() {
    var ul = $('#block_sip');
    ul.slideToggle(400);
    return false;
}

function deleteIp(e) {
    $(e.currentTarget).parent().remove();
}

function addNewInput(e) {
    var html = '<div> ' +
        '<input type="text" class="form-control ip_user" placeholder="Ведите новый IP" ip_id="" style="margin-bottom: 10px;display: inline-block;width: 88%">' +
        '<label class="btn btn-primary delete_ip" style="margin-left: 5px"> X </label> ' +
        '</div>';
    $(e.currentTarget).before(html);
    $('.delete_ip').on('click', deleteIp);
}

function generate()
{
    var ints = [0,1,2,3,4,5,6,7,8,9];
    var chars = ['a','b','c','d','e','f','g','h','j','k','l','m','n','o','p','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','J','K','L','M','N','O','P','R','S','T','U','V','W','X','Y','Z'];
    var pas = '';
    var len = 30;
    for(var i=0;i<len;i++){
        var rand = Math.ceil(Math.random() * 100);
        if (rand < chars.length ) {
            pas +=chars[rand];
        } else {
            rand = Math.ceil(Math.random() * 10);
            if (rand < ints.length) {
                pas += ints[rand];
            } else {
                pas += chars[rand];
            }
        }
    }
    return pas;
}

function changeDataSip() {
    var data = {
        login_sip : $('#login_sip').val(),
        password_sip : $('#password_sip').val(),
        nat : $('#nat').prop('checked') ? 1 :0,
        ips: []
    };
    var ips = [];
    $('.ip_user').each(function (i, v) {
        ips.push($(v).val());
    });
    data.ips = ips;
    getMessage('wait', 'Обработка');
    if ($('#create_sip').length) {
        $.post('/create-account-elastix-ajax/' + window.id, data, function (json) {
            $('#data_sip').find('.has-error').removeClass('has-error');
            if (json.errors) {
                for (key in json.errors) {
                    $('#' + key).parents('.form-group').addClass('has-error');
                    if (key == 'ips') {
                        $('#ip_label').parents('.form-group').addClass('has-error');
                    }
                }
                getMessage('error', 'Ошибка');
            }
            if (json.status) {
                getMessage('success', 'Аккаунт создан');
            }
        })
    } else {
        $.post('/change-account-elastix-ajax/' + window.id, data, function (json) {
            $('#data_sip').find('.has-error').removeClass('has-error');
            if (json.errors) {
                for (key in json.errors) {
                    $('#' + key).parents('.form-group').addClass('has-error');
                    if (key == 'ips') {
                        $('#ip_label').parents('.form-group').addClass('has-error');
                    }
                }
                getMessage('error', 'Ошибка');
            }
            if (json.status) {
                getMessage('success', 'Аккаунт изменен ');
            }
        })
    }
    return false;
}

/**
 * Выводим сообщение
 */
function getMessage(type, message) {
    $('.ns-box').remove();
    if (type === 'wait') {
        var notification = new NotificationFx({
            message: '<span class="fa fa-spinner fa-2x alert_spinner"></span><p>' + message + '</p>',
            layout: 'bar',
            effect: 'slidetop',
            type: 'notice',
            ttl: 60000,
        });
    } else {
        var notification = new NotificationFx({
            message: '<span class="icon fa fa-bullhorn fa-2x"></span><p>' + message + '</p>',
            layout: 'bar',
            effect: 'slidetop',
            type: type,
            ttl: 3000,
        });
    }
    notification.show();
}
