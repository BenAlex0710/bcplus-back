<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageOrder;
use App\Models\User;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $page_title = __('package.page_title_list');
        $packages = Package::get();
        return view('admin.packages.list', compact('page_title', 'packages'));
    }

    public function create()
    {
        $page_title = __('package.page_title_create');
        return view('admin.packages.create', compact('page_title'));
    }

    public function edit($id)
    {
        $package = Package::findorfail($id);
        $locale = session()->get('locale');
        $page_title = __('package.page_title_edit', ['package_name' => $package->{$locale . '_name'}]);
        return view('admin.packages.edit', compact('page_title', 'package'));
    }

    public function change_status(Request $request, $id)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];

        $package = Package::find($id);
        if (!$package) {
            return response()->json($request);
        }

        if ($package->status == '1') {
            $status = '0';
        } else {
            $status = '1';
        }

        $update = $package->update(['status' => $status]);
        if ($update) {
            $response = [
                'status' => true,
                'message' => 'Package status updated successfully.',
                'data' => []
            ];
        }
        return response()->json($response);
    }

    public function save(Request $request)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];
        $rules = [
            'price' => 'required|min:0',
            'events' => 'required|integer|min:1',
            'validity' => 'required|integer|min:1',
            'max_guests' => 'required|integer|min:0',
            'max_attendees' => 'required|integer|min:1',
            'ticket_commission' => 'required|min:1',
            'storage' => 'required|min:1',
            'video_quality' => 'required',
            'event_max_duration' => 'required|integer|min:1',
            'type' => 'required'
        ];

        foreach ($this->langs as $lang) {
            $rules[$lang . '_name'] = 'required';
        }

        $this->validate($request, $rules);

        $string = str_replace('(', '', $request->color);;
        $string = str_replace(')', '', $string);;
        $string = str_replace('rgb', '', $string);


        $package_data = [
            'price' => $request->price,
            'events' => $request->events,
            'max_guests' => $request->max_guests,
            'max_attendees' => $request->max_attendees,
            'ticket_commission' => $request->ticket_commission,
            'storage' => $request->storage,
            'video_quality' => $request->video_quality,
            'event_max_duration' => $request->event_max_duration,
            'validity' => $request->validity,
            'type' => $request->type,
            'status' => '0'
        ];
        foreach ($this->langs as $lang) {
            $package_data[$lang . '_name'] = $request->{$lang . '_name'};
        }

        $package = Package::create($package_data);

        if ($package) {
            $response['status'] = true;
            $response['message'] = __('package.created_success_message');
        }
        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];

        $rules = [
            'price' => 'required|min:0',
            'events' => 'required|integer|min:1',
            'validity' => 'required|integer|min:1',
            'max_guests' => 'required|integer|min:0',
            'max_attendees' => 'required|integer|min:1',
            'ticket_commission' => 'required|min:1',
            'storage' => 'required|min:1',
            'video_quality' => 'required',
            'event_max_duration' => 'required|integer|min:1',
            'type' => 'required'
        ];
        foreach ($this->langs as $lang) {
            $rules[$lang . '_name'] = 'required';
        }
        $this->validate($request, $rules);

        $string = str_replace('(', '', $request->color);;
        $string = str_replace(')', '', $string);;
        $string = str_replace('rgb', '', $string);;


        $package_data = [
            'price' => $request->price,
            'events' => $request->events,
            'max_guests' => $request->max_guests,
            'max_attendees' => $request->max_attendees,
            'ticket_commission' => $request->ticket_commission,
            'storage' => $request->storage,
            'video_quality' => $request->video_quality,
            'event_max_duration' => $request->event_max_duration,
            'validity' => $request->validity,
            'type' => $request->type,
        ];
        foreach ($this->langs as $lang) {
            $package_data[$lang . '_name'] = $request->{$lang . '_name'};
        }

        $package = Package::where('id', $id)->update($package_data);

        if ($package) {
            $response['status'] = true;
            $response['message'] =  __('package.updated_success_message');
        }
        return response()->json($response);
    }

    public function set_as_trial(Request $request, $id)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];

        $package = Package::find($id);
        if (!$package) {
            return response()->json($request);
        }

        $removed = Package::where('trial', '1')->update(['trial' => '0']);

        $update = $package->update(['trial' => '1']);

        if ($update) {
            $response = [
                'status' => true,
                'message' => 'Package status updated successfully.',
                'data' => []
            ];
        }
        return response()->json($response);
    }

    public function delete($id)
    {
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];
        $package  = Package::findorfail($id);

        if ($package->delete()) {
            $response['status'] = true;
            $response['message'] =  __('package.updated_success_message');
        }
        return response()->json($response);
    }

    public function package_orders_page()
    {
        $page_title = __('package.orders.page_title');
        $users = User::where('role', '2')->get();
        return view('admin.packages.package_orders', compact('page_title', 'users'));
    }

    public function package_orders_datatable(Request $request)
    {
        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        // $search = $request->search['value'];
        // $sort_by = $request->order[0]['column'];
        // $sort_direction = $request->order[0]['dir'];
        $package_orders_query = PackageOrder::with('user')->latest();
        //search
        // if (!empty($search)) {
        //     $package_orders_query->where('user_id', 'like', '%' . $search . '%');
        //     $package_orders_query->orWhere('package_data', 'like', '%' . $search . '%');
        // }

        //sorting
        // if ($sort_by == 0) {
        //     $package_orders_query->orderBy('id', $sort_direction);
        // } elseif ($sort_by == 1) {
        //     $package_orders_query->orderBy('user_id', $sort_direction);
        // } elseif ($sort_by == 3) {
        //     $package_orders_query->orderBy('amount', $sort_direction);
        // } elseif ($sort_by == 4) {
        //     $package_orders_query->orderBy('package_data', $sort_direction);
        // }

        if ($request->filled('users')) {
            $package_orders_query->whereHas('user', function ($query) use ($request) {
                $query->whereIn('id', explode(',', $request->users));
            });
        }
        if ($request->filled('payment_status')) {
            $package_orders_query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('status')) {
            $package_orders_query->where('status', $request->status);
        }

        $total_pages = $package_orders_query->count();
        $pages = $package_orders_query->limit($length)->offset($start)->get();
        $pages->each->append(['payment_status_label', 'status_label']);

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_pages,
            'recordsFiltered' => $total_pages,
            'data' => $pages,
        );
        // print_r($data);
        // print_r(mb_detect_order());
        // die;
        return response()->json($data);
    }
    /* 

    public function change_package_orders_status($id)
    {
        $order_id = $id;
        $purchaseRecord = PurchaseRecord::find($order_id);
        $package_data = json_decode($purchaseRecord->package_data);


        $user = User::find($purchaseRecord->user_id);
        $last_purchased = $user->package_orders()->orderBy('expiry_date', 'desc')->where('payment_status', '1')->first();
        if ($last_purchased && $last_purchased->expiry_date) {
            $start = new Carbon($last_purchased->expiry_date);
            $start = $start->addDay();
        } else {
            $start = Carbon::now();
        }
        $start_date = $start->format('Y-m-d');
        $expiry_date = $start->addDays($package_data->validity)->format('Y-m-d');
        $purchaseRecord->start_date = $start_date;

        $purchaseRecord->expiry_date = $expiry_date;

        $purchaseRecord->payment_status = "1";
        $purchaseRecord->payment_response = 'Manully updated';
        $purchaseRecord->save();
        $response['status'] = true;
        $response['message'] = 'Purchase record update succesfully.';
        return response()->json($response);
    } */
}
