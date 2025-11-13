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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained('rooms');
            $table->foreignId('person_id')->nullable()->constrained('people');

            // $table->time('start_time')->nullable();  
            $table->datetime('start_time')->nullable();  

            $table->decimal('amount_room', 10, 2)->default(0);
            $table->decimal('amount_products', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('observation')->nullable();            

            $table->string('status')->default('vigente');
           
            $table->timestamps();            
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
        Schema::dropIfExists('services');
    }
};
