@if (auth()->user()->sub_project_id)
    <input id="storage_id" type="hidden" name="storage_id"
           data-url="{{ route('transaction-get-products') }}" value="{{ auth()->user()->sub_project_id }}" />
    <h3 class="h3-create-moving">
        <i class="fa fa-cubes"></i>
        {{ $storages[auth()->user()->sub_project_id] }}
    </h3>
@else
    {!! Form::label('storage_id', trans('general.storage'), ['class' => 'storage-label']) !!}

    {!! Form::select(
        'storage_id',
        $storages,
        null,
        [
            'placeholder' => '--' . trans('general.select') . '--',
            'id' => 'storage_id',
            'class' => 'storage',
            'style' => 'width:100%;',
            'data-url' => route('transaction-get-products')
        ]
    ) !!}
@endif
