<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div id="processing">
        <div class="table-responsive">
            <table class="table " id="processing">
                <thead>
                <tr>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="moderator-block-load">
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
      @lang('orders.load-to-callback')
      {{-- Загрузить на  прозвон --}}
    </div>
    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
        <div class="form-group upload-cancel">

            <div class="checkbox-nice">{{ Form::checkbox('addCall', $value, NULL, ['id' => 'add_call', 'class' => 'checkbox-nice']) }}
                {{ Form::label('add_call', $action) }}</div>
        </div>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
        {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id' => 'addCall'])}}
    </div>
</div>
