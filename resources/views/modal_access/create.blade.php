<link rel="stylesheet" type="text/css" href="/css/select2.css" />

<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Set access control</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tabs-wrapper">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-open-access" data-toggle="tab" aria-expanded="true">Open Access</a></li>
            <li class=""><a href="#tab-close-access" data-toggle="tab" aria-expanded="false">Close Access</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade active in" type="open" id="tab-open-access">

              <div class="row">
                <p>
                  <strong>Here you can make settings for open access.</strong>
                </p>
                <p>If you need more than one setting, you can add a setting.
                  <span>
                <button onclick="addRule('open')" type="button" class="btn btn-success">
                  <span class="fa fa-plus"></span>
                  </button>
                  </span>
                </p>
              </div>

              <div class="main-box-body clearfix">
                <div class="panel-group accordion" id="accordion-open">
                  {!! view('modal_access.panel',['type' => 'open', 'number' => 1]) !!}
                </div>
              </div>

            </div>
            <div class="tab-pane fade" type="open" id="tab-close-access">
              <div class="row">
                <p>
                  <strong>Here you can make settings for close access.</strong>
                </p>
                <p>If you need more than one setting, you can add a setting.
                  <span>
                <button type="button" onclick="addRule('close')" class="btn btn-success">
                  <span class="fa fa-plus"></span>
                  </button>
                  </span>
                </p>
              </div>

              <div class="main-box-body clearfix">
                <div class="panel-group accordion" id="accordion-close">
                  {!! view('modal_access.panel',['type' => 'close', 'number' => 1]) !!}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="serializeRule();">Save changes</button>
        <button type="button" class="btn btn-primary" onclick="storeAccess('test','27');">Store</button>
      </div>
    </div>
  </div>
</div>

<script src="/js/vendor/select2.min.js"></script>
<script type="text/javascript">
  function addRule(type) {
    count = $('.accord-access-' + type).length;
    $.ajax({
      type: 'post',
      url: '/access/rule/add',
      dataType: 'json',
      data: ({
        type: type,
        count: count
      }),
    }).done(function(response) {
      $('#accordion-' + type).append(response.html);
    }).fail(function() {
      alert('Request error');
    });
  }
</script>
<script src="/js/modal_access.js"></script>
