<tr>
    <td>{{$newProduct->id}}</td>
    <td>{{ !empty($newProduct->project->name) ? $newProduct->project->name : '' }}</td>
    <td><span style="font-weight: bold; color: #077871">{{ config('app.product_types')[$newProduct->type] }}</span>
    </td>
    <td>
        <span style="font-weight: bold; color: #1ABC9C">{{ !empty($newProduct->category->name) ? $newProduct->category->name : '' }}</span>
    </td>
    <td>
        <a href="#" id="product" data-type="text"
           data-pk="{{  $newProduct->id }}"
           data-name="name"
           data-url="/ajax/products/edit-product-title"
           data-id="{{ $newProduct->title }}"
           data-title="{{trans('general.enter-new-name')}}"
           class="editable editable-click product"
           data-original-title=""
           title="">{{ $newProduct->title}}</a>
    </td>
    <td class="text-center">
        <div class="checkbox-nice checkbox">
            @php
                if($newProduct->status == 'on'){
                $status = true;
                }else{
                    $status = false;}
            @endphp
            {{ Form::checkbox('activity',  $newProduct->id, $status, ['id' => 'activate_'. $newProduct->id, 'class' => 'activate_product']) }}
            {{ Form::label('activate_'. $newProduct->id, ' ') }}
        </div>
    </td>
    @if(isset($permissions['product_edit']))
        <td>
            <a href="{{route('products-edit', $newProduct->id)}}" class="table-link">
                    <span class="fa-stack">
                            <i class="fa fa-square fa-stack-2x"></i>
                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                    </span></a>
        </td>
    @endif
    @if(isset($permissions['product_destroy']))
        <td>
            <a href="#"
               data-type="text"
               data-pk="{{ $newProduct->id }}"
               data-title="{{trans('products.delete')}}"
               data-id="{{ $newProduct->id }}"
               data-url="/ajax/products/destroy"
               class="editable editable-click table-link danger  destroy-product"> @lang('general.delete')</a>
        </td>
    @endif
</tr>
