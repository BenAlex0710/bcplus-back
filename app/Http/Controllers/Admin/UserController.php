<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index($type = "")
    {
        switch ($type) {
            case 'approved':
                $page_title = __('user.page_title_approved');
                break;
            case 'unapproved':
                $page_title = __('user.page_title_unapproved');
                break;
            case 'suspended':
                $page_title = __('user.page_title_suspended');
                break;
            default:
                $page_title = __('user.page_title_all_users');
                break;
        }
        return view('admin.users.index', compact('page_title'));
    }

    public function datatable(Request $request, $type = "")
    {
        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search = $request->search['value'];
        $sort_by = $request->order[0]['column'];
        $sort_direction = $request->order[0]['dir'];
        $users_query = User::select('*');

        if ($type == 'suspended') {
            $users_query->where('users.status', '2');
        } elseif ($type == 'unapproved') {
            $users_query->where('users.status', '0');
        } else if ($type == 'approved') {
            $users_query->where('users.status',  '1');
        }

        //search
        if (!empty($search)) {
            $users_query->where(function ($query) use ($search) {
                $query->orWhere('users.id', '=', $search);
                $query->orWhere('users.username', 'like', '%' . $search . '%');
                $query->orWhere('users.email', 'like', '%' . $search . '%');
            });
        }

        //sorting
        if ($sort_by == 0) {
            $users_query->orderBy('id', $sort_direction);
        } elseif ($sort_by == 1) {
            $users_query->orderBy('username', $sort_direction);
        } elseif ($sort_by == 2) {
            $users_query->orderBy('email', $sort_direction);
        } elseif ($sort_by == 3) {
            $users_query->orderBy('first_name', $sort_direction);
        } elseif ($sort_by == 4) {
            $users_query->orderBy('last_name', $sort_direction);
        } elseif ($sort_by == 5) {
            $users_query->orderBy('status', $sort_direction);
        }

        $total_users = $users_query->count();
        $users = $users_query->limit($length)->offset($start)->get();

        $users->each->append('action');
        // $users->each->append('gender_label');
        $users->each->append('status_label');

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_users,
            'recordsFiltered' => $total_users,
            'data' => $users
        );
        return response()->json($data);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $page_title = 'View Profile - ' . $user->username;
        return view('admin.users.show', compact('user', 'page_title'));
    }

    public function update(Request $request, $id)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id)
            ],
            'password' => 'nullable|min:8'
        ]);
        $response = [
            'status' => false,
            'message' => 'Something went wrong, please try again.',
            'data' => []
        ];
        $user = User::findOrFail($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->email = $request->email;
        if ($request->has('password') && !empty($request->password)) {
            $user->password = bcrypt($request->password);
        }
        if ($user->save()) {
            $response['status'] = true;
            $response['message'] = 'User updated successfully.';
        }
        return response()->json($response);
    }

    public function change_status(Request $request, $id, $status)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $response = [
            'status' => false,
            'message' => 'Something went wrong, please try again.'
        ];
        $user = User::findOrFail($id);
        $user->status = $status;
        if ($user->save()) {
            $response = [
                'status' => true,
                'message' => 'User status changed successfully.'
            ];
        }
        return response()->json($response);
    }

    public function update_password(Request $request, $id)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $this->validate($request, [
            'password' => 'required|min:8',
            'confirm_password' => 'same:password',
        ]);
        $response = [
            'status' => false,
            'message' => 'Something went wrong, please try again.',
            'data' => []
        ];
        $user = User::findorfail($id);
        $user->password = bcrypt($request->password);
        if ($user->save()) {
            $response['status'] = true;
            $response['message'] = 'User updated successfully.';
        }
        return response()->json($response);
    }
}
