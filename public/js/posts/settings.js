
function search(page) {
  setLoader('table');
  $.ajax({
    type: 'post',
    url: '/posts/search',
    data: ({
      page: page,

      title: $('#title').val(),
      category: $('#category').val(),
    }),
  }).done(function(html) {
    $('#table').html(html);
  }).fail(function() {
    showMessage('error', 'Request error');
  });
}

$('#title').on('keyup', function(e) {
  if (e.keyCode === 13) {
    search(1);
  }
});

$("#category").change(function() {
  search(1);
});

function changeActivity(id) {
  $.ajax({
    type: 'post',
    url: '/posts/active/change',
    data: ({
      id: id
    }),
  }).done(function(response) {
    if(response.success){
      showMessage('success', 'Atcivity changed successfully!');
    }else {
      showMessage('error', 'Error change atcivity!');
    }
  }).fail(function() {
    showMessage('error', 'Request error');
  });
}

$('.delete-link').editable({
  escape: true,
  title: 'Do you shore want to delete this post?',
  tpl: null,
  type: 'text',
  emptytext: '',
  success: function(data) {
    let tr = $(this).closest('tr')[0];
    $.ajax({
      type: 'delete',
      url: $(this).attr('data-url'),
      dataType: 'json'
    }).done(function(response) {
      if (response.success) {
        tr.remove();
        showMessage('success', 'Post successfully deleted');
      } else
        showMessage('error', response.message);
    }).fail(function() {
      showMessage('error', 'Error occurs when trying to delete post!');
    });
    return true;
  }
});
