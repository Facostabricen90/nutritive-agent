<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'appointment_date' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'status' => 'scheduled',
            'patient_name' => $this->faker->name(),
            'patient_document' => $this->faker->numerify('#########'),
            'patient_email' => $this->faker->safeEmail(),
            'appointment_reason' => $this->faker->sentence(8),
        ];
    }
}
