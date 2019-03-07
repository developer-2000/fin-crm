var arrayAccess;

function serializeRule() {
  arrayAccess = $.map($('.panel-collapse'), function(el, i) {
    var inp = $('input.mySel2', el);

    return ['project_id', 'subproject_id', 'company_id', 'role_id', 'rank_id', 'user_id'].reduce(
      function(obj, key, indx) {
      obj[key] = inp[indx].value.split(',');
      return obj
    }, {})
  });
  console.log(arrayAccess);
  return arrayAccess;
}

function storeAccess(entity, entityId) {
  $.ajax({
    type: 'post',
    url: '/access/store',
    dataType: 'json',
    data: ({
      access: arrayAccess,
      entity: entity,
      entity_id: entityId
    }),
  }).done(function(response) {
    if(response.success){
      return true;
    }
    return false;
  }).fail(function() {
    return false;
  });
}
