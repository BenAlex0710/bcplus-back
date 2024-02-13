<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait TranslateColums
{
    public $langs = [];
    public $request_lang;
    public $prefix;

    public function __construct(array $attributes = [])
    {

        $this->prefix = DB::getTablePrefix();
        $this->request_lang = session()->get('locale');
        $this->langs = get_app_language();

        if (isset($this->translateable_fields)) {
            $translateable_fields_columns = $this->addTranslateableColumnsToFillable();
            // $this->hidden = array_merge($this->hidden, $translateable_fields_columns);
            $this->appends = array_merge($this->appends, $this->translateable_fields);
        }
        parent::__construct($attributes);
    }

    public function addTranslateableColumnsToFillable()
    {
        $translateable_fields_columns = [];
        foreach ($this->translateable_fields as $column) {
            foreach ($this->langs as $lang) {
                array_push($this->fillable, $lang . '_' . $column);
                $translateable_fields_columns[] = $lang . '_' . $column;
            }
        }
        return $translateable_fields_columns;
    }
}
