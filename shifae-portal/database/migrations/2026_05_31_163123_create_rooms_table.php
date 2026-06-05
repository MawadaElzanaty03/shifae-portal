<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try{
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('roomNumber')->unique(); // رقم الحجرة
                $table->enum('roomStatus', ['Available', 'Occupied'])->default('Available');//حالة الحجرة
            $table->timestamps();
        });
        }
        catch (\Exception $exceptionError) {
            Log::error('Error creating rooms table: ' . $exceptionError->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
