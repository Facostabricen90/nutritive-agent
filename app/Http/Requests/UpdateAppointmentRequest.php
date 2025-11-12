<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appointment_date' => [
                'sometimes',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    
                    // Validar que el slot esté disponible (excluyendo la cita actual)
                    $exists = \App\Models\Appointment::where('appointment_date', $value)
                        ->where('status', '!=', 'canceled')
                        ->where('id', '!=', $this->route('appointment')->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Este horario ya está reservado. Por favor selecciona otro.');
                    }
                    
                    // Validar que sea un día permitido
                    $daysString = env('DAYS_AVAILABLE', '{Monday,Tuesday,Wednesday,Thursday,Friday}');
                    $daysString = trim($daysString, '{}');
                    $allowedDays = array_map('trim', explode(',', $daysString));
                    
                    $dayName = \Carbon\Carbon::parse($value)->format('l');
                    if (!in_array($dayName, $allowedDays)) {
                        $fail('Las citas solo están disponibles los siguientes días: ' . implode(', ', $allowedDays));
                    }
                    
                    // Validar que esté dentro del horario de negocio
                    $hour = \Carbon\Carbon::parse($value)->hour;
                    if ($hour < 8 || $hour >= 18) {
                        $fail('Las citas solo están disponibles entre las 8:00 AM y 6:00 PM.');
                    }
                },
            ],
            'status' => 'sometimes|in:scheduled,completed,canceled',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'appointment_date.date' => 'La fecha proporcionada no es válida.',
            'appointment_date.after' => 'La cita debe ser programada para una fecha futura.',
            'status.in' => 'El estado debe ser: scheduled, completed o canceled.',
        ];
    }
}
