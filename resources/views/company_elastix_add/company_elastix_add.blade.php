@extends('layouts.app')

@section('title') @lang('general.add') @stop

@section('css')
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/campaigns/company_elastix_add.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span> @lang('companies.companies-by-autocall')</span></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                </div>
                <div class='main-box-body clearfix'>
                    <form id="form" role="form" method="post" action="http://deincontactservice.com/company_elastix_add_Ajax" onsubmit="return false">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label for="name" id="label-name">Название</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="checkbox checkbox-nice">
                                <input type="checkbox" id="status" checked="checked" name="status"/>
                                <label for="status">
                                    @lang('general.on-off')
                                </label>
                            </div>
                            <div class="checkbox checkbox-nice">
                                <input type="checkbox" id="learning" name="learning"/>
                                <label for="learning">
                                    @lang('companies.queue-learning')
                                    {{-- Очередь для обучения --}}
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="min_call_count"> @lang('companies.minimal-calls-queue')</label>
                                {{-- Минимальное кол-во звонков(для учебной очереди) --}}
                                <input type="text" id="min_call_count" name="min_call_count" class="form-control">
                            </div>
                            <div class="form-group">
                                <label id="label-time"> @lang('companies.interval-time')</label>
                                <input type="text" class="form-control time" id="call-time"   placeholder="1">
                                <div id="inputs">
                                </div>
                                <div class="btn-group" data-toggle="buttons" style="margin-top: 10px;" id="add_call_time">
                                    <label class="btn btn-primary">
                                        <input type="checkbox"> @lang('general.add')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group form-group-select2">
                            <label id="label_country" class="label_"> @lang('general.country') </label>
                            <select style="width:300px;display: inline-block" id="sel-country" name="country" class="form-control">
                                <option value=""></option>
                                @foreach($countries as $country)
                                    <option value="{{$country->id}}"> @lang('countries.' . $country->code)</option>
                                @endforeach
                            </select>
                            <div class="checkbox checkbox-nice" style="display: inline-block;margin-left: 5px;">
                                <input class="include" type="checkbox" id="incl_country" checked="checked" name="incl_country"/>
                                <label for="incl_country">
                                    @lang('general.on')
                                </label>
                            </div>
                            <div class="btn-group" data-toggle="buttons" style="margin:0 0 4px 10px;" id="add_call_time">
                                <label class="btn btn-primary add-something">
                                    <input type="checkbox" > @lang('general.add')
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group form-group-select2">
                            <label id="label_source" class="label_"> @lang('general.source') </label>
                            <select style="width:300px;display: inline-block" id="sel-source" name="source" class="form-control">
                                <option value=""></option>
                                @foreach($source as $s)
                                    <option value="{{$s->id}}">{{$s->name}}</option>
                                @endforeach
                            </select>
                            <div class="checkbox checkbox-nice" style="display: inline-block;margin-left: 5px;">
                                <input class="include" type="checkbox" id="incl_source" checked="checked" name="incl_source"/>
                                <label for="incl_source">
                                    @lang('general.on')
                                </label>
                            </div>
                            <div class="btn-group" data-toggle="buttons" style="margin:0 0 4px 10px;" id="add_call_time">
                                <label class="btn btn-primary add-something">
                                    <input type="checkbox"> @lang('general.add')
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group form-group-select2">
                            <label id="label_offer" class="label_"> @lang('general.offer')</label>
                            <select style="width:300px;display: inline-block" id="sel-offer" class="form-control">
                                <option value=""></option>
                                @foreach($offers as $offer)
                                    <option value="{{$offer->id}}">{{$offer->name}}</option>
                                @endforeach
                            </select>
                            <div class="checkbox checkbox-nice" style="display: inline-block;margin-left: 5px;">
                                <input class="include" type="checkbox" id="incl_offer" checked="checked" name="incl_offer"/>
                                <label for="incl_offer">
                                    @lang('general.on')
                                </label>
                            </div>
                            <div class="btn-group" data-toggle="buttons" style="margin:0 0 4px 10px;" id="add_call_time">
                                <label class="btn btn-primary add-something">
                                    <input type="checkbox"> @lang('general.add')
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-2 ">
                            <button type="submit" class="btn btn-success" id="submit"> @lang('general.add')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
