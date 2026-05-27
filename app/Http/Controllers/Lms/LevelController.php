<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\UpdateLevelRequest;
use App\Models\Level;

class LevelController extends Controller
{
    public function index()
    {
        // BelongsToTenant trait scopes to the session tenant automatically.
        $levels = Level::orderBy('position')->get();

        return view('lms.levels.index', ['levels' => $levels]);
    }

    public function update(UpdateLevelRequest $request, Level $level)
    {
        // Route-model binding gives us $level. The tenant global scope means
        // a level from another tenant would 404 before reaching here.
        $level->update($request->validated());

        return redirect()
            ->route('lms.levels.index')
            ->with('status', __('Level updated.'));
    }
}