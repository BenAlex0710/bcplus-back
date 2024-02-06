<?php

namespace App\Models;

use App\Traits\TranslateColums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Page extends Model
{
    use TranslateColums;

    protected $fillable = [
        'slug',   'status', 'system_page',
    ];

    protected $translateable_fields = [
        'title', 'description'
    ];

    public function getTitleAttribute()
    {
        if (in_array($this->request_lang, $this->langs)) {
            return $this->{$this->request_lang . '_title'};
        }
        return $this->en_title;
    }
    public function getDescriptionAttribute()
    {
        if (in_array($this->request_lang, $this->langs)) {
            return $this->{$this->request_lang . '_description'};
        }
        return $this->en_description;
    }

    public function getActionAttribute()
    {
        $action_links = "";
        // if ($this->system_page != 1) {
        $action_links .= '<a href="' . route('admin.page.edit', $this->id) . '" class="text-info page_edit" data-toggle="tooltip" title="Edit Page"><i class="fe-edit"></i></a> &nbsp; ';
        // }
        // $action_links .= '<a target="_blank" href="' . route('view_page', $this->slug) . '" class="text-success" data-toggle="tooltip" title="view Page"><i class="fe-eye"></i></a>  &nbsp;';
        /* if ($this->status == "1") {
            $action_links .= '<a href="' . route('admin.page.change_status', $this->id) . '" class="text-danger change_status" data-type="Disable" data-toggle="tooltip" title="Disable Page"><i class="fe-x-circle"></i></a>  &nbsp;';
            // $action_links .= '<a href="javascript:void(0)" class="text-primary add_to_menu" data-id="'.$this->id.'"  data-parent_id="'.$this->parent_id.'" data-toggle="tooltip" title="Add To Menu"><i class="fe-menu"></i></a>  &nbsp;';
        } else {
            $action_links .= '<a href="' . route('admin.page.change_status', $this->id) . '" class="text-warning change_status" data-type="Enable" data-toggle="tooltip" title="Enable Page"><i class="fe-check-circle"></i></a>  &nbsp;';
        } */
        return $action_links;
    }

    public function getExcerptAttribute()
    {
        return mb_substr(strip_tags($this->description), 0, 30) . '...';
    }

    public function scopeActive($query)
    {
        return $query->where('status', "1");
    }

    public function scopecharLimit($query, $field, $limit)
    {
        return $query->addSelect(DB::raw("SUBSTRING(CONVERT({$field} USING utf8), 1,  {$limit}) as {$field}"));
    }


    //relations
    public function childern_pages()
    {
        return $this->hasMany('App\ModelsFmp\Page', 'parent_id', 'id');
    }
}
