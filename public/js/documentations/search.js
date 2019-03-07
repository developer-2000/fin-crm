function search(page) {
  setLoader('div_table');
  $.ajax({
    type: "POST",
    url: "/documentations/search",
    async: false,
    data: ({
      page: page,

      name: $('#name').val(),
      category: $('#category').val(),
    }),
    success: function(msg) {
      $('#div_table').html(msg)
    },
    error: function(jqXHR, textStatus, errorThrown) {
      alert('Server error. Please refresh page');
    },
  })
}

$('#name').on('keyup', function(e) {
  if (e.keyCode === 13) {
    search(1);
  }
});

$("#category").change(function() {
  search(1);
});

// $(document).on('click', '.delete-link', function ( event ) {

$('.delete-link').editable({
  escape: true,
  title: 'Do you shore want to delete this documentation?',
  tpl: null,
  type: 'text',
  emptytext: '',
  success: function(data) {
    let tr = $(this).closest('tr')[0];
    $.ajax({
      type: 'get',
      url: $(this).attr('data-url'),
      dataType: 'json'
    }).done(function(response) {
      if (response.success) {
        tr.remove();
        showMessage('success', 'Documentation successfully deleted');
      } else
        showMessage('error', response.message);
    }).fail(function() {
      showMessage('error', 'Error occurs when trying to delete documentation!');
    });
    return true;
  }

});
// });
