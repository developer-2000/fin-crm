$(function () {

    $('#generate_pas_two').on('click', function () {
        $('#password_sip').val(generate());
    });
    $('#generate_pas').on('click', function () {
        $('#password').val(generate());
    });
    $('#data_crm').on('submit', changeDataUser);
    $('#data_sip').on('submit', changeDataSip);
    $('#create_sip').on('click', slideForm);

    $('#password').pwstrength();
    $("#birthday").mask("99/99/9999");


    $('.delete_ip').on('click', deleteIp);
    $('#new_input').on('click', addNewInput);

    $('#role').on('change', getRole);

    $('#sub_project_id').select2({
        ajax: {
            url: '/sub_projects/find',
            dataType: 'json',
            data: function (params) {
                return {
                    query: $.trim(params),
                    project_id : $('#current_project_id').attr('data-id')
                };
            },
            results: function (data) {
                return {
                    results: data,
                };
            }
        },
        width : '100%'
    });

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

    $('#time_zone').select2({width : '100%'});
});

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

function getRole() {
    var currentSelect = $(this).find('option:selected');
    var requireCompany = currentSelect.attr('data-id');
    if (requireCompany == 1) {
        $('#company').parents('.form-group').fadeIn(200);
    } else {
        $('#company').parents('.form-group').fadeOut(200);
        $('#company').val(0);
    }

    if (+currentSelect.attr('data-project')) {
        $('#current_project_id').attr('data-id', +currentSelect.attr('data-project'));
        $('#sub_project_id').parents('.form-group').fadeIn(200);
    } else {
        $('#sub_project_id').parents('.form-group').fadeOut(200);
        $('#sub_project_id').select2("val", "");
        $('#current_project_id').attr('data-id',0);
    }

    //показываем/скрываем ранг для роли
    var role = $(this).val();
    var ranks = $('#rank');
    var ranksOptions = ranks.find('option[data-id="' + role + '"]');
    var ranksOptionsHidden = ranks.find('option[data-id!="' + role + '"]');
    if (ranksOptions.length) {
        ranks.parents('.form-group').fadeIn(200);
        ranksOptions.each(function () {
            $(this).css('display', 'block');
        });
    } else {
        ranks.parents('.form-group').fadeOut(200);
        ranks.val(0);
    }
    ranksOptionsHidden.each(function () {
        $(this).css('display', 'none');
    });
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
    var delegatedPermisisions = [];
    $('input[name="delegated_permissions[]"]').each(function(e, value){
        if($(value).is(':checked')){
            delegatedPermisisions.push(Number($(value).val()));
        }
    });
    form_data.append('user_permissions', delegatedPermisisions);

    $.ajax({
        type : 'post',
        url : '/registration-data-users-ajax',
        data : form_data ,
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
                getMessage('success', 'Аккаунт создан');
                window.id = json.status;
                $('#block_sip').parent().fadeIn(400);
            }
        }
    });
    return false;
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
            $('#block_sip').parent().fadeOut(400);
            $('#data_crm')[0].reset();
            $('#data_sip')[0].reset();
            getMessage('success', 'Аккаунт создан');
        }
    });
    return false;
}

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