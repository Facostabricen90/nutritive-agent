<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'user_id' => 'sometimes|required|exists:users,id',
            'appointment_date' => [
                'required',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    // Validar que el slot esté disponible
                    $exists = \App\Models\Appointment::where('appointment_date', $value)
                        ->where('status', '!=', 'canceled')
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
                    
                    // Validar que esté dentro del horario de negocio (8 AM - 6 PM)
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Solo agregar user_id si no viene en la petición
        if (!$this->has('user_id')) {
            $this->merge([
                'user_id' => auth()->id(),
            ]);
        }
        
        // Agregar status por defecto si no viene
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'scheduled',
            ]);
        }
    }

    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Asegurar que user_id esté presente
        if (!isset($validated['user_id'])) {
            $validated['user_id'] = auth()->id();
        }
        
        // Asegurar que status esté presente
        if (!isset($validated['status'])) {
            $validated['status'] = 'scheduled';
        }
        
        return $validated;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'appointment_date.required' => 'La fecha y hora de la cita es obligatoria.',
            'appointment_date.date' => 'La fecha proporcionada no es válida.',
            'appointment_date.after' => 'La cita debe ser programada para una fecha futura.',
        ];
    }
}
