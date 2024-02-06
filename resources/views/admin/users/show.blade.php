@extends('layouts.dashboard')
@section('breadcrumb')
    <h4 class="page-title-main">{{ __('users_common.user_info') }} <span class="text-primary font-weight-bolder">({{ $user->username }})</span></h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('users_common.dashboard') }}</a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li> -->
        <li class="breadcrumb-item active">{{ __('users_common.user_info') }}</li>
    </ol>
@endsection
@section('content')
    @php

    $user_info = $user->basic_info;
    $personality_tag = $user->personality_tags;
    @endphp
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box py-0 d-flex justify-content-between">
                <div class="text-left align-self-center">
                    {{ __('users_common.action_butons') }}
                </div>
                <div class="my-2 text-right">
                    @if ($user->status == '1' || $user->status == '3')
                        <a href="{{ route('admin.user.change_status', [$user->id, '0']) }}" class="btn btn-warning change_status" title="Unapprove"><i class="fe-x-circle"></i> {{ __('users_common.unapprove') }}</a> &nbsp;
                        <a href="{{ route('admin.user.change_status', [$user->id, '2']) }}" class="btn btn-success change_status" title="Approve"><i class="fe-check-circle"></i>{{ __('users_common.approve') }} </a> &nbsp;
                    @endif
                    @if ($user->status != '3')
                        <a href="{{ route('admin.user.change_status', [$user->id, '3']) }}" class="btn btn-danger change_status" title="Suspend"><i class="fe-x-circle"></i>{{ __('users_common.suspend') }} </a> &nbsp;
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-box">
                <h4 class="header-title mb-3 text-primary">{{ __('users_common.personal_info') }}</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="personal_info">
                        <tr>
                            <th>{{ __('users_common.id') }}</th>
                            <td>#{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('users_common.username') }}</th>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('users_common.first_name') }}</th>
                            <td>{{ $user->first_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('users_common.last_name') }}</th>
                            <td>{{ $user->last_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('users_common.full_name') }}</th>
                            <td>{{ $user->full_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('users_common.email') }}</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('users_common.gender') }}</th>
                            <td>{!! $user->gender_label !!}</td>
                        </tr>
                        <tr>
                            <th>{{ __('users_common.dob') }}</th>
                            <td>{{ $user->birthday }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('users_common.joining_time') }}</th>
                            <td>{{ $user->joining_time }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-box">
                <h4 class="header-title mb-3 text-primary">{{ __('users_common.basic_info') }}</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="basic_info">
                        @if (!empty($user_info))
                            <tr>
                                <th>{{ __('users_common.height') }}</th>
                                <td>{{ $user_info->height }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.weight') }}</th>
                                <td>{{ $user_info->weight }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.body_type') }}</th>
                                <td>{{ $user->option_name($user_info->body_type) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.living_area') }}</th>
                                <td>{{ $user->option_name($user_info->living_area) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.carrer_type') }}</th>
                                <td>{{ $user->option_name($user_info->career_type) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.highest_education') }}</th>
                                <td>{{ $user->option_name($user_info->highest_education) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.preferred_language') }}</th>
                                <td>{{ $user->option_name($user_info->preferred_language) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.relationship_status') }}</th>
                                <td>{{ $user->option_name($user_info->relationship_status) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.smoking_habits') }}</th>
                                <td>{{ $user->option_name($user_info->smoking_habit) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.drnking_habits') }}</th>
                                <td>{{ $user->option_name($user_info->drinking_habit) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.personality_tag') }}</th>
                                <td>
                                    @foreach ($personality_tag as $tag)
                                        {{ $user->option_name($tag->tag_id) . ',' }}
                                    @endforeach

                                </td>
                            </tr>
                        @else
                            <td colspan="2" class="text-center">{{ __('users_common.user_info_empty_msg') }}</td>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card-box">
                <h4 class="header-title mb-3 text-primary">{{ __('users_common.profile_images') }}</h4>

                <div class="row">
                    @if ($user->profile_pictures()->exists())
                        @foreach ($user->profile_pictures as $key => $profile_picture)
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <img src="{{ $profile_picture->image['url'] }}">
                                <div class="aprove_unapprove_link text-center py-2">
                                    @if ($profile_picture->status == 0)
                                        <a href="{{ route('admin.user.profile_picture.approve', $profile_picture->id) }}" class="btn btn-sm btn-success mr-1 approve_image" data-toggle="tooltip" title="Approve"><i class="fe-check-circle"></i></a>
                                        <a href="{{ route('admin.user.profile_picture.reject', $profile_picture->id) }}" class="btn btn-sm btn-danger reject_image" data-toggle="tooltip" title="Unapprove"><i class="fe-x-circle"></i></a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-md-12">
                            {{ __('users_common.user_info_empty_msg') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
        <div class="col-lg-6">
            <div class="card-box">
                <h4 class="header-title mb-3 text-primary">{{ __('users_common.dateing_related_info') }}</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="dating_related_info">
                        @if (!empty($user_info))
                            <tr>
                                <th>>{{ __('users_common.about_yourself') }}</th>
                                <td>{{ $user_info->about_yourself }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.idel_date') }}</th>
                                <td>{{ $user_info->ideal_date }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.date_budget') }}</th>
                                <td>{{ $user_info->option_name('date_budget') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('users_common.idel_date_description') }} </th>
                                <td>{!! $user_info->ideal_date_description !!}</td>
                            </tr>
                        @else
                            <td colspan="2" class="text-center">{{ __('users_common.user_info_empty_msg') }}</td>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('js')
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>
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
                                btn.parents('tr').remove();
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
                                $(btn).parent().hide();
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
                                location.reload();
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
    </script>
@endsection
