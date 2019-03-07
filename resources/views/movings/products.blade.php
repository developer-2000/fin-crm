@if ($products->isNotEmpty())

    <div class="row" id="product_id_papa" style="padding-top:12px;">
        <div class="col-sm-12">
            <div class="input-group" style="width:100%;">
            <span class="input-group-addon ">
                <i class="fa fa-plus"></i>
            </span>
                <select id="product_id" name="product_id" style="width:100%;"
                        data-url="{{ route('moving-plus-product') }}">
                    <option value="0">- Выберите продукт -</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->title }}
                            @if ($product->amount || $product->hold)
                                (max: {{ (int) $product->amount }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

@endif
