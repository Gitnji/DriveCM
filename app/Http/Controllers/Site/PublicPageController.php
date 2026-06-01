<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Tenant;
use App\Services\TenantSiteSettings;
use Stancl\Tenancy\Tenancy;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublicPageController extends Controller
{
    public function home(TenantSiteSettings $settings)
    {
        $tenant = $this->activeTenantOrFail();

        $page = Page::where('is_home', true)->where('status', 'published')->first();
        if (! $page) {
            return view('site.public.placeholder');
        }

        return view('site.public.page', $this->context($page, $tenant, $settings));
    }

    public function show(string $slug, TenantSiteSettings $settings)
    {
        $tenant = $this->activeTenantOrFail();

        $page = Page::where('slug', $slug)->where('status', 'published')->first();
        if (! $page) {
            throw new NotFoundHttpException();
        }

        return view('site.public.page', $this->context($page, $tenant, $settings));
    }

    private function activeTenantOrFail(): Tenant
    {
        /** @var Tenant|null $tenant */
        $tenant = app(Tenancy::class)->tenant;
        if (! $tenant || $tenant->status !== 'active') {
            throw new NotFoundHttpException();
        }
        return $tenant;
    }

    /**
     * Shared view context: tenant, settings, the page itself, and the nav list (published pages
     * ordered by position, home first).
     */
    private function context(Page $page, Tenant $tenant, TenantSiteSettings $settings): array
    {
        $navPages = Page::where('status', 'published')
            ->orderByDesc('is_home') // home first
            ->orderBy('position')
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'is_home']);

        return [
            'page' => $page,
            'tenant' => $tenant,
            'siteSettings' => $settings->get(),
            'navPages' => $navPages,
        ];
    }
}