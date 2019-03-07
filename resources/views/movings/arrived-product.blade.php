<div class="row product_papa" style="padding-top:12px;" data-id="{{ $product->id }}">
    <div class="col-sm-12">
        <div class="input-group" style="width:100%;">
            <?php /*
            <span class="input-group-addon for_id">
                id: {{ $product->id }}
                <input name="product_list_id" value="{{ $product->id }}" type="hidden" />
            </span>
            */ ?>
            <span class="input-group-addon for_title2">
                {{ $product->title }}
                <input name="product_list_id" value="{{ $product->id }}" type="hidden" />
            </span>
            <span class="input-group-addon for_amount">
                <i class="fa fa-truck" title=" @lang('warehouses.should-arrive')"></i> {{ $product->amount }}
            </span>
            <span class="input-group-addon for_arrived">
                <i class="fa fa-check-square-o" title=" @lang('warehouses.arrived-confirmed-earlier')"></i> {{ $product->arrived }}
            </span>
            <input name="product_list_arrived" class="form-control" value=""
                   placeholder="+ @lang('warehouses.arrived')" autocomplete="off"
                   type="number" max="{{ $product->amount - $product->arrived - $product->shortfall }}" min="0"
                    {!! ($product->amount <= ($product->arrived + $product->shortfall)) ? 'disabled="true"' : '' !!}
                    />
            <span class="input-group-addon for_shortfall">
                <i class="fa fa-thumbs-down" title=" @lang('warehouses.shortage-earlier')"></i> {{ $product->shortfall }}
            </span>
            <input name="product_list_shortfall" class="form-control" value=""
                   placeholder="+ @lang('warehouses.shortfall')" autocomplete="off"
                   type="number" max="{{ $product->amount - $product->arrived - $product->shortfall }}" min="0"
                    {!! ($product->amount <= ($product->arrived + $product->shortfall)) ? 'disabled="true"' : '' !!}
                    />
        </div>
    </div>
</div>