@extends('layouts.dashboard')
@section('breadcrumb')
<link href="{{asset('libs/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('libs/datatables/datatables.mark.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('libs/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('libs/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('libs/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
<h4 class="page-title-main">Earning Report <span class="text-primary font-weight-bolder">({{$user->username}})</span></h4>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
    <!-- <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li> -->
    <li class="breadcrumb-item active">Earning Report</li>
</ol>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card-box">
            <!-- <h4 class="header-title mb-3 text-primary">Earning Report <span class="text-primary font-weight-bolder">({{$user->username}})</span></h4>
            <p class="sub-header">You can check complete reports or details of specified user.</p> -->
            <h4 class="header-title mb-3 text-primary">Personal Info</h4>
            <table class="table table-striped table-bordered" id="personal_info">
                <tr>
                    <th>Id</th>
                    <td># {{$user->id}}</td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td>{{$user->username}}</td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td>{{$user->first_name}}</td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td>{{$user->last_name}}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{$user->email}}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{$user->phone}}</td>
                </tr>
                <tr>
                    <th>Photo</th>
                    <td><a target="_blank" href="{{$user->photo_url}}"><img src="{{$user->photo_url}}" alt="{{$user->username}}" width="50"></a></td>
                </tr>
                <tr>
                    <th>Joining Time</th>
                    <td>{{$user->joining_time}}</td>
                </tr>
            </table>

            <!-- <table id="epins_table" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Epin No.</th>
                        <th>Price</th>
                        <th>Username</th>
                        <th>Used At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table> -->
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-box">
            <h4 class="header-title mb-3 text-primary">Network info</h4>
            <table class="table table-striped table-bordered" id="personal_info">
                <!-- <tr>
                    <th>Userkey</th>
                    <td>{{$user->userkey}}</td>
                </tr> -->
                <tr>
                    <th>Sponsor</th>
                    <td>{{$user->sponsor}}</td>
                </tr>
                <tr>
                    <th>Parent</th>
                    <td>{{$user->parent}}</td>
                </tr>
                <tr>
                    <th>Payment Status</th>
                    <td>{!! $user->payment_status_label !!}</td>
                </tr>
                <tr>
                    <th>Direct Referrals</th>
                    <td>{{$user->direct_referrals->count()}}</td>
                </tr>
                <tr>
                    <th>E-Wallet Balance</th>
                    <td class="text-success">{{$user->e_wallet_balance}} {{$currency}}</td>
                </tr>
                <tr>
                    <th>Pending Amount</th>
                    <td class="text-warning">{{$user->pending_amount}} {{$currency}}</td>
                </tr>
                <tr>
                    <th>Earned Amount</th>
                    <td class="text-primary">{{$user->earned_amount}} {{$currency}}</td>
                </tr>
                <tr>
                    <th>Withdrawaled Amount</th>
                    <td class="text-danger">{{$user->withdrawals()->where('status', '1')->sum('amount')}} {{$currency}} <small class="text-primary">( In Proccess {{$user->withdrawals()->where('status', '0')->sum('amount')}} {{$currency}} )</small></td>
                </tr>
            </table>

            <!-- <table id="epins_table" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Epin No.</th>
                        <th>Price</th>
                        <th>Username</th>
                        <th>Used At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table> -->
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title mb-3 text-primary">Referrals</h4>
            <table class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" id="referrals_table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Parent Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Referrals</th>
                        <th>Joining Time</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title mb-3 text-primary">Transactions</h4>
            <table class="table table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" id="transactions_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>CR</th>
                        <th>DR</th>
                        <th>Referrence</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title mb-3 text-primary">Withdrawals</h4>
            <table id="withdrawal_requests" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <!-- <th>Username</th> -->
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <!-- <th>Getway Details</th> -->
                        <th>Transaction Id</th>
                        <th>Status</th>
                        <!-- <th>Comment</th> -->
                        <th>Requested At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title mb-3 text-primary">Payouts</h4>
            <table class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" id="payouts_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Join<br>Commission</th>
                        <th>Referral<br>Commission</th>
                        <th>Level<br>Commission</th>
                        <th>Regular<br>Bonus</th>
                        <th>Royalty<br>Bonus</th>
                        <th>Deduction<br>Amount</th>
                        <th>Total<br>Amount</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div id="withdrawal-view" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="withdrawal_id" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <form id="withdrawal-update" method="put" action="" autocomplete="off">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Withdrawal #<span></span></h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>ID</th>
                            <td id="withdrawal_id"></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td id="username"></td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td id="amount">1245</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td id="payment_method"></td>
                        </tr>
                        <tr>
                            <th>Requested At</th>
                            <td id="requested_at"></td>
                        </tr>
                        <tr>
                            <th>Getway Data</th>
                            <td id="getway_data">1245</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <select class="form-control" name="status" id="status">
                                    <!-- <option selected disabled value="">Please Select</option> -->
                                    <option value="0">Pending</option>
                                    <option value="1">Completed</option>
                                    <option value="2">Rejected</option>
                                </select>
                            </td>
                        </tr>                    
                        <tr>
                            <th>Transaction Id</th>
                            <td>
                                <input type="text" class="form-control" name="transaction_id" id="transaction_id">
                            </td>
                        </tr>
                        <tr>
                            <th>Comment</th>
                            <td>
                                <textarea class="form-control" id="comment" name="comment"></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                </div>
            </div>
        </form>
    </div><!-- /.modal-dialog -->
