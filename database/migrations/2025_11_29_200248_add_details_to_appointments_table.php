<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('patient_id')->nullable()->after('user_id'); // Cedula
            $table->string('patient_name')->nullable()->after('patient_id');
            $table->string('patient_email')->nullable()->after('patient_id');
            $table->text('appointment_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['patient_id', 'patient_name', 'patient_email', 'appointment_reason']);
        });
    }
};
