@extends('layouts.dashboard')
@section('breadcrumb')
    <link href="{{ asset('libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/datatables.mark.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <h4 class="page-title-main">{{ $page_title }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('event.dashboard') }}</a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li> -->
        <li class="breadcrumb-item active">{{ $page_title }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title mb-3">{{ $page_title }}</h4>
                <div class="mb-2 border border-secondary px-3 pt-3">
                    <div class="row">
                        <div class="col-sm-12 mb-1">
                            <h4 class="header-title">{{ __('event.search_title') }}</h4>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <input type="text" class="form-control" id="filter_title" placeholder="{{ __('event.title') }}" value="{{ request()->title }}">
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <input type="text" class="form-control" id="filter_performer" placeholder="{{ __('event.performer') }}" value="{{ request()->performer }}">
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <button type="button" id="filter_btn" class="btn btn-info">{{ __('event.search_btn') }}</button>
                        </div>
                    </div>
                </div>
                <table id="events_table" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ __('event.id') }}</th>
                            <th>{{ __('event.title') }}</th>
                            <th>{{ __('event.performer') }}</th>
                            <th>{{ __('event.joining_fee') }}</th>
                            <th>{{ __('event.start_time') }}</th>
                            <th>{{ __('event.end_time') }}</th>
                            <th>{{ __('event.timezone') }}</th>
                            <th>{{ __('event.event_type') }}</th>
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
                            return data.length > 30 ? data.substring(0, 30) + '...' : data;
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
                    url: "{{ route('admin.events-datatable') }}",
                    error: function(xhr) {
                        session_error(xhr);
                        // console.log(xhr.status);
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "title"
                    },
                    {
                        "data": "performer.username"
                    },
                    {
                        "data": "joining_fee"
                    },
                    {
                        "data": "start_time"
                    },
                    {
                        "data": "end_time"
                    },
                    {
                        "data": "timezone"
                    },
                    {
                        "data": "event_type"
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
                var title = $('#filter_title').val();
                var performer = $('#filter_performer').val();
                events_table.ajax.url("{{ route('admin.events-datatable') }}?title=" + title + '&performer=' + performer, {}).load();
            });

            // change status
            $(document).on('click', '.change_status', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var type = $(this).attr('title');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to " + type + " this event.",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, ' + type + ' it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            type: 'put',
                            dataType: 'json',
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire(
                                        type + 'd',
                                        'User ' + type + 'd successfully.',
                                        'success'
                                    );
                                    events_table.ajax.reload(null, false);
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
