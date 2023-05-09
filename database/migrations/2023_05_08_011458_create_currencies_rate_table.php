<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies_rate_history', function (Blueprint $table) {
            $table->id();
            $table->string('char_code');
            $table->date('date');
            $table->float('rate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies_rate_history');
    }
};
