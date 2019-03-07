<div class="row product_papa" style="padding-top:12px;" data-id="{{ $product->id }}">
    <div class="col-sm-12">
        <div class="input-group" style="width:100%;">
            <span class="input-group-addon for_id">
                id: {{ $product->id }}
                <input name="product_list_id" value="{{ $product->id }}" type="hidden" />
            </span>
            <span class="input-group-addon for_title">
                {{ $product->title }}
            </span>

            {{--{{json_encode($product)}}--}}
            <input name="product_list_amount" class="form-control" value="<?php if (isset($product->takenamount)){ print ((int)$product->takenamount ?? 0); } ?>" type="number"
            <?php
                if (isset($product->amount)){
                print (($product->amount) ? ('max="' . $product->amount . '"') : '');
                }
                else{ print ''; }
            ?>
            />

            <span class="input-group-addon for_amount">
                <?php
                if (isset($product->amount)){
                    print 'max: ' . ( ($product->amount) ? (int) $product->amount : '&#8734;');
                }
                else{ print 'max: &#8734;'; }
                ?>
            </span>

            <div class="input-group-btn">
                <button class="btn btn-primary product_minus" data-url="{{ route('moving-minus-product') }}" data-id="{{ $product->id }}">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>