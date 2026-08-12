<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Models\Faq;
use App\Models\FaqCategory;

class FaqManagementController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Faq::class);
        return view('admin.faqs.index', ['faqs' => Faq::with('category')->latest()->get()]);
    }

    public function create()
    {
        $this->authorize('create', Faq::class);
        return view('admin.faqs.create', ['categories' => FaqCategory::orderBy('name')->get()]);
    }

    public function store(StoreFaqRequest $request)
    {
        $this->authorize('create', Faq::class);
        Faq::create($request->validated());

        return to_route('admin.faqs.index')->with('status', 'FAQ aangemaakt.');
    }

    public function edit(Faq $faq)
    {
        $this->authorize('update', $faq);
        return view('admin.faqs.edit', ['faq' => $faq, 'categories' => FaqCategory::orderBy('name')->get()]);
    }

    public function update(UpdateFaqRequest $request, Faq $faq)
    {
        $this->authorize('update', $faq);
        $faq->update($request->validated());

        return to_route('admin.faqs.index')->with('status', 'FAQ bijgewerkt.');
    }

    public function destroy(Faq $faq)
    {
        $this->authorize('delete', $faq);
        $faq->delete();

        return to_route('admin.faqs.index')->with('status', 'FAQ verwijderd.');
    }
}
