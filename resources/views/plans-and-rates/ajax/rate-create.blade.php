<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 ">
    @if(!empty($planRate->planRatesOffers))
        <div class="table-responsive">
            <table class="table table-striped table-hover all_lists">
                <thead>
                <tr>
                    <th class="text-center">Оффер ID</th>
                    <th class="text-center">Название</th>
                    <th class="text-center"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($planRate->planRatesOffers as $row)
                    <tr>
                        <td class="text-center">
                            <a href="{{route('offer', $row->offer)}}">
                                {{$row->offer_id}}
                            </a>
                        </td>
                        <td>
                            @if(!empty($row->offer))
                                @php
                                    $prefix = '';
                                if ($row->offer->project_id == 1) {
                                $prefix = 'UM::';
                                } elseif ($row->offer->project_id == 2) {
                                $prefix = 'BM::';
                                }
                                 elseif ($row->offer->project_id == 3) {
                                $prefix = 'HP::';
                                }
                                @endphp
                                <span class="crm_id">{{$prefix . $row->offer->name}}<br></span>
                            @endif
                        </td>
                        <td>
                            <a href="#" class="table-link danger" id="delete_rate">
                                                                    <span class="fa-stack "
                                                                          data-id="{{ json_encode($row) }}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                <div class="hidden planRateId">{{Request::segment(3)}}</div>
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 ">
    @if(!empty($planRate))
        <div class="table-responsive">
            <table class="table table-striped table-hover all_lists">
                <thead>
                <tr>
                    <th class="text-center">Страна</th>
                    <th class="text-center">Ставка</th>
                    <th class="text-center">Ставка upsell</th>
                    <th class="text-center"></th>
                </tr>
                </thead>
                <tbody>
                @foreach(json_decode($planRate->data) as $row)
                    @if(!empty($row->geo))
                        <tr>
                            <td class="text-center">
                                <div class="order_phone_block">
                                    <a href="#" class="pop">
                                                    <span class="order_phone">
                                                      @if(!empty($row->geo))
                                                            <img class="country-flag"
                                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}" />
                                                        @endif
                                                    </span>
                                    </a>
                                    <div class="data_popup">
                                        <div class="arrow"></div>
                                        <h3 class="title">Страна</h3>
                                        <div class="content">{{\App\Models\Country::where('code', $row->geo)->first()->name}}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                {{$row->rate}} %
                            </td>
                            <td class="text-center">
                                {{$row->upsell_rate}} %
                            </td>
                            <td>
                                <a href="#" class="table-link danger" id="delete_rate">
                                                                    <span class="fa-stack "
                                                                          data-id="{{ $planRate->id}}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>