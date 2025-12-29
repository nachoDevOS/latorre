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
        Schema::create('service_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions');

            $table->datetime('start_time')->nullable();  
            $table->datetime('end_time')->nullable();

            $table->time('total_time')->nullable();

            $table->string('time_type')->nullable(); //Tiempo fijo, Tiempo sin límite

            $table->decimal('amount', 10, 2)->nullable();
            $table->smallInteger('status')->default(1);

            $table->timestamps();
            $table->timestamp('closed_at')->nullable();

            $table->foreignId('registerUser_id')->nullable()->constrained('users');
            $table->string('registerRole')->nullable();

            $table->softDeletes();
            $table->foreignId('deleteUser_id')->nullable()->constrained('users');
            $table->string('deleteRole')->nullable();
            $table->text('deleteObservation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_times');
    }
};
