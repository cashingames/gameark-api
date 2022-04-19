<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trivias', function (Blueprint $table) {
            //
            $table->dropColumn('start_time');
           
        });
        Schema::table('trivias', function (Blueprint $table) {
            //
            $table->dropColumn('end_time');

           
        });

        Schema::table('trivias', function (Blueprint $table){
            $table->dateTime('start_time');
            $table->dateTime('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trivias', function (Blueprint $table) {
            //
            $table->dropColumn('start_time');
            // $table->timestamp('start_time');
            // $table->timestamp('end_time');
        });

        Schema::table('trivias', function (Blueprint $table) {
            //
            $table->dropColumn('end_time');

           
        });
    }
};
