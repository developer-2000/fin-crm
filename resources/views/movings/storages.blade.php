@if (($forma == 'subproj') && auth()->user()->sub_project_id)

    <input id="subproj" type="hidden" name="subproj"
           data-url="{{ route('moving-get-products') }}" value="{{ auth()->user()->sub_project_id }}" />
    <h3 class="h3-create-moving">
        <i class="fa fa-cubes"></i>
        {{ $subproj[auth()->user()->sub_project_id] }}
    </h3>
@elseif(($forma == 'subproj') && !auth()->user()->sub_project_id)
    {!! Form::label($forma, $forma == 'my_storage' ? 'Подпроекты' : 'Подпроекты', ['class' => 'storage-label']) !!}

    {!! Form::select(
        $forma,
        $subproj,
        null,
        [
            'placeholder' => '--' . trans('general.select') . '--',
            'id' => $forma,
            'class' => 'storage',
            'style' => 'width:100%;',
            'data-url' => route('moving-get-products')
        ]
    ) !!}
@elseif($forma == 'my_storage')
        {!! Form::label($forma, $forma == 'my_storage' ? 'Мои склады' : 'Мои склады', ['class' => 'storage-label']) !!}

        {!! Form::select(
            $forma,
            $storages,
            null,
            [
                'placeholder' => '--' . trans('general.select') . '--',
                'id' => $forma,
                'class' => 'storage',
                'style' => 'width:100%;',
                'data-url' => route('moving-get-products')
            ]
        ) !!}
@else
    {!! Form::label($forma, $forma == 'sender_id' ? 'Перевод на склады' : 'Перевод на склады', ['class' => 'storage-label']) !!}

    {!! Form::select(
        $forma,
        $storages,
        null,
        [
            'placeholder' => '--' . trans('general.select') . '--',
            'id' => $forma,
            'class' => 'storage',
            'style' => 'width:100%;',
            'data-url' => route('moving-get-products')
        ]
    ) !!}
@endif
