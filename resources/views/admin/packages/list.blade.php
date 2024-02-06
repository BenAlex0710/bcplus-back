@extends('layouts.dashboard')
@section('breadcrumb')
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <h4 class="page-title-main">{{ $page_title }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('package.breadcrumb_dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ $page_title }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title mb-3">{{ __('package.package_list') }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('package.name_en') }}</th>
                                <th>{{ __('package.name_zh') }}</th>
                                {{-- <th>{{ __('package.type') }}</th> --}}
                                <th>{{ __('package.price') }}</th>
                                <th>{{ __('package.events') }}</th>
                                <th>{{ __('package.event_max_duration') }}</th>
                                <th>{{ __('package.validity') }}</th>
                                <th class="text-center">{{ __('package.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packages as $package)
                                <tr>
                                    <td>{{ $package->en_name }} @if ($package->trial == '1')
                                            &nbsp; <span class="badge badge-warning text-light">{{ __('package.trial') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $package->zh_name }}</td>
                                    {{-- <td>{{ __('package.type_' . $package->type) }}</td> --}}
                                    <td>{{ $package->price }}</td>
                                    <td>{{ $package->events }}</td>
                                    <td>{{ $package->event_max_duration }}</td>
                                    <td>{{ $package->validity }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.package.edit', $package->id) }}" class="text-primary" data-toggle="tooltip" title="{{ __('package.tooltips.edit') }}"><i class="fe-edit"></i></a> &nbsp;
                                        @if ($package->trial == '0')
                                            <a href="{{ route('admin.package.set_trial', $package->id) }}" class="text-info set_trial" onclick="$('#trial_form').submit()" data-toggle="tooltip" title="{{ __('package.tooltips.set_trial') }}"><i class="fe-crosshair"></i></a> &nbsp;
                                        @endif
                                        @if ($package->status == 1)
                                            <a href="{{ route('admin.package.change_status', $package->id) }}" class="text-warning change_status" title="{{ __('package.tooltips.disable') }}" data-type="0" data-toggle="tooltip"><i class="fe-x-circle"></i></a> &nbsp;
                                        @else
                                            <a href="{{ route('admin.package.change_status', $package->id) }}" class="text-success change_status" title="{{ __('package.tooltips.enable') }}" data-type="1" data-toggle="tooltip"><i class="fe-check-circle"></i></a> &nbsp;
                                        @endif
                                        <a href="{{ route('admin.package.delete', $package->id) }}" class="text-danger delete_package" title="{{ __('package.tooltips.delete') }}" data-toggle="tooltip"><i class="fe-trash"></i></a>
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
@section('js')
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script type="text/javascript">
        $(function() {

            $('[data-toggle="tooltip"]').tooltip();

            // change status
            $(document).on('click', '.change_status', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var type = $(this).data('type');

                if (type == '1') {
                    var title = "{{ __('package.status_enable.title') }}";
                    var subtitle = "{{ __('package.status_enable.subtitle') }}";
                    var confirm_text = "{{ __('package.status_enable.confirm_text') }}";
                    var cancel_text = "{{ __('package.status_enable.cancel_text') }}";
                    var result_success_title = "{{ __('package.status_enable.result_success_title') }}";
                    var result_failed_title = "{{ __('package.status_enable.result_failed_title') }}";
                } else {
                    var title = "{{ __('package.status_disable.title') }}";
                    var subtitle = "{{ __('package.status_disable.subtitle') }}";
                    var confirm_text = "{{ __('package.status_disable.confirm_text') }}";
                    var cancel_text = "{{ __('package.status_disable.cancel_text') }}";
                    var result_success_title = "{{ __('package.status_disable.result_success_title') }}";
                    var result_failed_title = "{{ __('package.status_disable.result_failed_title') }}";
                }

                Swal.fire({
                    title: title,
                    text: subtitle,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirm_text,
                    cancelButtonText: cancel_text
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            type: 'put',
                            dataType: 'json',
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire(
                                        result_success_title,
                                        res.message,
                                        'success'
                                    ).then(() => {
                                        window.location.reload()
                                    });
                                } else {
                                    Swal.fire(
                                        result_failed_title,
                                        res.message,
                                        'error'
                                    ).then(() => {
                                        window.location.reload()
                                    });
                                }
                            },
                            error: function(xhr) {
                                session_error(xhr);
                            }
                        });
                    }
                });
            });


            $(document).on('click', '.set_trial', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                var title = "{{ __('package.set_trial_popup.title') }}";
                var subtitle = "{{ __('package.set_trial_popup.subtitle') }}";
                var confirm_text = "{{ __('package.set_trial_popup.confirm_text') }}";
                var cancel_text = "{{ __('package.set_trial_popup.cancel_text') }}";
                var result_success_title = "{{ __('package.set_trial_popup.result_success_title') }}";
                var result_failed_title = "{{ __('package.set_trial_popup.result_failed_title') }}";

                Swal.fire({
                    title: title,
                    text: subtitle,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirm_text,
                    cancelButtonText: cancel_text
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            type: 'put',
                            dataType: 'json',
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire(
                                        result_success_title,
                                        res.message,
                                        'success'
                                    ).then(() => {
                                        window.location.reload()
                                    });
                                } else {
                                    Swal.fire(
                                        result_failed_title,
                                        res.message,
                                        'error'
                                    ).then(() => {
                                        window.location.reload()
                                    });
                                }
                            },
                            error: function(xhr) {
                                session_error(xhr);
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.delete_package', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                Swal.fire({
                    title: "{{ __('package.delete_popup.title') }}",
                    text: "{{ __('package.delete_popup.subtitle') }}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('package.delete_popup.confirm_text') }}",
                    cancelButtonText: "{{ __('package.delete_popup.cancel_text') }}"
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            type: 'delete',
                            dataType: 'json',
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire(
                                        "{{ __('package.delete_popup.result_success_title') }}",
                                        res.message,
                                        'success'
                                    ).then(() => {
                                        window.location.reload()
                                    });
                                } else {
                                    Swal.fire(
                                        "{{ __('package.delete_popup.result_failed_title') }}",
                                        res.message,
                                        'error'
                                    ).then(() => {
                                        window.location.reload()
                                    });
                                }
                            },
                            error: function(xhr) {
                                session_error(xhr);
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
