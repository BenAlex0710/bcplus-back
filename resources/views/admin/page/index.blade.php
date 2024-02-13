@extends('layouts.dashboard')
@section('breadcrumb')
    <link href="{{ asset('libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/datatables.mark.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <h4 class="page-title-main">{{ $page_title }} </h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('page.breadcrumb_dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ $page_title }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title text-primary mb-3 pb-1 border-bottom border-primary">{{ __('page.manage_page') }}
                    <!-- <a href="javascript:void(0)" id="manage_menu" class="btn btn-sm  btn-warning p-1 float-right" style="margin-top: -15px;">Manage Menu</a> -->
                </h4>
                <a href="{{ route('admin.page.create') }}" class="btn btn-success btn-sm float-left">{{ __('page.create_new_page') }}</a>
                <table id="pages_table" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ __('page.id') }}</th>
                            @foreach ($langs as $lang)
                                <th>{{ __('page.' . $lang . '_title') }}</th>
                            @endforeach
                            <th>{{ __('page.slug') }}</th>
                            <th>{{ __('page.action') }}</th>
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
            var pages_table = $('#pages_table').DataTable({
                "mark": true,
                "order": [
                    [0, "desc"]
                ],
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
                    url: "{{ route('admin.page.datatable') }}",
                    error: function(xhr) {
                        session_error(xhr);
                        // console.log(xhr.status);
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    @foreach ($langs as $lang)
                        {
                        "data": "{{ $lang }}_title"
                        },
                    @endforeach {
                        "data": "slug"
                    },
                    {
                        "data": "action"
                    }
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

            dt_search(pages_table);


            // $(document).on('click', '.add_to_menu', function(){
            //     var page_id = $(this).data('id');
            //     var parent_id = $(this).data('parent_id');
            //     $('*[name="page"]').val(page_id);
            //     $('*[name="parent_page"]').val(parent_id);
            //     $('#add_to_menu_modal').modal('show');
            // });

            $('#manage_menu').click(function() {
                $('#add_to_menu_modal').modal('show');
            });

            $('*[name="page"]').change(function() {
                var page_id = $(this).val();
                var parent = $('*[name="parent_page"]');
                var parent_id = $(this).find('option[value="' + page_id + '"]').data('parent_id');
                var parent_option = parent.find('option[value="' + page_id + '"]');
                if (parent_option.length) {
                    parent.prop('disabled', true).val('0');
                } else {
                    parent.prop('disabled', false).val(parent_id);
                }
                var show_in_menu = $(this).find('option[value="' + page_id + '"]').data('show_in_menu');
                if (show_in_menu == 1) {
                    $('#show_in_menu').prop('checked', true);
                } else {
                    $('#show_in_menu').prop('checked', false);
                }

            });

            $(document).on('submit', '#add_page_to_menu', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var type = form.attr('method');
                $.ajax({
                    url: url,
                    type: type,
                    dataType: 'json',
                    data: form.serialize(),
                    success: function(res) {
                        if (res.status) {
                            Swal.fire(
                                'Success !!!',
                                'Menu updated successfully.',
                                'success'
                            );
                            window.location.href = '';
                        } else {
                            Swal.fire(
                                'Sorry!',
                                'Something went wrong, please try again.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 422) {
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                if (k == 'menu_order') {
                                    form.find('[name="' + k + '"]').parent().after('<div class="text-danger">' + v + '</div>');
                                } else {
                                    form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                                }
                            });
                        } else if (xhr.status == 419 || xhr.status == 401) {
                            window.location.href = "";
                        }
                    }
                });
            });

            // remove from menu
            $(document).on('click', '.remove_page_from_menu', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to remove this page from menu",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            type: 'post',
                            dataType: 'json',
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire(
                                        'Removed',
                                        'Page Removed from menu successfully.',
                                        'success'
                                    );
                                    window.location.href = "";
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

            // change status
            $(document).on('click', '.change_status', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var type = $(this).data('type');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to " + type + " this Page.",
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
                            // data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire(
                                        type + 'd',
                                        'User ' + type + 'd successfully.',
                                        'success'
                                    );
                                    $(document).find('[data-toggle="tooltip"]').tooltip();
                                    pages_table.ajax.reload();
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

            //restore
            $(document).on('click', '.user_restore', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to restore this user from trash!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Restore it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            type: 'post',
                            dataType: 'json',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire(
                                        'Restored!',
                                        'User restored successfully.',
                                        'success'
                                    );
                                    // $('#pages_table').DataTable().ajax.reload();
                                    pages_table.ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
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
