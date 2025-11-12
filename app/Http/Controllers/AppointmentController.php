<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Appointments/Index', [
            'appointments' => Appointment::with('user')->orderBy('appointment_date', 'desc')->get()
        ]);
    }

    /**
     * Display the calendar view for appointments.
     */
    public function calendar()
    {
        $daysAvailable = $this->getAvailableDays();
        
        return Inertia::render('Appointments/Calendar', [
            'daysAvailable' => $daysAvailable,
            'appointmentDuration' => env('APPOINTMENT_DURATION_MINUTES', 20),
        ]);
    }

    /**
     * Get available appointment slots for the calendar.
     */
    public function getAvailableSlots(Request $request)
    {
        $start = Carbon::parse($request->input('start'));
        $end = Carbon::parse($request->input('end'));
        
        $availableSlots = $this->generateAvailableSlots($start, $end);
        $bookedAppointments = $this->getBookedAppointments($start, $end);
        
        return response()->json([
            'availableSlots' => $availableSlots,
            'bookedAppointments' => $bookedAppointments,
        ]);
    }

    /**
     * Generate available time slots based on configuration.
     */
    private function generateAvailableSlots(Carbon $start, Carbon $end)
    {
        $slots = [];
        $daysAvailable = $this->getAvailableDays();
        $duration = env('APPOINTMENT_DURATION_MINUTES', 20);
        
        // Define business hours (8 AM to 6 PM)
        $businessHoursStart = 8;
        $businessHoursEnd = 18;
        
        $currentDate = $start->copy()->startOfDay();
        
        while ($currentDate->lte($end)) {
            $dayName = $currentDate->format('l');
            
            if (in_array($dayName, $daysAvailable)) {
                // Generate time slots for this day
                $slotTime = $currentDate->copy()->setTime($businessHoursStart, 0);
                $endOfDay = $currentDate->copy()->setTime($businessHoursEnd, 0);
                
                while ($slotTime->lt($endOfDay)) {
                    // Check if slot is not already booked
                    $isBooked = Appointment::where('appointment_date', $slotTime->format('Y-m-d H:i:s'))
                        ->exists();
                    
                    if (!$isBooked && $slotTime->isFuture()) {
                        $slots[] = [
                            'title' => 'Available',
                            'start' => $slotTime->format('Y-m-d H:i:s'),
                            'end' => $slotTime->copy()->addMinutes($duration)->format('Y-m-d H:i:s'),
                            'backgroundColor' => '#10b981',
                            'borderColor' => '#059669',
                            'classNames' => ['available-slot'],
                            'extendedProps' => [
                                'type' => 'available',
                            ],
                        ];
                    }
                    
                    $slotTime->addMinutes($duration);
                }
            }
            
            $currentDate->addDay();
        }
        
        return $slots;
    }

    /**
     * Get booked appointments for the calendar.
     */
    private function getBookedAppointments(Carbon $start, Carbon $end)
    {
        $appointments = Appointment::with('user')
            ->whereBetween('appointment_date', [$start, $end])
            ->get();
        
        $duration = env('APPOINTMENT_DURATION_MINUTES', 20);
        
        return $appointments->map(function ($appointment) use ($duration) {
            $startTime = Carbon::parse($appointment->appointment_date);
            
            return [
                'id' => $appointment->id,
                'title' => 'Booked - ' . $appointment->user->name,
                'start' => $startTime->format('Y-m-d H:i:s'),
                'end' => $startTime->copy()->addMinutes($duration)->format('Y-m-d H:i:s'),
                'backgroundColor' => '#ef4444',
                'borderColor' => '#dc2626',
                'extendedProps' => [
                    'type' => 'booked',
                    'userId' => $appointment->user_id,
                    'status' => $appointment->status,
                ],
            ];
        })->toArray();
    }

    /**
     * Get the list of available days from environment configuration.
     */
    private function getAvailableDays()
    {
        $daysString = env('DAYS_AVAILABLE', '{Monday,Tuesday,Wednesday,Thursday,Friday}');
        // Remove curly braces and split by comma
        $daysString = trim($daysString, '{}');
        return array_map('trim', explode(',', $daysString));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Appointments/Create', [
            'daysAvailable' => $this->getAvailableDays(),
            'appointmentDuration' => env('APPOINTMENT_DURATION_MINUTES', 20),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        try {
            $appointment = Appointment::create($request->validated());
            
            // Si es una petición JSON (desde el calendario), retornar JSON
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cita creada exitosamente',
                    'appointment' => $appointment->load('user'),
                ], Response::HTTP_CREATED);
            }
            
            // Si es una petición normal, redirigir
            return redirect()->route('appointments.calendar')
                ->with('success', 'Cita creada exitosamente');
                
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la cita: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withErrors(['error' => 'Error al crear la cita']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return Inertia::render('Appointments/Show', [
            'appointment' => $appointment->load('user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        return Inertia::render('Appointments/Edit', [
            'appointment' => $appointment->load('user'),
            'daysAvailable' => $this->getAvailableDays(),
            'appointmentDuration' => env('APPOINTMENT_DURATION_MINUTES', 20),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        try {
            $appointment->update($request->validated());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cita actualizada exitosamente',
                    'appointment' => $appointment->load('user'),
                ], Response::HTTP_OK);
            }
            
            return redirect()->route('appointments.index')
                ->with('success', 'Cita actualizada exitosamente');
                
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la cita: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withErrors(['error' => 'Error al actualizar la cita']);
        }
    }

    /**
     * Cancel an appointment (soft delete by changing status).
     */
    public function cancel(Appointment $appointment)
    {
        try {
            $appointment->update(['status' => 'canceled']);
            
            return response()->json([
                'success' => true,
                'message' => 'Cita cancelada exitosamente',
                'appointment' => $appointment,
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la cita: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        try {
            $appointment->delete();
            
            return redirect()->route('appointments.index')
                ->with('success', 'Cita eliminada exitosamente');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la cita']);
        }
    }
}
