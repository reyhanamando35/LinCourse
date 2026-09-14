<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_detail_id')->constrained('student_details')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('month_year', 7); // Format: YYYY-MM
            $table->string('payment_proof')->nullable(); // Path ke file bukti bayar
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->comment('Admin ID')->constrained('admins')->onDelete('set null');
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
