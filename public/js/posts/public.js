var category;

function setCategory(id) {
  category = id;
  search(1);
}

function search(page) {
  setLoader('newsfeed');
  $.ajax({
    type: 'post',
    url: '/posts/public/search',
    data: ({
      page: page,

      title: $('#search').val(),
      category: category,
    }),
  }).done(function(html) {
    $('#newsfeed').html(html);
  }).fail(function() {
    showMessage('error', 'Request error');
  });
}

$('#search').on('keyup', function(e) {
  if (e.keyCode === 13) {
    search(1);
  }
});
