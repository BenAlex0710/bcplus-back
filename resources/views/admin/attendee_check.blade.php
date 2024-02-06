@extends('layouts.dashboard')
@section('css')
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('breadcrumb')
    <h4 class="page-title-main">{{ __('common.sidebar.attendee_check') }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('attendees_report.dashboard') }}</a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li> -->
        <li class="breadcrumb-item active">{{ __('common.sidebar.attendee_check') }}</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title mb-3">{{ __('common.sidebar.attendee_check') }}</h4>
                <div class="mb-2 border border-secondary px-3 py-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div id="reader" class="center-block" style="width:300px;height:250px">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div id="message">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>
{{--    <script type="text/javascript" src="{{ asset('qr_scan/jsqrcode-combined.min.js') }}"></script>--}}
    <script type="text/javascript" src="{{ asset('qr_scan/html5-qrcode.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            var lastResult = 0;

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText !== lastResult) {
                    lastResult = decodedText;
                    $.ajax({
                        url: "{{ route('admin.attendee_check') }}",
                        type: 'POST',
                        dataType: 'JSON',
                        data: {attendee_id: decodedText},
                        success: function(resp) {
                            if (resp.success == true) {
                                $('#message').html(`
                                    <img src="${resp.attendee.photo}" height="200" width="200"/>
                                    <p class="text-info mt-2">Event: ${resp.event.title} </p>
                                    <p class="text-info">Date: ${resp.event.start_time} ~ ${resp.event.end_time} </p>
                                    <p class="text-info">Attendee: ${resp.attendee.name} </p>
                                    <p class="text-info">Email: ${resp.attendee.email} </p>
                                `);
                            }else{
                                Swal.fire(
                                    'No such attendee!',
                                    'There is no attendee with this qr code',
                                    'error'
                                );
                                $('#message').html('');
                            }
                        }
                    });

                }
            }

            var html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", { fps: 10, qrbox: 250 });
            html5QrcodeScanner.render(onScanSuccess);

            // $('#reader').html5_qrcode(function(data){
            //     $('#message').html('<span class="text-success send-true">Scanning now....</span>');
            //
            // },
            // function(error){
            //     $('#message').html('Scaning now ....'  );
            // }, function(videoError){
            //     $('#message').html('<span class="text-danger camera_problem"> there was a problem with your camera </span>');
            // }
            // );

        });
    </script>
@endsection