</div>

@endsection
@section('js')
<script type="text/javascript" src="{{asset('libs/datatables/jquery.mark.min.js')}}"></script>
<script src="{{asset('libs/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="{{asset('libs/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('libs/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('libs/datatables/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('libs/datatables/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('libs/datatables/dataTables.keyTable.min.js')}}"></script>
<script src="{{asset('libs/datatables/dataTables.select.min.js')}}"></script>
<script src="{{asset('libs/jszip/jszip.min.js')}}"></script>
<script src="{{asset('libs/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('libs/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('libs/datatables/buttons.html5.min.js')}}"></script>
<script src="{{asset('libs/datatables/buttons.print.min.js')}}"></script>
<script src="{{asset('libs/datatables/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('libs/datatables/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('libs/datatables/datatables.mark.js')}}"></script>
<script type="text/javascript">
$(function() {
    var currency = "{{$currency}}";
    // var type = "{{request()->type}}";

    var referrals_table = $('#referrals_table').DataTable({
        "mark" : true,
        "searching" : true,
        "bLengthChange" : true,
        "searchDelay" : 350,
        "select":{
            "style":"single"
            // style:    'os',
            // selector: 'td:first-child'
        },
        "columnDefs" : [
            {
                "className": "text-center align-middle", "targets": '_all'
            }
        ],
        "order": [
            [5, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "{{route('admin.user.referrals.datatable', request()->id)}}/",
            error: function(xhr) {
                $('.loader').fadeOut();
                if (xhr.status == 422) {
                    $.each(xhr.responseJSON.errors, function(k, v) {
                        form.after('<div class="text-danger">' + v + '</div>');
                    });
                } else if (xhr.status == 419 || xhr.status == 401) {
                    window.location.href = "";
                }
            }
        },
        "columns": [
            { "data": "username" },
            { "data": "parent" },
            { "data": "full_name" },
            { "data": "email" },
            { "data": "direct_referrals_count" },
            { "data": "joining_time" }
        ]
    }).on('processing.dt', function(e, settings, processing) {
        $('div.dataTables_processing').css('display', 'none');
        if (processing) {
            $('.loader').fadeIn();
        } else {
            $('.loader').fadeOut();
        }
    });

    dt_search(referrals_table);



    var transactions_table = $('#transactions_table').DataTable({
        "searching" : false,
        "bLengthChange" : false,
        "select":{
            "style":"single"
            // style:    'os',
            // selector: 'td:first-child'
        },
        "order": [
            [0, "desc"]
        ],
        "columnDefs": [
            {
                "targets": [0],
                "data": "amount",
                "render": function ( data, type, row, meta ) {
                    return '#' +data;
                }
            },
            {
                "targets": [1, 2],
                "data": "amount",
                "render": function ( data, type, row, meta ) {
                    if(data === null) {
                        return '0 ' + currency;
                    }
                    return data+ ' ' +currency;
                }
            }
        ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "{{route('admin.user.transactions.datatable', request()->id)}}/",
            error: function(xhr) {
                $('.loader').fadeOut();
                if (xhr.status == 422) {
                    $.each(xhr.responseJSON.errors, function(k, v) {
                        form.after('<div class="text-danger">' + v + '</div>');
                    });
                } else if (xhr.status == 419 || xhr.status == 401) {
                    window.location.href = "";
                }
            }
        },
        "columns": [
            { "data": "id" },
            { "data": "cr" },
            { "data": "dr" },
            { "data": "referrence_label" },
            { "data": "time" }
        ]
    }).on('processing.dt', function(e, settings, processing) {
        $('div.dataTables_processing').css('display', 'none');
        if (processing) {
            $('.loader').fadeIn();
        } else {
            $('.loader').fadeOut();
        }
    });


    var withdrawals_table = $('#withdrawal_requests').DataTable({
        "mark" : true,
        "select":{
            "style":"single"
            // style:    'os',
            // selector: 'td:first-child'
        },
        "order": [
            [0, "desc"]
        ],
        "columnDefs": [
            {
                "className": "text-center align-middle", "targets": '_all' //[1,2,3,4,5]
            },
            {
                "targets" : [6],
                "orderable" : false,
            },
            {
                "targets": [0],
                "render": function ( data, type, row, meta ) {
                    return '#'+data;
                }
            },{
                "targets": [1],
                "render": function ( data, type, row, meta ) {
                    if(data === null) {
                        // return JSON.parse(data);
                        return '0 ' + currency;
                    }
                    return data+ ' ' +currency;
                }
            }
        ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "{{route('admin.user.withdrawals.datatable', request()->id)}}/",
            error: function(xhr) {
                session_error(xhr);
                /*$('.loader').fadeOut();
                if (xhr.status == 422) {
                    $.each(xhr.responseJSON.errors, function(k, v) {
                        form.after('<div class="text-danger">' + v + '</div>');
                    });
                } else if (xhr.status == 419 || xhr.status == 401) {
                    window.location.href = "";
                }*/
            }
        },
        "columns": [
            { "data": "id" },
            // { "data": "username" },
            { "data": "amount" },
            { "data": "payment_method" },
            // { "data": "getway_data" },
            { "data": "transaction_id" },
            { "data": "status_label" },
            // { "data": "comment" },
            { "data": "requested_at" },
            { "data": "action" }
        ]
    }).on('processing.dt', function(e, settings, processing) {
        $('div.dataTables_processing').css('display', 'none');
        if (processing) {
            $('.loader').fadeIn();
        } else {
            $('.loader').fadeOut();
        }
    });

    dt_search(withdrawals_table);

    $(document).on('click', '.withdrawal_view', function(e){
        e.preventDefault();
        $('.loader').fadeIn();
        var url = $(this).attr('href');
        $.ajax({
            url : url,
            dataType : 'json',
            success : function(result) {
                $('.loader').fadeOut();
                $('#withdrawal-view .modal-title span').text(result.id);
                $('#withdrawal_id').text(result.id);
                $('#amount').text(result.amount+' '+currency);
                $('#username').text(result.user.username);
                $('#payment_method').text(result.payment_method);
                $('#transaction_id').val(result.transaction_id);
                $('#requested_at').text(result.requested_at);
                $('#comment').text(result.comment);
                $('#status option[value="'+result.status+'"]').prop('selected', true).prop('disabled', true);
                $('#withdrawal-update').attr('action', result.update_link);

                var getway_data = JSON.parse(result.getway_data);
                var html_string = "<small>";
                $.each(getway_data, function(k, v){
                    html_string += '<span class="font-weight-bolder">'+k.replace('_', ' ')+'</span> : '+ v+'<br>';
                });
                html_string += '</small>';
                $('#getway_data').html(html_string);

                if(result.status != 0) {
                    $('#comment').prop('disabled', true);
                    $('#status').prop('disabled', true);
                    $('#transaction_id').prop('disabled', true);
                    $('#withdrawal-update').find('*[type="submit"]').prop('disabled', true);
                }else{
                    $('#comment').prop('disabled', false);
                    $('#status').prop('disabled', false);
                    $('#transaction_id').prop('disabled', false);
                    $('#withdrawal-update').find('*[type="submit"]').prop('disabled', false);
                    $('#status option').not('#status option[value="'+result.status+'"]').prop('selected', false).prop('disabled', false);
                }
                $('#withdrawal-view').modal('show');
            },
            error: function(xhr) {
                $('.loader').fadeOut();
                if (xhr.status == 422) {
                    $.each(xhr.responseJSON.errors, function(k, v) {
                        form.after('<div class="text-danger">' + v + '</div>');
                    });
                } else if (xhr.status == 419 || xhr.status == 401 ||  xhr.status == 404) {
                    window.location.href = "";
                }
            }
        });
    });

    $('#withdrawal-update').submit(function(e) {
        $('.loader').fadeIn();
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var type = form.attr('method');
        form.find('.alert').remove();
        form.find('.text-danger').remove();
        $.ajax({
            url: url,
            type: type,
            dataType: 'JSON',
            data: form.serialize(),
            success: function(result) {
                $('.loader').fadeOut();
                // console.log(result);
                if (!result.status) {
                    form.find('.modal-body').append('<div class="alert alert-danger">' + result.message + '</div>');
                } else {
                    form.find('.modal-body').append('<div class="alert alert-success">' + result.message + '</div>');
                    // form[0].reset();
                    setTimeout(function(){
                        window.location.href = "";
                    }, 3000);
                }
            },
            error: function(xhr) {
                $('.loader').fadeOut();
                if (xhr.status == 422) {
                    $.each(xhr.responseJSON.errors, function(k, v) {
                        form.find('[name="' + k + '"]').after('<div class="text-danger">' + v + '</div>');
                    });
                } else if (xhr.status == 419 || xhr.status == 401 ||  xhr.status == 404) {
                    window.location.href = "";
                }
                // console.log(xhr);
            }
        });
    });


    var payouts_table = $('#payouts_table').DataTable({
        "mark" : true,
        "searching" : false,
        "bLengthChange" : false,
        "select":{
            "style":"single"
            // style:    'os',
            // selector: 'td:first-child'
        },
        "order": [
            [0, "desc"]
        ],
        "columnDefs": [
            {
                "className": "text-center align-middle", "targets": '_all'
            },
            // {
            //     "className": "text-center align-middle", "targets": [1, 2, 4, 5, 7]
            // },
            {
                "orderable": false, "targets": [-1]
            },
            // {
            //     "targets": [3],
            //     "data": "getway_data",
            //     "render": function ( data, type, row, meta ) {
            //         if(data === null) {
            //             // return JSON.parse(data);
            //             return '';
            //         }
            //         var obj = JSON.parse(data);
            //         var html_string = "<small>";
            //         $.each(obj, function(k, v){
            //             html_string += '<span class="font-weight-bolder">'+k.replace('_', ' ')+'</span> : '+ v+'<br>';
            //         });
            //         html_string += '</small>';
            //         return html_string;
            //     }
            // },
            {
                "targets": [0],
                "data": "amount",
                "render": function ( data, type, row, meta ) {
                    return '#' +data;
                }
            },
            {
                "targets": [1,2,3,4,5,6,7],
                "data": "amount",
                "render": function ( data, type, row, meta ) {
                    if(data === null) {
                        return '0 ' + currency;
                    }
                    return data+ ' ' +currency;
                }
            }
        ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "{{route('admin.user.payouts.datatable', request()->id)}}/",
            error: function(xhr) {
                $('.loader').fadeOut();
                if (xhr.status == 422) {
                    $.each(xhr.responseJSON.errors, function(k, v) {
                        form.after('<div class="text-danger">' + v + '</div>');
                    });
                } else if (xhr.status == 419 || xhr.status == 401) {
                    window.location.href = "";
                }
            }
        },
        "columns": [
            { "data": "id" },
            { "data": "join_commission" },
            { "data": "referral_commission" },
            { "data": "level_commission" },
            { "data": "regular_bonus" },
            { "data": "royalty_bonus" },
            { "data": "deduction" },
            { "data": "total_commission" },
            { "data": "run_at" },
            { "data": "action" }
        ]
    }).on('processing.dt', function(e, settings, processing) {
        $('div.dataTables_processing').css('display', 'none');
        if (processing) {
            $('.loader').fadeIn();
        } else {
            $('.loader').fadeOut();
        }
    });





});

</script>
@endsection
