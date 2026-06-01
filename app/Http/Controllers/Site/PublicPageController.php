<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Tenant;
use Stancl\Tenancy\Tenancy;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublicPageController extends Controller
{
    public function home()
    {
        $this->assertTenantActive();

        // D132 — home is optional. No published home -> placeholder.
        $page = Page::where('is_home', true)->where('status', 'published')->first();
        if (! $page) {
            return view('site.public.placeholder');
        }

        return view('site.public.page', ['page' => $page]);
    }

    public function show(string $slug)
    {
        $this->assertTenantActive();

        // D125 — published only. The BelongsToTenant trait scopes by session('tenant_id').
        $page = Page::where('slug', $slug)->where('status', 'published')->first();
        if (! $page) {
            throw new NotFoundHttpException();
        }

        return view('site.public.page', ['page' => $page]);
    }

    /**
     * D125 — the public site only exists for active tenants. Pending/rejected -> 404.
     */
    private function assertTenantActive(): void
    {
        /** @var Tenant|null $tenant */
        $tenant = app(Tenancy::class)->tenant;
        if (! $tenant || $tenant->status !== 'active') {
            throw new NotFoundHttpException();
        }
    }
}