@extends('layouts.dashboard')
@section('css')
    <link href="{{ asset('libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/datatables.mark.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('breadcrumb')
    <h4 class="page-title-main">{{ $page_title }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('attendees_report.dashboard') }}</a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li> -->
        <li class="breadcrumb-item active">{{ $page_title }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title mb-3">{{ $page_title }}</h4>
                <div class="d-flex justify-content-between mb-3">
                    <div class="bg-info p-1 px-2 text-light rounded font-weight-bold">{{ __('attendees_report.total_amount') }} :<span id="total_amount"></span> USD</div>
                    <div class="bg-primary p-1 px-2 text-light rounded font-weight-bold">{{ __('attendees_report.total_admin_commission') }} :<span id="total_admin_commission">0</span> USD</div>
                    <div class="bg-success p-1 px-2 text-light rounded font-weight-bold">{{ __('attendees_report.total_performers_amount') }} :<span id="total_performers_amount">0</span> USD</div>
                </div>
                <div class="mb-2 border border-secondary px-3 pt-3">
                    <div class="row">
                        <div class="col-sm-12 mb-1">
                            <h4 class="header-title">{{ __('attendees_report.filter_title') }}</h4>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <select class="form-control" id="filter_event">
                                <option value="">{{ __('attendees_report.select_event') }}</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <select class="form-control" id="filter_performer">
                                <option value="">{{ __('attendees_report.select_performer') }}</option>
                                @foreach ($users as $user)
                                    @if ($user->role == '2')
                                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <select class="form-control" id="filter_user">
                                <option value="">{{ __('attendees_report.select_user') }}</option>
                                @foreach ($users as $user)
                                    @if ($user->role == '1')
                                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <button type="button" id="filter_btn" class="btn btn-info">{{ __('attendees_report.filter_btn') }}</button>
                        </div>
                    </div>
                </div>
                <table id="events_table" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ __('attendees_report.id') }}</th>
                            <th>{{ __('attendees_report.user') }}</th>
                            <th>{{ __('attendees_report.event') }}</th>
                            <th>{{ __('attendees_report.performer') }}</th>
                            <th>{{ __('attendees_report.joining_fee') }}</th>
                            <th>{{ __('attendees_report.admin_commission') }}</th>
                            <th>{{ __('attendees_report.performer_amount') }}</th>
                            <th>{{ __('attendees_report.payment_status') }}</th>
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
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>
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
            //datatable
            var events_table = $('#events_table').DataTable({
                "mark": true,
                "ordering": false,
                "searching": false,
                "order": [
                    [0, "desc"]
                ],
                "columnDefs": [{
                        "targets": [-1],
                    },
                    {
                        "targets": [0],
                        "render": function(data, type, row, meta) {
                            return '#' + data;
                        }
                    },
                    {
                        "targets": [1],
                        "render": function(data, type, row, meta) {
                            return data?.length > 30 ? data.substring(0, 30) + '...' : data;
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
                    url: "{{ route('admin.attendees_reports.datatable') }}",
                    error: function(xhr) {
                        session_error(xhr);
                        // console.log(xhr.status);
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "user",
                        "render": function (data) {
                            return data ? data.username : '';
                        }
                    },
                    {
                        "data": "event.title"
                    },
                    {
                        "data": "event.performer",
                        "render": function (data) {
                            return data ? data.username : '';
                        }
                    },
                    {
                        "data": "amount"
                    },
                    {
                        "data": "admin_commission"
                    },
                    {
                        "data": "total_amount"
                    },
                    {
                        "data": "payment_status_label"
                    },
                ]
            }).on('processing.dt', function(e, settings, processing) {
                $('div.dataTables_processing').css('display', 'none');
                if (processing) {
                    $('.loader').fadeIn();
                } else {
                    $('.loader').fadeOut();
                }
            });

            // dt_search(events_table);

            $('#filter_btn').click(function() {
                var event = $('#filter_event').val();
                var performer = $('#filter_performer').val();
                var user = $('#filter_user').val();
                events_table.ajax.url("{{ route('admin.attendees_reports.datatable') }}?event=" + event + '&user=' + user + '&performer=' + performer, {}).load();
            });

            events_table.on('xhr.dt', function(e, settings, json, xhr) {
                $('#total_amount').text(json.total_amount);
                $('#total_admin_commission').text(json.total_admin_commission);
                $('#total_performers_amount').text(json.total_performers_amount);
            });


        });
    </script>
@endsection
