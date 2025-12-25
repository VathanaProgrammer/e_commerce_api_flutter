<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('remember_token', 100)->nullable()->after('password_hash');
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('remember_token');
        });
    }
};