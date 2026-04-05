<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlatformController extends Controller
{
    public function index(): View
    {
        return view('platforms.index', [
            'platforms' => Platform::query()
                ->withCount('variables')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $name = trim($validated['name']);
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;

        if ($slug === '') {
            return back()->withInput()->withErrors([
                'name' => 'Nama platform tidak valid.',
            ]);
        }

        $counter = 2;
        while (Platform::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        Platform::query()->create([
            'name' => $name,
            'slug' => $slug,
        ]);

        return redirect()->route('platforms.index')->with('success', 'Platform berhasil ditambahkan.');
    }
}
