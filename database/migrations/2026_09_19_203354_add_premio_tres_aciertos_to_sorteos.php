<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sorteos', function (Blueprint $table) {
            $table->decimal('premio_tres_aciertos', 10, 2)->default(10.00)->after('premio_cuatro_aciertos');
        });
    }

    public function down(): void
    {
        Schema::table('sorteos', function (Blueprint $table) {
            $table->dropColumn('premio_tres_aciertos');
        });
    }
};