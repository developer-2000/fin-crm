@php
    $data = [
        0 => ['url' => 'moving-store',      'fa' => 'road',             'word' => 'create'],
        1 => ['url' => 'moving-move',       'fa' => 'truck',            'word' => 'send'],
        2 => ['url' => 'moving-arrived',    'fa' => 'flag-checkered',   'word' => 'check', 'type' => 'arrived'],
    ];
@endphp
<div class="row" id="button_papa" style="padding-top:12px;">
    <div class="col-sm-12">
        <div class="form-controll">
            <button class="btn btn-primary" id="button" style="width:100%;" data-type="{!! $data[$type]['type'] ?? '' !!}"
                    data-url="{{ route($data[$type]['url']) }}">
                <i class="fa fa-{!! $data[$type]['fa'] !!}"></i>
                @lang('movings.' . $data[$type]['word'])
            </button>
        </div>
    </div>
</div>
