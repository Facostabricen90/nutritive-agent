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
            'user_id' => 'required|exists:users,id',
            'patient_name' => 'required|string|max:255',
            'patient_document' => 'required|string|max:100',
            'patient_email' => 'required|email|max:255',
            'appointment_reason' => 'nullable|string|max:1000',
            'appointment_date' => [
                'required',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    // Validar que el slot esté disponible
                    $userId = $this->input('user_id');

                    $exists = \App\Models\Appointment::where('appointment_date', $value)
                        ->when($userId, function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        })
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
        // Agregar status por defecto si no viene
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'scheduled',
            ]);
        }
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
            'user_id.required' => 'Debes seleccionar un doctor.',
            'user_id.exists' => 'El doctor seleccionado no es válido.',
            'patient_name.required' => 'El nombre del paciente es obligatorio.',
            'patient_document.required' => 'La cédula del paciente es obligatoria.',
            'patient_email.required' => 'El correo del paciente es obligatorio.',
            'patient_email.email' => 'El correo electrónico no es válido.',
        ];
    }
}
