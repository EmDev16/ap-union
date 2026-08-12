<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFaqCategoryRequest;
use App\Http\Requests\UpdateFaqCategoryRequest;
use App\Models\FaqCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqCategoryManagementController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', FaqCategory::class);
        return view('admin.faq-categories.index', [
            'categories' => FaqCategory::withCount('faqs')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', FaqCategory::class);
        return view('admin.faq-categories.create');
    }

    public function store(StoreFaqCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', FaqCategory::class);
        FaqCategory::create($request->validated());

        return to_route('admin.faq-categories.index')->with('status', 'Categorie aangemaakt.');
    }

    public function edit(FaqCategory $faqCategory): View
    {
        $this->authorize('update', $faqCategory);
        return view('admin.faq-categories.edit', compact('faqCategory'));
    }

    public function update(UpdateFaqCategoryRequest $request, FaqCategory $faqCategory): RedirectResponse
    {
        $this->authorize('update', $faqCategory);
        $faqCategory->update($request->validated());

        return to_route('admin.faq-categories.index')->with('status', 'Categorie bijgewerkt.');
    }

    public function destroy(FaqCategory $faqCategory): RedirectResponse
    {
        $this->authorize('delete', $faqCategory);
        if ($faqCategory->faqs()->exists()) {
            return to_route('admin.faq-categories.index')
                ->with('error', 'Verwijder of verplaats eerst de FAQ’s in deze categorie.');
        }

        $faqCategory->delete();

        return to_route('admin.faq-categories.index')->with('status', 'Categorie verwijderd.');
    }
}
