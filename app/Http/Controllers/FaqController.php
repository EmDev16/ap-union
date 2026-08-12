<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;

class FaqController extends Controller
{
    public function index()
    {
        $categories = FaqCategory::with('faqs')->orderBy('name')->get();
        return view('faqs.index', compact('categories'));
    }
}
