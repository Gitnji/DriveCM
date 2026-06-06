<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentApplicationRequest;
use App\Models\Page;
use App\Models\StudentApplication;
use App\Models\Tenant;
use App\Services\TenantSiteSettings;
use Stancl\Tenancy\Tenancy;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StudentRegistrationController extends Controller
{
    public function create(TenantSiteSettings $settings)
    {
        return view('register.form', $this->publicContext($settings));
    }

    public function store(StudentApplicationRequest $request)
    {
        $tenant = $this->activeTenantOrFail();
        $data = $request->validated();
        unset($data['website']); // honeypot — never persisted

        StudentApplication::create([
            ...$data,
            'tenant_id'    => $tenant->id,
            'source'       => 'public_form',
            'status'       => 'pending',
            'submitted_at' => now(),
        ]);

        return redirect()->route('register.submitted');
    }

    public function submitted(TenantSiteSettings $settings)
    {
        return view('register.submitted', $this->publicContext($settings));
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
     * Same shape as PublicPageController::context(), minus the $page (no CMS page on these
     * routes). The public layout's $page uses are all null-safe, so passing null works.
     */
    private function publicContext(TenantSiteSettings $settings): array
    {
        $tenant = $this->activeTenantOrFail();
        $navPages = Page::where('status', 'published')
            ->orderByDesc('is_home')
            ->orderBy('position')
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'is_home']);

        return [
            'page'         => null,
            'tenant'       => $tenant,
            'siteSettings' => $settings->get(),
            'navPages'     => $navPages,
        ];
    }
}