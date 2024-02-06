@extends('layouts.dashboard')
@section('breadcrumb')
    {{-- <link href="{{ asset('libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <h4 class="page-title-main">{{ __('support.admin.breadcrumb_name') }}</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('support.admin.breadcrumb_dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ __('support.admin.breadcrumb_name') }}</li>
    </ol>
@endsection
@section('content')
    <div class="card-box">
        <div class="d-flex">
            <div class="chat-sidebar">
                <ul class="list-group">
                    <li class="list-group-item">
                        <h4 class="header-title mb-0 text-success">{{ __('support.admin.support_performers') }}</h4>
                        <p class="text-info mb-0">Total {{ $performers->count() }} Performers</p>
                    </li>
                </ul>
                <ul class="list-group performers-list">
                    @foreach ($performers as $performer)
                        <li class="list-group-item p-2 list-group-item-action text-white" data-userinfo="{{ $performer->toJson() }}">
                            <div class="users-info d-flex align-items-center">
                                <div class="user-photo">
                                    <img class="rounded-circle" src="{{ $performer->photo }}">
                                </div>
                                <div class="user-details m-1">
                                    <p class="mb-0">{{ $performer->full_name }}</p>
                                    <p class="mb-0"><small>{{ '@' . $performer->username }}</small></p>
                                </div>
                                @if ($performer->support_messages_count > 0)
                                    <div class="message-count ml-auto bg-danger">{{ $performer->support_messages_count }}</div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="active-chat-container d-none">
                <ul class="list-group">
                    <li class="list-group-item active-chat-user-info d-flex align-items-center">
                        <div class="user-photo">
                            <img class="rounded-circle" src="{{ asset('admin/images/users/avatar-1.jpg') }}">
                        </div>
                        <div class="user-details mx-2">
                            <h4 class="header-title mb-1 text-success"></h4>
                            <p class="text-info mb-0"></p>
                        </div>
                    </li>
                    <li class="list-group-item p-2 chat-messages">
                        <div id="messages-list">
                            <div class="message-block d-flex mb-3">
                                <div class="photo">
                                    <img src="https://bcplusnews.com/bcadmin/admin/images/users/avatar-1.jpg">
                                </div>
                                <div class="text-block">
                                    <div class="message-text">
                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda possimus blanditiis voluptates inventore incidunt eius iure minus omnis facere ullam maxime ut necessitatibus eveniet enim, modi aliquid qui? Minima, cupiditate!
                                    </div>
                                    <span class="message-time"> Today 10:35</span>
                                </div>
                            </div>
                            <div class="message-block d-flex mb-3 inverse">
                                <div class="photo">
                                    <img src="https://bcplusnews.com/bcadmin/admin/images/users/avatar-1.jpg">
                                </div>
                                <div class="text-block">
                                    <div class="message-text">
                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda possimus blanditiis voluptates inventore incidunt eius iure minus omnis facere ullam maxime ut necessitatibus eveniet enim, modi aliquid qui? Minima, cupiditate!
                                    </div>
                                    <span class="message-time"> Today 10:35</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item p-2 chat-inputs">
                        <form name="send_message_form" id="send_message_form" action="{{ route('admin.support.send-message') }}" method="post">
                            <div class="d-flex">
                                <input type="hidden" name="user_id" value="">
                                <textarea class="form-control mr-2" name="message"></textarea>
                                <button type="submit" class="btn btn-info">{{ __('support.admin.send_btn') }}</button>
                            </div>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="start-chat start-chat d-flex justify-content-center align-items-center flex-grow-1">
                {{ __('support.admin.start_chat_message') }}
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('libs/select2/select2.min.js') }}"></script>
    <script type="text/javascript">
        var page = 1;
        var userinfo;

        function get_messages($, userinfo) {
            $.ajax({
                url: "{{ route('admin.support.messages') }}",
                type: 'get',
                dataType: 'JSON',
                data: {
                    user_id: userinfo.id,
                    page: page
                },
                success: function(result) {
                    console.log(result);
                    if (result.status) {
                        $.each(result.data, function(key, value) {
                            var photo = value.from == '0' ? "{{ asset('admin/images/users/avatar-1.jpg') }}" : userinfo.photo;
                            var inverseClass = value.from == '0' ? 'inverse' : '';

                            $('#messages-list').prepend('<div class="message-block d-flex mb-3 ' + inverseClass + '"><div class="photo"><img src="' + photo + '"></div><div class="text-block"><div class="message-text">' + value.message + '</div><span class="message-time"> ' + value.created_at + '</span></div></div>')
                        })
                        // $(".chat-messages").animate({
                        //     scrollTop: $('.chat-messages').prop("scrollHeight")
                        // }, 1000);
                    }
                }
            })
        }


        $(function() {

            $('.performers-list li').click(function() {
                $('.performers-list li').removeClass('list-group-item-secondary');
                $(this).addClass('list-group-item-secondary');
                $(this).find('.message-count').remove();
                userinfo = $(this).data('userinfo');
                // console.log(userinfo);
                $('.active-chat-user-info .user-photo img').attr('src', userinfo.photo);
                $('.active-chat-user-info .user-details h4').text(userinfo.full_name);
                $('.active-chat-user-info .user-details p').text(userinfo.username);
                var form = $('#send_message_form');
                form.find('[name="user_id"]').val(userinfo.id);
                form.find('[name="message"]').val('');
                $('#messages-list').html('');
                page = 1;
                get_messages($, userinfo);
                $('.active-chat-container').removeClass('d-none');
                $('.start-chat').removeClass('d-flex').addClass('d-none');
            });

            $('#send_message_form').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var msgInput = form.find('[name="message"]');
                if (msgInput.val().trim().length > 0) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'post',
                        dataType: 'JSON',
                        data: form.serialize(),
                        success: function(result) {
                            // console.log(result)
                            if (result.status) {
                                msgInput.val('');

                                var photo = "{{ asset('admin/images/users/avatar-1.jpg') }}";
                                $('#messages-list').append('<div class="message-block d-flex mb-3 inverse"><div class="photo"><img src="' + photo + '"></div><div class="text-block"><div class="message-text">' + result.data.message + '</div><span class="message-time"> ' + result.data.created_at + '</span></div></div>')

                                $(".chat-messages").animate({
                                    scrollTop: $('.chat-messages').prop("scrollHeight")
                                }, 1000);
                            }
                        }
                    })
                }
            })

            $('[name="message"]').keyup(function(e) {
                e.preventDefault();
                var code = e.keyCode || e.which;
                if (code == 13) {
                    $('#send_message_form').submit();
                }
            });

            $('.chat-messages').scroll(function(e) {
                if ($('.chat-messages').scrollTop() == 0) {
                    page++;
                    get_messages($, userinfo);
                }
            });

        });
    </script>
@endsection
