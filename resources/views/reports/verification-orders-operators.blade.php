@extends('layouts.app')

@section('title') @lang('reports.login-logout')@stop

@section('css')
@stop

@section('jsBottom')
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                                <li class="active"><span> @lang('menu.link-title.verification')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('menu.link-title.verification')</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <form method="post" action="{{ route('verification-orders--operators')}}">
                <div class="main-box">
                    <div class="item_rows ">
                        <div class="main-box-body clearfix">
                            <div class="row">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle fa-fw fa-lg"></i>
                                    Для корректной работы, <strong>Id заказов</strong>, необходимо писать через <strong>запятую</strong> или с <strong>новой строки</strong>
                                </div>
                                <div class="form-group col-md-12 form-horizontal">
                                    <label for="order_ids" class="col-sm-1 control-label"> @lang('general.id')</label>
                                    <div class="col-sm-11">
                                        <textarea class="form-control" id="order_ids" name="order_ids">@if (isset($_GET['order_ids'])){{ $_GET['order_ids'] }}@endif</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center" style="padding-bottom:20px;">
                    <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                    <a href="{{ route('verification-orders--operators') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
								<th>
                                    #
                                </th>
                                <th>
                                    @lang('general.id')
                                </th>
                                <th>
                                    @lang('general.user')
                                </th>
                                <th>
                                    @lang('general.date-target')
                                </th>
                            </tr>
                            </thead>
                            @if ($orders->isNotEmpty())
                                <tbody>
                                @foreach($orders as $key => $order)
									
                                    <tr>
										<td>{{ $key+1 }}</td>
                                        <td>
                                            <a href="{{route('order', $order->id)}}">
                                                {{$order->id}}
                                            </a>
                                        </td>
                                        <td>{{$order->targetUser->surname ?? $order->target_user}} {{$order->targetUser->name ?? $order->target_user}}</td>
                                        <td>{{$order->time_modified}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @endif
                        </table>
                    </div>
					
					<div class="table-responsive">
						<h2>Subtotal</h2>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>
                                    @lang('general.user')
                                </th>
                                <th>
                                    @lang('general.orders')
                                </th>
                            </tr>
                            </thead>
                            @if ($subtotal->isNotEmpty())
                                <tbody>
                                @foreach($subtotal as $user => $orders)
                                    <tr>
                                        <td>{{ $user }}</td>
                                        <td>{{ $orders }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @endif
                        </table>
                    </div>
					
                </div>
            </div>
        </div>
    </div>
@stop
