@extends('layouts.app')

@section('title') @lang('companies.all') @stop

@section('css')
@stop

@section('jsBottom')
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('companies.all')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('companies.all')</h1>
                @if (isset($permissions['add_chenge_companies']) && !auth()->user()->company_id)
                <div class="pull-right top-page-ui">
                    <a href="{{route('companies-registration')}}" class="btn btn-primary pull-right">
                        <i class="fa fa-plus-circle fa-lg"></i> @lang('companies.create')
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        @if ($companies)
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th> @lang('general.id')</th>
                                        <th> @lang('general.name')</th>
                                        <th> @lang('companies.type-pay')</th>
                                        <th class="text-center"> @lang('general.billing')</th>
                                        <th > @lang('companies.type-pay-billing')</th>
                                        @if (isset($permissions['add_chenge_companies']))
                                        <th style="width: 5%"></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($companies as $c)
                                        <tr>
                                            <td>{{ $c->id }}</td>
                                            <td>{{ $c->name }}</td>
                                            <td>
                                                @if ($c->type == 'lead')
                                                    @lang('companies.by-lead')
                                                @else
                                                    @lang('companies.by-hour')
                                                @endif
                                            </td>
                                            <td class="text-center">@if ($c->billing)<i class="fa  fa-check" style="color: #1ABC9C"></i>@endif</td>
                                            <td>
                                                @if ($c->billing_type)
                                                    @if ($c->type == 'lead')
                                                        @lang('companies.by-lead')
                                                    @else
                                                        @lang('companies.by-hour')
                                                    @endif
                                                @endif
                                            </td>
                                            @if (isset($permissions['add_chenge_companies']))
                                            <td>
                                                <a href="{{ route("company", $c->id) }}" class="table-link">
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
