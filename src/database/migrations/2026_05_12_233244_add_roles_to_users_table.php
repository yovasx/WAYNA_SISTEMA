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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'rol')) {
                $table->string('rol', 30)->default('comprador')->after('password');
            }

            if (! Schema::hasColumn('users', 'estado')) {
                $table->string('estado', 20)->default('activo')->after('rol');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'rol')) {
                $table->dropColumn('rol');
            }

            if (Schema::hasColumn('users', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
