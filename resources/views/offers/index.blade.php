@extends('layouts.app')

@section('title') @lang('offers.all') @stop

@section('css')
    <style>
        .item_rows {
            position: relative;
            padding-top: 15px;
            border-bottom: 1px solid #e6ebef;
        }

        .item_rows:last-of-type {
            border-bottom: none;
        }

        .item_rows .main-box-body {
            /*padding-left: 59px;*/
            padding-bottom: 0;
        }

        .item_rows .form-horizontal .control-label {
            text-align: left;
        }

        .btns_filter {
            text-align: center;
            padding-top: 4px;
            padding-bottom: 20px;
        }

        .btns_filter .btn {
            margin-right: 8px;
            width: 144px;
        }
    </style>
@stop

@section('jsBottom')

@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('offers.all')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('offers.all')</h1>
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{ $_SERVER['REQUEST_URI'] }}"
                      method="post">
                    <div class="main-box">
                        <div class="item_rows ">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6 form-horizontal">
                                        <label for="name" class="col-sm-3 control-label"> @lang('general.name')</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="name" name="name"
                                                   value="@if (isset($_GET['name'])){{ $_GET['name'] }}@endif">
                                        </div>
                                    </div>
                                    @if(isset($permissions['filter_projects_page_offers']))
                                    <div class="form-group col-md-6 col-sm-6 form-horizontal">
                                        <label for="partner" class="col-sm-3 control-label"> @lang('general.partner')</label>
                                        <div class="col-sm-9">
                                            <select id="partner" name="partner" class="form-control"
                                                    style="width: 100%">
                                                <option value="">Все</option>
                                                @foreach ($partners as $partner)
                                                    <option
                                                            @if (isset($_GET['partner']))
                                                            @if ($partner->id == $_GET['partner'])
                                                            selected
                                                            @endif
                                                            @endif
                                                            value="{{ $partner->id }}">{{ $partner->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success" type="submit" name="button_filter" value='Фильтровать'/>
                        <a href="{{ route('offers') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        @if ($data)
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th> @lang('general.id')</th>
                                    <th> @lang('general.partner')</th>
                                    <th> @lang('general.oid')</th>
                                    <th> @lang('general.name')</th>
                                    <th class="text-center">
                                      @lang('general.up-sell')<br>
                                      @lang('general.up-sell') 2 <br>
                                      @lang('general.cross-sell')
                                    </th>
                                    <th class="text-center"> @lang('general.script')</th>
                                    @if (isset($permissions['page_setting_offers']))
                                        <th></th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data as $offer)
                                    @if(auth()->user()->role_id == 1 && !($offer->script))
                                    @else
                                        <tr>
                                            <td>{{ $offer->id }}</td>
                                            <td>{{ $offer->project }}</td>
                                            <td>{{ $offer->offer_id }}</td>
                                            <td>
                                                {{ $offer->name }}
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $up_sell = isset($offer->up_sell) ? $offer->up_sell : 0;
                                                $up_sell_2 = isset($offer->up_sell_2) ? $offer->up_sell_2 : 0;
                                                $cross = isset($offer->cross_sell) ? $offer->cross_sell : 0;
                                                ?>
                                                {{$up_sell}}/{{$up_sell_2}}/{{$cross}}
                                            </td>
                                            <td class="text-center">
                                                @if($offer->script)
                                                    <a href="{{route('script-show',$offer->script->id) }}"> <i
                                                                class="fa fa-check"></i> @lang('general.script') 2 <br></a>
                                                @else
                                                    {{'N/A'}}
                                                @endif
                                            </td>
                                            @if(isset($permissions['read_add_script']))
                                                <td>
                                                    <div class="pull-right">
                                                        <div class="btn-group">
                                                            <a aria-expanded="false"
                                                               class="btn btn-default btn-xs dropdown-toggle"
                                                               data-toggle="dropdown">
                                                                Действия <i class="fa fa-angle-down"></i>
                                                            </a>
                                                            <ul class="dropdown-menu pull-right" role="menu">
                                                                <li><a href="{{route('scripts-offers', $offer->id)}}">
                                                                  @lang('offers.view-scripts')
                                                                </a></li>
                                                                <li class="divider"></li>
                                                                <li><a href="/offer/{{$offer->id}}/script/add">
                                                                  @lang('offers.add-script')
                                                                </a></li>
                                                            </ul>
                                                        </div>
                                                        @if (isset($permissions['page_setting_offers']))
                                                            <a href="{{ route("offer", $offer->id) }}"
                                                               class="btn btn-primary btn-xs"><i class="fa fa-cog"></i></a>
                                                        @endif
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
                @if ($pagination && count($pagination[0]) > 1)
                    <ul class="pagination pull-right">
                        <li><a href="{{ route('offers') }}/{{ ($pagination[3]) ? $pagination[3] : '' }}"><i
                                        class="fa fa-chevron-left"></i></a></li>
                        @foreach ($pagination[0] as $number)
                            <? $activaPage = '' ?>
                            @if ($pagination[1] == $number)
                                <li class=active><span>{{ $number }}</span></li>
                            @else
                                @if ($number == 1)
                                    <li>
                                        <a href="{{ route('offers') }}/{{ ($pagination[3]) ? $pagination[3] : '' }}">{{ $number }}</a>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ route('offers') }}/{{ ($pagination[3]) ? $pagination[3] . '&page=' . $number : '?page=' . $number }}">{{ $number }}</a>
                                    </li>
                                @endif
                            @endif
                        @endforeach
                        <li>
                            <a href="{{ route('offers') }}/{{ ($pagination[3]) ? $pagination[3] . '&page=' . $pagination[2] : '?page=' . $pagination[2] }}"><i
                                        class="fa fa-chevron-right"></i></a></li>
                    </ul>
                @endif
            </div>
        </div>
    </div>

@stop
