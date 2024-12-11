<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disponibilidad', function (Blueprint $table) {
            $table->boolean('estado_disponibilidad')->default(true)->after('hora_fin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disponibilidad', function (Blueprint $table) {
            $table->dropColumn('estado_disponibilidad');
        });
    }
};
