<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Muestra el formulario de edición del horario semanal (solo tipo normal).
     */
    public function edit(): View|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        if ($user->schedule_type !== 'normal') {
            return redirect()->route('dashboard')->with('error', 'Esta página solo está disponible para horario normal.');
        }

        $employee = $user->employees()->first();
        $schedules = $employee ? $employee->schedules()->orderBy('day_of_week')->get() : collect();

        return view('schedule.edit', compact('schedules'));
    }

    /**
     * Actualiza el horario semanal (solo tipo normal).
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        abort_if($user->schedule_type !== 'normal', 400, 'Solo disponible en Horario Normal');

        $request->validate([
            'schedules'               => ['required', 'array'],
            'schedules.*.day'         => ['required', 'integer', 'between:0,6'],
            'schedules.*.open'        => ['required', 'date_format:H:i'],
            'schedules.*.close'       => ['required', 'date_format:H:i', 'after:schedules.*.open'],
            'schedules.*.break_start' => ['nullable', 'date_format:H:i'],
            'schedules.*.break_end'   => ['nullable', 'date_format:H:i', 'after:schedules.*.break_start'],
        ]);

        $employee = $user->employees()->first();

        abort_if(!$employee, 422, 'No tienes un empleado configurado.');

        $employee->schedules()->delete();

        foreach ($request->schedules as $sch) {
            $employee->schedules()->create([
                'day_of_week' => $sch['day'],
                'open_time'   => $sch['open'],
                'close_time'  => $sch['close'],
                'break_start' => !empty($sch['break_start']) ? $sch['break_start'] : null,
                'break_end'   => !empty($sch['break_end']) ? $sch['break_end'] : null,
            ]);
        }

        return redirect()->route('schedule.edit')->with('success', '¡Horario actualizado correctamente!');
    }
}
