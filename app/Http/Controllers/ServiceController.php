<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = auth()->user()->services()->orderBy('name')->get();
        return view('services.index', compact('services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'in:15,30,45,60,90,120'],
            'price'            => ['required', 'numeric', 'min:0'],
            'color'            => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        auth()->user()->services()->create($data + ['is_active' => true]);

        return back()->with('success', 'Servicio añadido correctamente.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        abort_if($service->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'in:15,30,45,60,90,120'],
            'price'            => ['required', 'numeric', 'min:0'],
            'color'            => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'is_active'        => ['sometimes', 'boolean'],
        ]);

        $service->update($data);

        return back()->with('success', 'Servicio actualizado.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        abort_if($service->user_id !== auth()->id(), 403);

        $service->delete();

        return back()->with('success', 'Servicio eliminado.');
    }
}
