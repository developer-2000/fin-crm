var priority = 'low';

function setPriority(e) {
  priority = e;
}

function save() {
  if (check()) {
    $('#button_save').prop( "disabled", true );
    $.ajax({
      type: 'post',
      url: '/posts/store',
      dataType: 'json',
      data: ({
        title: $('#title').val(),
        body: CKEDITOR.instances.ckeditor.getData(),
        category_id: $('#category_id').val(),
        priority: priority,
        publish_at: $('#date').val(),
        required: $('#familiar').is(":checked")?1:0
      }),
    }).done(function(response) {
      if (response.success) {
        showMessage('success', 'Post created successfully');
        setTimeout(window.location = "/posts/settings", 3000);
      } else {
        $('#button_save').prop( "disabled", false );
        showMessage('error', response.message);
      }
    }).fail(function() {
      showMessage('error', 'Request error');
    });
  }
}

function check() {
  if (!$('#category_id').val()) {
    showMessage('error', "Field category is required!");
    return false;
  }
  if (!$('#title').val()) {
    showMessage('error', "Field title is required!");
    return false;
  }
  if (!CKEDITOR.instances.ckeditor.getData()) {
    showMessage('error', "Please enter text!");
    return false;
  }

  return true;
}

myDatepicker($('#date'));
function myDatepicker(obj) {
    var start = new Date(), prevDay, startHours = 1;
    start.setHours(1);
    start.setMinutes(0);
    obj.datepicker({
        timepicker: true,
        language: 'ru',
        startDate: start,
        minHours: startHours,
        maxHours: 18,
        onSelect: function (fd, d, picker) {
            if (!d) {
                return;
            }
            var day = d.getDay();
            if (prevDay != undefined && prevDay == day) {
                return;
            }
            prevDay = day;
            picker.update({
                minHours: 1,
                maxHours: 23
            });
        }
    });
}
