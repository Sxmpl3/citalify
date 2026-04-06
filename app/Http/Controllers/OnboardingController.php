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
            'schedule_type'        => ['required', 'string', 'in:normal,custom'],
            'schedules'            => ['required_if:schedule_type,normal', 'array'],
            'schedules.*.day'      => ['required_with:schedules', 'integer', 'between:0,6'],
            'schedules.*.open'     => ['required_with:schedules', 'date_format:H:i'],
            'schedules.*.close'    => ['required_with:schedules', 'date_format:H:i', 'after:schedules.*.open'],
            
            'custom_schedules'            => ['required_if:schedule_type,custom', 'array'],
            'custom_schedules.*.date'     => ['required_with:custom_schedules', 'date'],
            'custom_schedules.*.is_closed'=> ['required_with:custom_schedules', 'boolean'],
            'custom_schedules.*.open'     => ['nullable', 'date_format:H:i'],
            'custom_schedules.*.close'    => ['nullable', 'date_format:H:i', 'after:custom_schedules.*.open'],
        ]);

        $user = auth()->user();

        // Crear empleado por defecto (el propio propietario)
        $employee = Employee::create([
            'user_id' => $user->id,
            'name'    => $user->business_name ?? $user->name,
        ]);

        if ($request->schedule_type === 'normal' && $request->has('schedules')) {
            foreach ($request->schedules as $sch) {
                $employee->schedules()->create([
                    'day_of_week' => $sch['day'],
                    'open_time'   => $sch['open'],
                    'close_time'  => $sch['close'],
                ]);
            }
        } elseif ($request->schedule_type === 'custom' && $request->has('custom_schedules')) {
            foreach ($request->custom_schedules as $sch) {
                $employee->customSchedules()->create([
                    'date'       => $sch['date'],
                    'is_closed'  => $sch['is_closed'],
                    'open_time'  => $sch['is_closed'] ? null : ($sch['open'] ?? null),
                    'close_time' => $sch['is_closed'] ? null : ($sch['close'] ?? null),
                ]);
            }
        }

        $user->update([
            'onboarding_completed' => true,
            'schedule_type' => $request->schedule_type
        ]);

        return redirect()->route('dashboard')->with('success', '¡Bienvenido a citalify! Tu negocio ya está configurado.');
    }
}

