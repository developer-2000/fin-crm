@extends('layouts.app')

@section('title')Успешные звонки@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('feedbacks.success-calls')</span></li>
            </ol>
        </div>
    </div>
    <div class="clearfix">
        <h1 class="pull-left"> @lang('feedbacks.success-calls')</h1>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{$_SERVER['REQUEST_URI'] }}"
                      method="post">
                    <div class="main-box">
                        <div class="item_rows ">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="user" class="col-sm-4 control-label"> @lang('general.operator')</label>
                                        <div class="col-sm-8">
                                            <select id="user" name="user[]" style="width: 100%" multiple>
                                                @if ($users)
                                                    @foreach ($users as $user)
                                                        <option
                                                                @if (isset($_GET['user']))
                                                                <? $usersGet = explode(',', $_GET['user']); ?>
                                                                @foreach ($usersGet as $usg)
                                                                @if ($user->id == $usg)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{ $user->id }}">{{ $user->name . ' ' . $user->surname }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    @if(!empty($moderators))
                                    <!--<div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="user" class="col-sm-4 control-label">Модератор</label>
                                        <div class="col-sm-8">
                                            <select id="moderator" name="moderator[]" style="width: 100%" multiple>
                                                {{--@if ($moderators)--}}
                                                    {{--@foreach ($moderators as $moderator)--}}
                                                        {{--<option--}}
                                                                {{--@if (isset($_GET['moderator']))--}}
                                                                {{--<? $moderatorsGet = explode(',', $_GET['moderator']); ?>--}}
                                                                {{--@foreach ($moderatorsGet as $usg)--}}
                                                                {{--@if ($moderator->id == $usg)--}}
                                                                {{--selected--}}
                                                                {{--@endif--}}
                                                                {{--@endforeach--}}
                                                                {{--@endif--}}
                                                                {{--value="{{ $moderator->id }}">{{ $moderator->name . ' ' . $moderator->surname }}</option>--}}
                                                    {{--@endforeach--}}
                                                {{--@endif--}}
                                            </select>
                                        </div>
                                    </div>-->
                                    @endif
                                @if(!empty($offers))
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="offers" class="col-sm-4 control-label"> @lang('general.offer')</label>
                                        <div class="col-sm-8">
                                            <select id="offers" name="offers[]" style="width: 100%" multiple>
                                                @if ($offers)
                                                    @foreach ($offers as $offer)
                                                        <option
                                                                @if (isset($_GET['offer']))
                                                                <? $offersGet = explode(',', $_GET['$offer']); ?>
                                                                @foreach ($offersGet as $getOffer)
                                                                @if ($offer->id == $getOffer)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{ $offer->id }}">{{ $offer->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                    @if(!empty($products))
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="offers" class="col-sm-4 control-label"> @lang('general.offer')</label>
                                        <div class="col-sm-8">
                                            <select id="products" name="products[]" style="width: 100%" multiple>
                                                @if ($products)
                                                    @foreach ($products as $product)
                                                        <option
                                                                @if (isset($_GET['product']))
                                                                <? $offersGet = explode(',', $_GET['$offer']); ?>
                                                                @foreach ($offersGet as $getOffer)
                                                                @if ($offer->id == $getOffer)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{ $offer->id }}">{{ $offer->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{--<div class='main-box-body clearfix section_filter'>--}}
                            {{--<div class='main-box-body clearfix'>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;">Дата</div>--}}
                            {{--<div class="col-sm-2">--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="date_start">С</label>--}}
                                    {{--<div class="input-group">--}}
                                        {{--<span class="input-group-addon"><i class="fa fa-calendar"></i></span>--}}
                                        {{--<input class="form-control" id="date_start" type="text" data-toggle="tooltip"--}}
                                               {{--name="date_start"--}}
                                               {{--data-placement="bottom"--}}
                                               {{--value="{{ isset($_GET['date_start']) ? date('d.m.Y', $_GET['date_start']) : '' }}">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-2">--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="date_end">До</label>--}}
                                    {{--<div class="input-group">--}}
                                        {{--<span class="input-group-addon"><i class="fa fa-calendar"></i></span>--}}
                                        {{--<input class="form-control" id="date_end" type="text" data-toggle="tooltip"--}}
                                               {{--name="date_end"--}}
                                               {{--data-placement="bottom"--}}
                                               {{--value="{{ isset($_GET['date_end']) ? date('d.m.Y', $_GET['date_end']) : '' }}">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">--}}
                                {{--<div class="input-group" style="padding-top: 7px">--}}
                                    {{--<div class="btn-group" data-toggle="buttons" id="date_template">--}}
                                        {{--<label class="btn btn-default pattern_date">--}}
                                            {{--<input type="radio" name="date_template" value="1"> Сегодня--}}
                                        {{--</label>--}}
                                        {{--<label class="btn btn-default pattern_date">--}}
                                            {{--<input type="radio" name="date_template" value="5"> Вчера--}}
                                        {{--</label>--}}
                                        {{--<label class="btn btn-default pattern_date">--}}
                                            {{--<input type="radio" name="date_template" value="9"> Неделя--}}
                                        {{--</label>--}}
                                        {{--<label class="btn btn-default pattern_date">--}}
                                            {{--<input type="radio" name="date_template" value="10"> Месяц--}}
                                        {{--</label>--}}
                                        {{--<label class="btn btn-default pattern_date">--}}
                                            {{--<input type="radio" name="date_template" value="2"> Прошлый месяц--}}
                                        {{--</label>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                        <a href="{{ route('success-calls') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        @if ($feedbacks)
                            <div class="table-responsive">
                                <table id="orders" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center"> @lang('general.id')</th>
                                        <th class="text-center"> @lang('general.product')</th>
                                        <th class="text-center"> @lang('general.operator')</th>
                                        <th class="text-center"> @lang('general.calls')</th>
                                        {{--<th class="text-center">Наставник</th>--}}
                                        <th class="text-center"> @lang('general.comments')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($feedbacks as $feedback)
                                        <tr>
                                            <td class="text-center" style="width: 20%">
                                                {{!empty($feedback->offer->name) ? $feedback->offer->name : 'N/A'}}
                                            </td>
                                            <td class="text-center" style="width: 15%">
                                              @if(!empty($feedback->products))
                                                  @foreach($feedback->products as $product)
                                                      {{ \App\Models\Product::find($product->product_id)->title}}<br>
                                                      @endforeach
                                                  @endif
                                            </td>
                                            <td class="text-center" style="width: 10%;">
                                                {{$feedback->user->name. '  ' .$feedback->user->surname}}
                                            </td>
                                            <td style="width: 20%;">
                                                @if(!empty($feedback->record->file))
                                                    <div>
                                                        <?
                                                        $url = route('get-call-by-name') . '?fileName=' . $feedback->record->file;
                                                        $agent = $_SERVER['HTTP_USER_AGENT'];
                                                        if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                                            $output = '<p><a href="' . $url . '"><span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                            </span></a></p>';
                                                        } else {
                                                            $output = '
                                            <audio controls>
                                                <source src="' . $url . '" type="audio/mpeg">
                                            </audio>
                                    ';
                                                        }
                                                        echo $output?>
                                                    </div>
                                                @endif
                                            </td>
                                            {{--<td class="text-center">--}}
                                            {{--{{'Mentor'}}--}}
                                            {{--</td>--}}
                                            <td class="text-center">{{!empty($feedback->comments->first()->text) ? $feedback->comments->first()->text : '' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
                {{$feedbacks->links()}}
            </div>
        </div>
    </div>
@stop
