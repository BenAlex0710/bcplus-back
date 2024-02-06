@extends('layouts.dashboard')
@section('breadcrumb')
    <h4 class="page-title-main">{{ __('dashboard.breadcrumb_name') }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">{{ __('dashboard.breadcrumb_name') }}</li>
    </ol>
@endsection
@section('content')
    <div class="row text-center">
        <div class="col-sm-6 col-xl-3">
            <div class="card-box widget-flat border-primary bg-primary text-white">
                <i class="fe-users"></i>
                <h3 class="text-white">{{ $todays_users_count }}</h3>
                <p class="text-uppercase font-13 mb-2 font-weight-bold">{{ __('dashboard.todays_users') }}</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card-box bg-blue widget-flat border-blue text-white">
                <i class="fe-dollar-sign"></i>
                <h3 class="text-white">{{ $todays_sale_amount }} {{ $currency }}</h3>
                <p class="text-uppercase font-13 mb-2 font-weight-bold">{{ __('dashboard.todays_sale') }}</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card-box widget-flat border-success bg-success text-white">
                <i class="fe-users"></i>
                <h3 class="text-white">{{ $total_user_count }}</h3>
                <p class="text-uppercase font-13 mb-2 font-weight-bold">{{ __('dashboard.total_users') }}</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card-box bg-danger widget-flat border-danger text-white">
                <i class="fe-dollar-sign"></i>
                <h3 class="text-white">{{ $total_sale_amount }} {{ $currency }}</h3>
                <p class="text-uppercase font-13 mb-2 font-weight-bold">{{ __('dashboard.total_sale') }}</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card-box">
                <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-primary float-right">{{ __('dashboard.view_all') }}</a>
                <h4 class="header-title mb-3">{{ __('dashboard.users.box_heading') }}</h4>
                <div class="table-responsive_dfhjdfh">
                    <table class="table table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" id="recent_referrals">
                        <thead>
                            <tr>
                                <th>{{ __('dashboard.users.columns.username') }}</th>
                                <th>{{ __('dashboard.users.columns.name') }}</th>
                                <th>{{ __('dashboard.users.columns.email') }}</th>
                                <th>{{ __('dashboard.users.columns.status') }}</th>
                                <th>{{ __('dashboard.users.columns.joining_time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recent_users as $user)
                                <tr>
                                    <td>
                                        {{ $user->username }}
                                    </td>
                                    <td>
                                        {{ $user->full_name }}
                                    </td>
                                    <td>
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                        {!! $user->status_label !!}
                                    </td>
                                    <td>
                                        {{ $user->joining_time }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
