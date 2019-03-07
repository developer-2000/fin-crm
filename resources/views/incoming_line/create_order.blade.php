@extends('layouts.app')

@section('title') @lang('orders.order-create') @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/incoming_line.css') }}" />
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order_one.js') }}"></script>
    <script src="{{ URL::asset('js/incoming-calls/incoming_call.js') }}"></script>
@stop

@section('content')
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="content-header" class="clearfix">
                            <div class="pull-left">
                                <ol class="breadcrumb">
                                    <li> @lang('orders.incoming-line')</li>
                                    <li><a href="{{route('incoming-call', isset($_GET['phone']) ? $_GET['phone'] : 0)}}">Заказы по номеру</a></li>
                                    <li class="active"><span> @lang('orders.order-create')</span></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-push-4">
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left" style="color: #929292;"> @lang('orders.order-create')</h2>
                    </header>
                    <form action="#" onsubmit="return false" class="create" id="create_order">
                        <div class="main-box-body clearfix">
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ">
                                    <label for="surname"> @lang('general.surname') <span class="required_star">*</span></label>
                                    <input type="text" class="form-control" id="surname" name="surname" data-toggle="tooltip"
                                           data-placement="bottom" title=" @lang('general.surname')" value="" >
                                    <div class="error_messages"></div>
                                </div>
                                <div class="form-group ">
                                    <label for="name"> @lang('general.name') <span class="required_star">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" data-toggle="tooltip"
                                           data-placement="bottom" title=" @lang('general.name')" value="" >
                                    <div class="error_messages"></div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ">
                                    <label for="middle"> @lang('general.middle-name')</label>
                                    <input type="text" class="form-control" id="middle" name="middle" data-toggle="tooltip"
                                           data-placement="bottom" title=" @lang('general.middle-name')" value="">
                                    <div class="error_messages"></div>
                                </div>
                                <div class="form-group ">
                                    <label for="phone"> @lang('general.phone') <span class="required_star">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" data-toggle="tooltip"
                                           data-placement="bottom" title=" @lang('general.phone')" value="{{isset($_GET['phone']) ? $_GET['phone'] : ''}}" >
                                    <div class="error_messages"></div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group form-group-select2">
                                    <label for="country"> @lang('general.country') <span class="required_star">*</span></label>
                                    @if ($countries)
                                        <select name="country" id="country" style="width: 100%">
                                            <option value=""> @lang('general.select-country')</option>
                                            @foreach($countries as $country)
                                                <option value="{{mb_strtolower($country->code)}}">
                                                    @lang('countries.' . $country->code)
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                    <div class="error_messages"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="age"> @lang('general.age')</label>
                                    <input type="text" class="form-control" id="age" name="age" data-toggle="tooltip"
                                           data-placement="bottom" title=" @lang('general.age')" value="">
                                    <div class="error_messages"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="gender"> @lang('general.gender)</label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option value=""> @lang('general.gender')</option>
                                        <option value="1"> @lang('general.male')</option>
                                        <option value="2">> @lang('general.female')</option>
                                    </select>
                                    <div class="error_messages"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 text-right ">
                                <input class="btn btn-success" type="submit" name="submit" value='Сохранить'/>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="main-box clearfix my_disable" style="position:relative">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left" style="color: #929292;">> @lang('general.select-product')</h2>
                        <div class="filter-block pull-right">
                            <div class="form-group pull-left">
                                <input type="text" class="form-control search_product" placeholder="> @lang('general.search')...">
                                <i class="fa fa-search search-icon"></i>
                            </div>
                        </div>
                    </header>
                    <div class="table-responsive search_block">
                    </div>
                    <div class="table-responsive">
                        <table class="table table_products" >
                            <thead>
                            <tr>
                                <th>> @lang('general.name')</th>
                                <th class="text-center"> @lang('general.warehouse')</th>
                                <th class="text-center"> @lang('general.up-sell')</th>
                                <th class="text-center"> @lang('general.up-sell') 2</th>
                                <th class="text-center"> @lang('general.cross-sell')</th>
                                <th class="text-center"> @lang('general.note')</th>
                                <th class="text-center"> @lang('general.price')</th>
                                {{--<th class="text-center">Price+</th>--}}
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    {{--<div class="main-box-body clearfix text-right">--}}
                        {{--<input class="btn btn-success" type="button" name="submit" id="save_changed" value='Сохранить'/>--}}
                    {{--</div>--}}
                </div>
                <div class="result my_disable">
                    <div class="result">
                        <header class="main-box-header clearfix">
                            <h3 style="border-bottom: none;"><span style="border-bottom: none;"> @lang('orders.call-result')</span></h3>
                            <div class="order_target hidden">

                            </div>
                        </header>
                        <div class="tabs-wrapper profile-tabs">
                            <ul class="nav nav-tabs">
                                <li><a href="#approve" data-toggle="tab" class="approve"> @lang('general.approved')</a><span class="close_tab"><i class="fa fa-times"></i></span></li>
                                <li><a href="#failure" data-toggle="tab" class="failure"> @lang('general.refusal')</a><span class="close_tab"><i class="fa fa-times"></i></span></li>
                                <li><a href="#fake" data-toggle="tab" class="fake"> @lang('general.annulled')</a><span class="close_tab"><i class="fa fa-times"></i></span></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active " id="def">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-4 col-md-pull-8 my_disable">
                <div class="main-box-body clearfix">
                    <header class="main-box-header clearfix">
                        <h3 style="border-bottom: none;"><span style="border-bottom: none;"> @lang('orders.order-comment')</span>
                        </h3>
                    </header>
                    <div class="main-box-body clearfix" style="padding: 0">
                        <div class="conversation-wrapper">
                            <div class="conversation-new-message">
                                <form onsubmit="return false;">
                                    <div class="form-group">
                                            <textarea class="form-control field_comment" rows="2"
                                                      placeholder=" @lang('general.comment')..." style="resize:vertical;"></textarea>
                                    </div>
                                    <div class="clearfix text-center">
                                        <button type="submit" class="btn btn-success add_comment ">
                                          @lang('orders.stay-comment')
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="conversation-content ">
                                <div class="conversation-inner">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@stop
