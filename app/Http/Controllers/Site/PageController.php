<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StorePageRequest;
use App\Http\Requests\Site\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Support\Facades\DB;
use App\Actions\SanitizePageBlocks;
use App\Http\Requests\Site\UpdatePageContentRequest;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('position')->orderBy('title')->get();

        return view('site.pages.index', ['pages' => $pages]);
    }

    public function create()
    {
        return view('site.pages.form', ['page' => new Page()]);
    }

    public function store(StorePageRequest $request)
    {
        $data = $request->validated();
        $data['is_home'] = (bool) ($data['is_home'] ?? false);

        DB::transaction(function () use ($data) {
            $page = Page::create($data);
            $this->enforceSingleHome($page);
        });

        return redirect()->route('site.pages.index')->with('status', __('Page created.'));
    }

    public function edit(Page $page)
    {
        return view('site.pages.form', ['page' => $page]);
    }

    public function update(UpdatePageRequest $request, Page $page)
    {
        $data = $request->validated();
        $data['is_home'] = (bool) ($data['is_home'] ?? false);

        DB::transaction(function () use ($page, $data) {
            $page->update($data);
            $this->enforceSingleHome($page);
        });

        return redirect()->route('site.pages.index')->with('status', __('Page updated.'));
    }

    public function editContent(Page $page)
    {
        return view('site.pages.content', ['page' => $page]);
    }

    public function updateContent(UpdatePageContentRequest $request, Page $page, SanitizePageBlocks $sanitizer)
    {
        $data = $request->validated();
        $page->update([
            'content' => $sanitizer->execute($data['content']),
        ]);

        return redirect()->route('site.pages.edit-content', $page)
            ->with('status', __('Page content saved.'));
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('site.pages.index')->with('status', __('Page deleted.'));
    }

    /**
     * D131 — if this page is the home page, un-set is_home on all the tenant's other pages.
     * Last-one-wins. Tenant scope is applied by the BelongsToTenant trait.
     */
    private function enforceSingleHome(Page $page): void
    {
        if ($page->is_home) {
            Page::where('id', '!=', $page->id)
                ->where('is_home', true)
                ->update(['is_home' => false]);
        }
    }
}