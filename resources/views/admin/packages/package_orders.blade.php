@extends('layouts.dashboard')
@section('css')
    <link href="{{ asset('libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/datatables.mark.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('breadcrumb')
    <h4 class="page-title-main">{{ __('package.orders.manage_orders') }}</h4>
    <ol class="breadcrumb">
        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li> -->
        <li class="breadcrumb-item active">{{ __('package.orders.manage_orders') }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title text-primary mb-3 pb-1 border-bottom border-primary">{{ __('package.orders.manage_orders') }}
                </h4>
                <div class="mb-2 border border-secondary px-3 pt-3">
                    <div class="row">
                        <div class="col-sm-12 mb-1">
                            <h4 class="header-title">{{ __('package.orders.filter_package_orders') }}</h4>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <select class="form-control" id="filter_users" multiple autocomplete="off">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <select class="form-control" id="filter_payment_status" autocomplete="off">
                                <option value="" selected>{{ __('package.orders.filter_payment_status_placeholder') }}</option>
                                <option value="0">{{ __('package.payment_status.0') }}</option>
                                <option value="1">{{ __('package.payment_status.1') }}</option>
                                <option value="2">{{ __('package.payment_status.2') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <select class="form-control" id="filter_status" autocomplete="off">
                                <option value="" selected>{{ __('package.orders.filter_status_placeholder') }}</option>
                                <option value="0">{{ __('package.orders.status_labels.0') }}</option>
                                <option value="1">{{ __('package.orders.status_labels.1') }}</option>
                                <option value="2">{{ __('package.orders.status_labels.2') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <button class="btn btn-info" id="filter_btn">{{ __('package.orders.filter_btn') }}</button>
                        </div>
                    </div>
                </div>
                <table id="package_orders_table" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ __('package.orders.id') }}</th>
                            <th>{{ __('package.orders.username') }}</th>
                            <th>{{ __('package.orders.amount') }}</th>
                            <th>{{ __('package.orders.package_name') }}</th>
                            <th>{{ __('package.orders.remaining_events') }}</th>
                            <th>{{ __('package.orders.events') }}</th>
                            <th>{{ __('package.orders.storage') }}</th>
                            <th>{{ __('package.orders.video_quality') }}</th>
                            <th>{{ __('package.orders.max_duration') }}</th>
                            <th>{{ __('package.orders.max_attendees') }}</th>
                            <th>{{ __('package.orders.validity') }}</th>
                            <th>{{ __('package.orders.start_date') }}</th>
                            <th>{{ __('package.orders.expiry_date') }}</th>
                            <th>{{ __('package.orders.payment_status') }}</th>
                            <th>{{ __('package.orders.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{ asset('libs/datatables/jquery.mark.min.js') }}"></script>
    <script src="{{ asset('libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('libs/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('libs/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/datatables.mark.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            var locale = "{{ App::getLocale() }}";
            // console.log(locale);

            $('#filter_users').select2({
                placeholder: "{{ __('package.orders.filter_users_placeholder') }}"
            });
            $('#filter_payment_status').select2({
                minimumResultsForSearch: -1,
                // placeholder: "{{ __('package.orders.filter_payment_status_placeholder') }}"
            });
            $('#filter_status').select2({
                minimumResultsForSearch: -1,
                // placeholder: "{{ __('package.orders.filter_status_placeholder') }}"
            });

            //datatable
            var package_orders_table = $('#package_orders_table').DataTable({
                "mark": true,
                "ordering": false,
                "searching": false,
                "columnDefs": [{
                        "targets": [0],
                        "render": function(data, type, row, meta) {
                            return '#' + data;
                        }
                    },
                    {
                        "responsivePriority": 1,
                        "targets": 0
                    },
                    {
                        "responsivePriority": 2,
                        "targets": -1,
                        "orderable": false
                    }
                ],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('admin.package_orders.datatable') }}",
                    error: function(xhr) {
                        session_error(xhr);
                        // console.log(xhr.status);
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "user.username"
                    },
                    {
                        "data": "amount"
                    },
                    {
                        "data": "package_data." + locale + "_name"
                    },
                    {
                        "data": "events"
                    },
                    {
                        "data": "package_data.events"
                    },
                    {
                        "data": "package_data.storage"
                    },
                    {
                        "data": "package_data.video_quality"
                    },
                    {
                        "data": "package_data.event_max_duration"
                    },
                    {
                        "data": "package_data.max_attendees"
                    },
                    {
                        "data": "package_data.validity"
                    },
                    {
                        "data": "start_date"
                    },
                    {
                        "data": "expiry_date"
                    },
                    {
                        "data": "payment_status_label"
                    },
                    {
                        "data": "status_label"
                    },
                ],
                drawCallback: function(settings) {
                    $(document).find('[data-toggle="tooltip"]').tooltip();
                },
                initComplete: function(settings, json) {
                    // $(document).find('[data-toggle="tooltip"]').tooltip();
                }
            }).on('processing.dt', function(e, settings, processing) {
                $('div.dataTables_processing').css('display', 'none');
                if (processing) {
                    $('.loader').fadeIn();
                } else {
                    $('.loader').fadeOut();
                }
            });

            // dt_search(package_orders_table);

            $('#filter_btn').click(function() {
                var users = $('#filter_users').val();
                var payment_status = $('#filter_payment_status').val();
                var status = $('#filter_status').val();
                package_orders_table.ajax.url("{{ route('admin.package_orders.datatable') }}?users=" + users + '&payment_status=' + payment_status + '&status=' + status, {}).load();
            });

            // change status
            $(document).on('click', '.change_status', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var type = $(this).data('type');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "{!! __('package.orders.update_success_warning') !!}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it to success!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            type: 'put',
                            dataType: 'json',
                            // data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire(
                                        'Success !!',
                                        'Payment status updated successfully.',
                                        'success'
                                    );
                                    $(document).find('[data-toggle="tooltip"]').tooltip();
                                    package_orders_table.ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Oops..',
                                        'Something went wrong, please try again.',
                                        'error'
                                    );
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
