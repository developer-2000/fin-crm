function save() {
  if (check()) {
    $.ajax({
      type: 'post',
      url: '/documentations/store',
      dataType: 'json',
      data: ({
        name: $('#name').val(),
        category_id: $('#category').val(),
        text: CKEDITOR.instances.ckeditor.getData(),
      }),
    }).done(function(response) {
      if (response.success) {
        if (Dropzone.forElement(".dropzone").getQueuedFiles().length > 0) {
        uploadFile('documentation', response.entity.id);
      }
      else{
        showMessage('success', "All information was successfully saved");
        setTimeout(window.location = "/documentations", 3000);
      }
      }else{
        showMessage('error', response.message);
      }
    }).fail(function() {
      showMessage('error', 'Request error');
    });
  }
}

function uploadFile(entity, entity_id) {
  var myDropzone = Dropzone.forElement(".dropzone");
  myDropzone.on('sending', function(data, xhr, formData) {
    formData.append('entity', entity);
    formData.append('entity_id', entity_id);
  });
  myDropzone.processQueue();

  myDropzone.on("complete", function (file) {
    if (myDropzone.getUploadingFiles().length === 0 && myDropzone.getQueuedFiles().length === 0) {
          showMessage('success', "All information was successfully saved");
          setTimeout(window.location = "/documentations", 3000);
        }else{
          return true;
        }
      });
}

function check() {
  if ($('#name').val() == '') {
    showMessage('error', 'Please enter name');
    return false;
  }
  if ($('#category').val() == '') {
    showMessage('error', 'Please select category');
    return false;
  }
  if (CKEDITOR.instances.ckeditor.getData() == '') {
    showMessage('error', 'Please enter text');
    return false;
  }
  // if (Dropzone.forElement(".dropzone").getQueuedFiles().length < 1) {
  //   showMessage('error', 'Please select file or Drag & Drop file upload');
  //   return false;
  // }

  return true;
}
