<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $page_title = __('page.page_list_title');
        return view('admin.page.index', compact('page_title'));
    }

    public function create()
    {
        $page_title = __('page.create_page_title');
        return view('admin.page.create', compact('page_title'));
    }

    public function save(Request $request)
    {
        if (!$request->ajax()) {
            return abort(404);
        }

        $response = [
            'status' => false,
            'message' =>  __('common.errors.something'),
            'data' => []
        ];

        $rules = [];
        foreach ($this->langs as $lang) {
            $rules[$lang . '_title'] = 'required|unique:pages,en_title';
            $rules[$lang . '_description'] = 'required';
        }

        $this->validate($request, $rules);

        $page_data = [
            'slug' => Str::slug($request->en_title),
        ];
        foreach ($this->langs as $lang) {
            $page_data[$lang . '_title'] = $request->{$lang . '_title'};
            $page_data[$lang . '_description'] = $request->{$lang . '_description'};
        }

        $page = Page::create($page_data);

        if ($page) {
            $response['status'] = true;
            $response['message'] = __('page.created_success_message');;
        }
        return response()->json($response);
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        $page_title = __('page.edit_page_title', ['title' => $page->title]);
        return view('admin.page.edit', compact('page_title', 'page'));
    }

    public function update(Request $request, $id)
    {
        if (!$request->ajax()) {
            return abort(404);
        }

        $response = [
            'status' => false,
            'message' =>  __('page.invalid_page_id'),
            'data' => []
        ];

        $rules = [];
        foreach ($this->langs as $lang) {
            $rules[$lang . '_title'] = 'required|unique:pages,en_title,' . $id;
            $rules[$lang . '_description'] = 'required';
        }

        $this->validate($request, $rules);

        $page = Page::find($id);

        $page_data = [
            'slug' => Str::slug($request->en_title),
        ];
        foreach ($this->langs as $lang) {
            $page_data[$lang . '_title'] = $request->{$lang . '_title'};
            $page_data[$lang . '_description'] = $request->{$lang . '_description'};
        }

        $page->update($page_data);
        if ($page) {
            $response['status'] = true;
            $response['message'] = __('page.update_success_message');
        }
        return response()->json($response);
    }


    public function change_status(Request $request, $id)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $response = [
            'status' => false,
            'message' =>  __('common.errors.something'),
            'data' => []
        ];
        $page = Page::find($id);
        $page->status = !$page->status ? '1' : '0';
        $page->save();
        if ($page) {
            $response['status'] = true;
            $response['message'] =  __('page.update_success_message');
        }
        return response()->json($response);
    }

    public function datatable(Request $request)
    {
        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search = $request->search['value'];
        $sort_by = $request->order[0]['column'];
        $sort_direction = $request->order[0]['dir'];
        $pages_query = Page::select('*')->charLimit('en_description', '1000');
        // $pages_query->addSelect(DB::raw("SUBSTRING('description', 0, 1000) as dsd"));
        //search
        if (!empty($search)) {
            $pages_query->where('title', 'like', '%' . $search . '%');
            $pages_query->orWhere('description', 'like', '%' . $search . '%');
        }
        //sorting
        if ($sort_by == 0) {
            $pages_query->orderBy('id', $sort_direction);
        } elseif ($sort_by == 1) {
            $pages_query->orderBy('title', $sort_direction);
        } elseif ($sort_by == 3) {
            $pages_query->orderBy('slug', $sort_direction);
        } elseif ($sort_by == 4) {
            $pages_query->orderBy('parent', $sort_direction);
        }

        $total_pages = $pages_query->count();
        $pages = $pages_query->limit($length)->offset($start)->get();
        $pages->each->append('action');
        $pages->each->append('excerpt');
        // $pages->each->append('sponsor');

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
}
