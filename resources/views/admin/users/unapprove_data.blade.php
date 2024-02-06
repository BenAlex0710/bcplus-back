@extends('layouts.dashboard')
@section('breadcrumb')
<h4 class="page-title-main">{{ __('users_common.user_info')}} <span class="text-primary font-weight-bolder">({{$user->username}})</span></h4>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{ __('users_common.dashboard')}}</a></li>
    <!-- <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li> -->
    <li class="breadcrumb-item active">{{ __('users_common.user_info')}}</li>
</ol>
@endsection
@section('content')
@php
$user_info = $user->basic_info;
$personality_tag = $user->personality_tags;

@endphp

<div class="row">
    @if(!empty($user_data['height']))
    <div class="col-lg-6">
        <div class="card-box">
            <h4 class="header-title  text-primary">
                <div class="mb-3 float-left">{{ __('users_common.basic_info')}}</div><a href="{{route('admin.user.unapprove_data.basic_info', $user->id)}}" class="btn btn-success mb-3  float-right btn-sm Approve_basic_info" data-toggle="tooltip" title="Approve"><i class="fe-check-circle"></i></a><a href="{{route('admin.user.unapprove_data.reject_basic_info', $user->id)}}" class="btn btn-danger mb-3  float-right btn-sm reject_basic_info" data-toggle="tooltip" title="Reject"><i class="fe-x-circle"></i></a>
            </h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="basic_info">
                    <?php //print_r($user_data); die;
                    ?>
                    <tr>
                        <th>{{ __('users_common.height')}}</th>
                        <td>{{ $user_data['height']}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.weight')}}</th>
                        <td>{{ $user_data['weight']}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.body_type')}}</th>
                        <td>{{$user->option_name($user_data['body_type'])}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.living_area')}}</th>
                        <td>{{$user->option_name($user_data['living_area'])}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.carrer_type')}}</th>
                        <td>{{$user->option_name($user_data['career_type'])}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.highest_education')}}</th>
                        <td>{{$user->option_name($user_data['highest_education'])}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.preferred_language')}}</th>
                        <td>{{$user->option_name($user_data['preferred_language'])}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.relationship_status')}}</th>
                        <td>{{$user->option_name($user_data['relationship_status'])}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.smoking_habits')}}</th>
                        <td>{{$user->option_name($user_data['smoking_habit'])}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.drnking_habits')}}</th>
                        <td>{{$user->option_name($user_data['drinking_habit'])}}</td>
                    </tr>
                    <!--  <tr>
                    <th>{{ __('users_common.personality_tag')}}</th>
                    <td> @foreach($personality_tag as $tag)
                        {{$user->option_name($tag->tag_id).','}}
                        @endforeach

                    </td>
                </tr> -->
                </table>
            </div>
        </div>
    </div>
    @endif
    @if(!$profile_pictures->isEmpty())
    <div class="col-lg-6">
        <div class="card-box">
            <h4 class="header-title mb-3 text-primary">{{ __('users_common.profile_images')}}</h4>
            <div class="row">
                @foreach($profile_pictures as $key=>$profile_picture)
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <img src="{{$profile_picture->image['url']}}">
                    <div class="aprove_unapprove_link text-center py-2">
                        @if($profile_picture->status==0)
                        <a href="{{route('admin.user.profile_picture.approve', $profile_picture->id)}}" class="btn btn-sm btn-success mr-1 approve_image" data-toggle="tooltip" title="Approve"><i class="fe-check-circle"></i></a>
                        <a href="{{route('admin.user.profile_picture.reject', $profile_picture->id)}}" class="btn btn-sm btn-danger reject_image" data-toggle="tooltip" title="Unapprove"><i class="fe-x-circle"></i></a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if(!empty($user_data['about_yourself']))
    <div class="col-lg-6">
        <div class="card-box">
            <h4 class="header-title text-primary">
                <div class="mb-3 float-left">{{ __('users_common.dateing_related_info')}}</div><a href="{{route('admin.user.unapprove_data.dateing_info', $user->id)}}" class="btn btn-success mb-3  float-right btn-sm Approve_dating_info" data-toggle="tooltip" title="Approve"><i class="fe-check-circle"></i></a>
                <a href="{{route('admin.user.unapprove_data.reject_dateing_info', $user->id)}}" class="btn btn-danger mb-3  float-right btn-sm reject_dateing_info" data-toggle="tooltip" title="Reject"><i class="fe-x-circle"></i></a>
            </h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="dating_related_info">
                    <tr>
                        <th>{{ __('users_common.about_yourself')}}</th>
                        <td>{{$user_data['about_yourself']}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.idel_date')}}</th>
                        <td>{{$user_data['ideal_date']}}</td>
                    </tr>

                    <tr>
                        <th>{{ __('users_common.idel_date_description')}}</th>
                        <td>{!!$user_data['ideal_date_description'] !!}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.date_budget')}}</th>
                        <td>{{$user->option_name($user_data['date_budget'])}}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users_common.date_image')}}</th>
                        <td><img src="{{($user_data['ideal_date_image'])}}"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @endif
    @if(!empty($user_data['tag_id']))
    <div class="col-lg-6">
        <div class="card-box">
            <h4 class="header-title text-primary">
                <div class="mb-3 float-left ">{{ __('users_common.personality_tag')}}</div><a href="{{route('admin.user.unapprove_data.personality_tag', $user->id)}}" class="btn btn-success mb-3  float-right btn-sm Approve_personality_tag" data-toggle="tooltip" title="Approve"><i class="fe-check-circle"></i></a>
                <a href="{{route('admin.user.unapprove_data.reject_personality_tag', $user->id)}}" class="btn btn-danger mb-3  float-right btn-sm reject_personality_tag" data-toggle="tooltip" title="Reject"><i class="fe-x-circle"></i></a>
            </h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="personality_tags">
                    @foreach($user_data['tag_id'] as $key=>$value)
                    <tr>
                        <th>{{$key+1}}</th>
                        <td>{{$user->option_name($value)}}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection
@section('css')
<link href="{{asset('libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('js')
<script src="{{asset('libs/sweetalert2/sweetalert2.min.js')}}"></script>
<script type="text/javascript">
    $(document).on('click', '.reject_image', function(e) {
        e.preventDefault();
        var btn = $(this);
        var url = btn.attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to reject this profile picture, if you confirm image will be deleted from server too.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Reject it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'delete',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            btn.parents('.col-xs-6').hide();
                            Swal.fire(
                                'Rejected!',
                                'Profile picture rejected successfully.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                res.message,
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

    $(document).on('click', '.approve_image', function(e) {
        e.preventDefault();
        var btn = $(this);
        var url = btn.attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to Approve this profile picture, if you confirm image will be Approve from server too.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            // $(btn).parent().hide();
                            // btn.parents('tr').remove();
                            btn.parents('.col-xs-6').hide();
                            Swal.fire(
                                'Approved!',
                                'Profile picture Approved successfully.',
                                'success'
                            );

                        } else {
                            Swal.fire(
                                'Error!',
                                res.message,
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

    $(document).on('click', '.Approve_basic_info', function(e) {
        e.preventDefault();
        var btn = $(this);
        var url = btn.attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to Approve this Basic Information , if you confirm then this info will save in user information.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Approved it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            $('#basic_info').hide();
                            Swal.fire(
                                'Approved!',
                                'Your basic info is Approved successfully.',
                                'success'
                            );

                        } else {
                            Swal.fire(
                                'Error!',
                                res.message,
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

    $(document).on('click', '.reject_basic_info', function(e) {
        e.preventDefault();
        var btn = $(this);
        var url = btn.attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to Unapprove this Basic Information , if you confirm then this info will not save in user information.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Unpproved it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            $('#basic_info').hide();
                            Swal.fire(
                                'Unapproved!',
                                'Your basic info is Unapproved successfully.',
                                'success'
                            );

                        } else {
                            Swal.fire(
                                'Error!',
                                res.message,
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

    $(document).on('click', '.Approve_dating_info', function(e) {
        e.preventDefault();
        var btn = $(this);
        var url = btn.attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to Approve this Dateing Info, if you confirm then this info will save in user information.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            $('#dating_related_info').hide();
                            Swal.fire(
                                'Approved!',
                                'User Dateing Information Approved successfully.',
                                'success'
                            );

                        } else {
                            Swal.fire(
                                'Error!',
                                res.message,
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


    $(document).on('click', '.reject_dateing_info', function(e) {
        e.preventDefault();
        var btn = $(this);
        var url = btn.attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to Unpprove this Dateing Info, if you confirm then this info will not update user information.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Unapprove it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            $('#dating_related_info').hide();
                            Swal.fire(
                                'Approved!',
                                'User Dateing Information Unpproved successfully.',
                                'success'
                            );

                        } else {
                            Swal.fire(
                                'Error!',
                                res.message,
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

    $(document).on('click', '.Approve_personality_tag', function(e) {
        e.preventDefault();
        var btn = $(this);
        var url = btn.attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to Approve this Personality Tags, if you confirm then this info will save in user information.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            $('#personality_tags').hide();
                            Swal.fire(
                                'Approved!',
                                'User Personality tag updated successfully.',
                                'success'
                            );

                        } else {
                            Swal.fire(
                                'Error!',
                                res.message,
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
    $(document).on('click', '.reject_personality_tag', function(e) {
        e.preventDefault();
        var btn = $(this);
        var url = btn.attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to Unapprove this Personality Tags, if you confirm then this info will notsave in user information.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Unapprove it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            $('#personality_tags').hide();
                            Swal.fire(
                                'Unapproved!',
                                'User Personality tag Unapproved successfully.',
                                'success'
                            );

                        } else {
                            Swal.fire(
                                'Error!',
                                res.message,
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
</script>
@endsection