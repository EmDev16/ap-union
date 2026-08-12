<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Models\Faq;
use App\Models\FaqCategory;

class FaqManagementController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Faq::class, 'faq');
    }

    public function index()
    {
        return view('admin.faqs.index', ['faqs' => Faq::with('category')->latest()->get()]);
    }

    public function create()
    {
        return view('admin.faqs.create', ['categories' => FaqCategory::orderBy('name')->get()]);
    }

    public function store(StoreFaqRequest $request)
    {
        Faq::create($request->validated());

        return to_route('admin.faqs.index')->with('status', 'FAQ aangemaakt.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', ['faq' => $faq, 'categories' => FaqCategory::orderBy('name')->get()]);
    }

    public function update(UpdateFaqRequest $request, Faq $faq)
    {
        $faq->update($request->validated());

        return to_route('admin.faqs.index')->with('status', 'FAQ bijgewerkt.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return to_route('admin.faqs.index')->with('status', 'FAQ verwijderd.');
    }
}
