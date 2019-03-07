<div class="row" id="product_id_papa" style="padding-top:12px;">
    <div class="col-sm-12 form-group form-group-select2">
        <div class="input-group" style="width:100%;">
            <span class="input-group-addon ">
                <i class="fa fa-plus"></i>
            </span>
            <input id="product_id" name="product_id"
                   data-url="{{ route('moving-plus-product') }}"
                   data-url_p="{{ route('moving-get-products-list') }}"
                   value="" placeholder="--@lang('general.select-product')--" style="width:100%;" />

        </div>
    </div>
</div>
