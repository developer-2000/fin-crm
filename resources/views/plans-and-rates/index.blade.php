@extends('layouts.app')

@section('title')Планы@stop

@section('css')

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/profile.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
@stop

@section('jsBottom')

    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/plans/plans.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active"><span>Планы</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Планы</h1>
                @if (isset($permissions['create_edit_plan']))
                    <div class="pull-right top-page-ui">
                        <a href="{{route('plans-create')}}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> Добавить план
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{ route('plans') }}">Планы</a>
                        </li>
                        <li class="">
                            <a href="{{ route('plans-logs') }}">Логи по транзакциям</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">
                                        @if (!$plans->isEmpty())
                                            <div class="main-box-body clearfix"
                                                 style="margin-top: 20px;padding: 0 0 20px 0;">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Activity</th>
                                                            <th class="text-center">Название</th>
                                                            <th class="text-center">Компания</th>
                                                            <th class="text-center">Интервал</th>
                                                            <th class="text-center">Обьект расчета</th>
                                                            <th class="text-center">Тип оплаты/</br>
                                                                Тип расчета
                                                            </th>
                                                            <th class="text-center">Основные критерии</th>
                                                            <th class="text-center">Исключающие критерии</th>
                                                            <th class="text-center">База для расчета</th>
                                                            <th class="text-center">План </br> выполнен, кол./сумма/%
                                                            </th>
                                                            <th class="text-center">Комментарий</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($plans as $plan)
                                                            <tr>
                                                                <td class="text-center">{{$plan->id}}</td>
                                                                <td class="text-center">
                                                                    @if($plan->status == 'active')
                                                                        <span id="activity" class="label label-success">Active</span>
                                                                    @else
                                                                        <span id="activity" class="label label-danger">Inactive</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="#" class="story-author-link">
                                                                        {{$plan->name}}   </a>
                                                                </td>
                                                                <td class="text-center">{{$plan->company->name}}</td>
                                                                <td class="text-center">
                                                                    @if($plan->interval){{$plan->interval}}
                                                                    @else{{"N/A"}}
                                                                    @endif</td>
                                                                <td class="text-center">{{$plan->type_object}}</td>
                                                                <td class="text-center">{{$plan->company->type}}
                                                                    /
                                                                    {{$plan->type_method}}</td>
                                                                {{--Основные критерии--}}
                                                                <td class="info-data">
                                                                    <div class="value">
                                                                        @if(!empty($plan['operators_groups']['data']))
                                                                            Группы операторов:
                                                                            @foreach($plan['operators_groups']['data'] as $group)
                                                                                <span> {{$group->name }},</span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="value">
                                                                        @if(!empty($plan['operators']['data']))
                                                                            Операторы:
                                                                            @foreach($plan['operators']['data'] as $operator)
                                                                                <span>{{$operator->surname }} {{$operator->name }}
                                                                                    , </span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="value">
                                                                        @if(!empty($plan['countries']['data']))
                                                                            Страны:
                                                                            @foreach($plan['countries']['data'] as $country)
                                                                                <span> {{$country->name }}</span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="value">
                                                                        @if(!empty($plan['offers']['data']))
                                                                            Офферы:
                                                                            @foreach($plan['offers']['data'] as $offer)
                                                                                <span> {{$offer->name }}</span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="value">
                                                                        @if(!empty($plan['products']['data']))
                                                                            Товары:
                                                                            @foreach($plan['products']['data'] as $product)
                                                                                <span> {{$product->name }}</span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                </td>

                                                                {{--Исключающие критерии--}}
                                                                <td class="info-data">
                                                                    <div class="value">
                                                                        @if(!empty($plan['operators_groups_except']['data']))
                                                                            Группы операторов:
                                                                            @foreach($plan['operators_groups_except']['data'] as $group)
                                                                                <span> {{$group->name }},</span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="value">
                                                                        @if(!empty($plan['operator_except']['data']))
                                                                            Операторы:
                                                                            @foreach($plan['operator_except']['data'] as $operator)
                                                                                <span> {{$operator->surname }} {{$operator->name }}</span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="value">
                                                                        @if(!empty($plan['countries_except']['data']))
                                                                            Страны:

                                                                            @foreach($plan['countries_except']['data'] as $country)
                                                                                <span> {{$country->name }} {{$country->name }}</span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="value">
                                                                        @if(!empty($plan['offers_except']))
                                                                            Офферы:
                                                                            @foreach($plan['offers_except']['data'] as $offer)
                                                                                <span> {{$offer->name }}</span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="value">
                                                                        @if(!empty($plan['products_except']['data']))
                                                                            Товары:
                                                                            @foreach($plan['products_except']['data'] as $product)
                                                                                <span> {{$product->title }},</span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                    <div class="value">

                                                                        @if(!empty( json_decode($plan['product_type_except']) != false ? $types = json_decode($plan['product_type_except']) : ''))
                                                                            Типы товаров:
                                                                            @foreach($types as $type)
                                                                                @if($type == '1')
                                                                                    <span> Up_sell_1,</span>
                                                                                @endif
                                                                                @if($type == '2')
                                                                                    <span> Up_sell_2,</span>
                                                                                @endif
                                                                                @if($type == '4')
                                                                                    <span> Cross_sell,</span>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                @if(!empty($plan->basis_for_calculation) && $plan->basis_for_calculation == 'percent')
                                                                <td class="text-center">Процент</td>
                                                                    @elseif(!empty($plan->basis_for_calculation) && $plan->basis_for_calculation == 'percent-each')
                                                                        <td class="text-center">Процент (расчет производится по каждой транзакции)</td>
                                                                        @elseif(!empty($plan->basis_for_calculation) && $plan->basis_for_calculation == 'quantity')
                                                                            <td class="text-center">Количество заказов</td>
                                                                            @elseif(!empty($plan->basis_for_calculation) && $plan->basis_for_calculation == 'sum-each')
                                                                                <td class="text-center">Сумма (расчет производится по каждой транзакции)</td>
                                                                    @else
                                                                    <td class="text-center">N/A</td>
                                                                                @endif
                                                                <td class="text-center">{{$plan->success_plan}}</td>
                                                                <td class="text-center">{{$plan->comment}}</td>
                                                                @if (isset($permissions['create_edit_plan']))
                                                                    <td>
                                                                        <a href="{{ route("plan", $plan->id) }}"
                                                                           class="table-link">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                                    </span>
                                                                        </a>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                Ни одного плана еще не создано!
                                                <a href="/plan/create" target="_blank">Добавить план.</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $plans->links() }}
@stop
