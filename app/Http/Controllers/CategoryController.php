<?php

namespace App\Http\Controllers;

use App\Triva\Model\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    //
    public function get(){
        return Category::all();
    }
}
