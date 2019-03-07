<br>
<div class="panel panel-default accord-access-{{ $type }}">
  <div class="panel-heading">
    <h4 class="panel-title">
<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#{{ $type }}-collapse-{{ $number }}">
Rule for {{ $type}} access #{{ $number }}
</a>
</h4>
  </div>
  <div id="{{ $type }}-collapse-{{ $number }}" class="panel-collapse collapse" aria-expanded="false">
    <div class="panel-body">
      <div class="row">
        <div class="form-group form-group-select2">
          <label>Select projects</label>
          <input style="width:100%" data-route="projects" name="projects[]" class="mySel2" id="{{ $type }}_project_{{ $number }}" multiple>
          </input>
        </div>
      </div>

      <div class="row">
        <div class="form-group form-group-select2">
          <label>Select subrojects</label>
          <input style="width:100%" data-route="sub_projects" name="sub_projects[]" class="mySel2" id="{{ $type }}_subproject_{{ $number }}" multiple>
          </input>
        </div>
      </div>

      <div class="row">
        <div class="form-group form-group-select2">
          <label>Select companies</label>
          <input style="width:100%" data-route="company" name="company[]" class="mySel2" id="{{ $type }}_company_{{ $number }}" multiple>

          </input>
        </div>
      </div>

      <div class="row">
        <div class="form-group form-group-select2">
          <label>Select roles</label>
          <input style="width:100%" data-route="roles" name="roles[]" class="mySel2" id="{{ $type }}_role_{{ $number }}" multiple>
          </input>
        </div>
      </div>

      <div class="row">
        <div class="form-group form-group-select2">
          <label>Select ranks</label>
          <input style="width:100%" data-route="ranks" name="ranks[]" class="mySel2" id="{{ $type }}_rank_{{ $number }}" multiple>
          </input>
        </div>
      </div>

      <div class="row">
        <div class="form-group form-group-select2">
          <label>Select users</label>
          <input style="width:100%" data-route="user" name="users[]" class="mySel2" id="{{ $type }}_user_{{ $number }}" multiple>
          </input>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

$(document).ready(function() {
  $("#{{ $type }}-collapse-{{ $number }} .mySel2").each(function() {
    var route = $(this).attr('data-route');
    $(this).select2({
      placeholder: "",
      multiple: true,
      minimumInputLength: 0,
      ajax: {
        type: "POST",
        url: '/' + route + '/find',
        dataType: 'json',
        data: function(params) {
          return {
            query: $.trim(params),
            project_id: $('#{{ $type }}_project_{{ $number }}').val().split(',')
          };
        },
        results: function(data) {
          return {
            results: data,
            "pagination": {
              "more": true
            }
          };
        }
      },
    });
  });
});
</script>
