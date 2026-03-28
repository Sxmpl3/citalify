<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function show(int $step): View|RedirectResponse
    {
        if ($step < 1 || $step > 3) {
            return redirect()->route('onboarding.step', 1);
        }

        return view("onboarding.step{$step}");
    }

    // Paso 1: datos del negocio
    public function storeStep1(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:100'],
            'business_slug' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:users,business_slug,' . auth()->id()],
            'phone'         => ['required', 'string', 'max:20'],
            'address'       => ['nullable', 'string', 'max:200'],
            'timezone'      => ['required', 'string', 'timezone'],
            'booking_days_ahead' => ['required', 'integer', 'in:14,28'],
        ]);

        auth()->user()->update($data);

        return redirect()->route('onboarding.step', 2);
    }

    // Paso 2: servicios
    public function storeStep2(Request $request): RedirectResponse
    {
        $request->validate([
            'services'                   => ['required', 'array', 'min:1'],
            'services.*.name'            => ['required', 'string', 'max:100'],
            'services.*.duration_minutes'=> ['required', 'integer', 'min:5', 'max:480'],
            'services.*.price'           => ['required', 'numeric', 'min:0'],
        ]);

        $user = auth()->user();

        foreach ($request->services as $svc) {
            Service::create([
                'user_id'          => $user->id,
                'name'             => $svc['name'],
                'duration_minutes' => $svc['duration_minutes'],
                'price'            => $svc['price'],
            ]);
        }

        return redirect()->route('onboarding.step', 3);
    }

    // Paso 3: horarios + finalizar
    public function storeStep3(Request $request): RedirectResponse
    {
        $request->validate([
            'schedules'            => ['required', 'array', 'min:1'],
            'schedules.*.day'      => ['required', 'integer', 'between:0,6'],
            'schedules.*.open'     => ['required', 'date_format:H:i'],
            'schedules.*.close'    => ['required', 'date_format:H:i', 'after:schedules.*.open'],
        ]);

        $user = auth()->user();

        // Crear empleado por defecto (el propio propietario)
        $employee = Employee::create([
            'user_id' => $user->id,
            'name'    => $user->business_name ?? $user->name,
        ]);

        foreach ($request->schedules as $sch) {
            $employee->schedules()->create([
                'day_of_week' => $sch['day'],
                'open_time'   => $sch['open'],
                'close_time'  => $sch['close'],
            ]);
        }

        $user->update(['onboarding_completed' => true]);

        return redirect()->route('dashboard')->with('success', '¡Bienvenido a citalia! Tu negocio ya está configurado.');
    }
}

