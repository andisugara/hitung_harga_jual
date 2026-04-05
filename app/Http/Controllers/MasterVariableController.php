<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterVariableController extends Controller
{
    public function index(): View
    {
        return view('master-variables.index', [
            'platforms' => Platform::query()->withCount('variables')->orderBy('id')->get(),
        ]);
    }

    public function show(Platform $platform): View
    {
        return view('master-variables.show', [
            'platform' => $platform->load('variables'),
        ]);
    }

    public function edit(Platform $platform): View
    {
        return view('master-variables.edit', [
            'platform' => $platform->load('variables'),
        ]);
    }

    public function update(Request $request, Platform $platform): RedirectResponse
    {
        $validated = $request->validate([
            'variables' => ['required', 'array', 'min:1'],
            'variables.*.variable' => ['required', 'string', 'max:255'],
            'variables.*.type' => ['required', 'in:percent,amount'],
            'variables.*.value' => ['required', 'numeric', 'min:0'],
        ]);

        $percentTotal = collect($validated['variables'])
            ->where('type', 'percent')
            ->sum(fn($row) => (float) $row['value']);

        if ($percentTotal >= 100) {
            return back()->withInput()->withErrors([
                'variables' => 'Total potongan persen tidak boleh >= 100%.',
            ]);
        }

        $platform->variables()->delete();

        $platform->variables()->createMany(
            collect($validated['variables'])
                ->map(fn($row) => [
                    'variable' => $row['variable'],
                    'type' => $row['type'],
                    'value' => $row['value'],
                ])
                ->values()
                ->all()
        );

        return redirect()->route('master-variables.show', $platform)->with('success', 'Master variable berhasil diperbarui.');
    }
}
