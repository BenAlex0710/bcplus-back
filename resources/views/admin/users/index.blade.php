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
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('user.dashboard') }}</a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li> -->
        <li class="breadcrumb-item active">{{ $page_title }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title mb-3">{{ $page_title }}</h4>
                <table id="users_table" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ __('user.id') }}</th>
                            <th>{{ __('user.username') }}</th>
                            <th>{{ __('user.email') }}</th>
                            <th>{{ __('user.first_name') }}</th>
                            <th>{{ __('user.last_name') }}</th>
                            <th>{{ __('user.role') }}</th>
                            <th>{{ __('user.status') }}</th>
                            <th>{{ __('user.action') }}</th>
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
            var type = "{{ request()->type }}";
            var users_table = $('#users_table').DataTable({
                "mark": true,
                "order": [
                    [0, "desc"]
                ],
                "columnDefs": [{
                        "className": "text-center",
                        "targets": [-1]
                    },
                    {
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
                    url: "{{ route('admin.user.datatable') }}/" + type,
                    error: function(xhr) {
                        session_error(xhr);
                        // console.log(xhr.status);
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "username"
                    },
                    {
                        "data": "email"
                    },
                    {
                        "data": "first_name"
                    },
                    {
                        "data": "last_name"
                    },
                    {
                        "data": "role_label"
                    },
                    {
                        "data": "status_label"
                    },
                    // { "data": "parent" },
                    // { "data": "sponsor" },
                    {
                        "data": "action"
                    }
                ]
            }).on('processing.dt', function(e, settings, processing) {
                $('div.dataTables_processing').css('display', 'none');
                if (processing) {
                    $('.loader').fadeIn();
                } else {
                    $('.loader').fadeOut();
                }
            });

            dt_search(users_table);

            // change status
            $(document).on('click', '.change_status', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var type = $(this).attr('title');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to " + type + " this user.",
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
                                    users_table.ajax.reload(null, false);
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
