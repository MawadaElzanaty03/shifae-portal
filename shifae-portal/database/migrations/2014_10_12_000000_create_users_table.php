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
        Schema::create('users', function (Blueprint $table) {
          $table->id('userId');//primary key
          $table->string('fullName');// camelCase for column names
          $table->string('userName')->unique();//اسم فريد 
          $table->string('password');
          $table->string('userRole');
          $table->timestamps();//مفيد لمعرفة متى تم انشاء هذا الحساب و متى تم تعديل بياناته

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
