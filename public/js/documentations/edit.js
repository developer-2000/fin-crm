
function save(){
  $.ajax({
    type: 'post',
    url: '/documentations/update',
    dataType: 'json',
    data: ({
      id: $('#entity_id').val(),
      name: $('#name').val(),
      category_id: $('#category').val(),
      text: CKEDITOR.instances.ckeditor.getData()
    }),
  }).done(function(response) {
    if (response.success) {
        showMessage('success', "All information saved successfully!");
        return false;
    }
    showMessage('error', response.message);
  }).fail(function() {
    showMessage('error', 'Request error');
  });
}

$('#name').keyup(function() {
    delay(function(){
      save()
    }, 1000 );
});

$("#category").change(function() {
  save();
});

function deleteFile(fileId) {
  var result = false;
  $.ajax({
    type: 'post',
    async: false,
    url: '/documentations/file/delete',
    dataType: 'json',
    data: ({
      id: $('#entity_id').val(),
      file_id: fileId
    }),
  }).done(function(response) {
    if (response.success) {
        result = true;
    }
  }).fail(function() {
    result = false;
  });

  return result;
}
