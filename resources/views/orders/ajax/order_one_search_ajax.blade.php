@if ($data->isNotEmpty())
    <table class="table table">
        <tbody>
            @foreach ($data as $d)
                <tr>
                    <td>
                        {{ $d->title }}
                        @if (!$d->storage)
                            <br>
                        <span class="label label-danger">(@lang('orders.not-in-stock'))</span>
                        @endif
                    </td>
                    @if ($d->storage)
                        <td class="text-center">
                        <input type="text" style="width: 70px; display: inline-block;" class="form-control price_offer_add" placeholder="Цена">
                            @if(isset($currency))
                                (<span class="offer_currency">{{ $currency }}</span>)
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="" class="table-link">
                                <span class="fa-stack add_product" data-id="{{ $d->id }}">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-plus-square fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </td>
                    @else
                        <td colspan="2"></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <header class="main-box-header clearfix">
        <h2> @lang('orders.search-not-results')</h2>
    </header>
@endif  