<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixDecimalColumnsForStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disk_usages', function (Blueprint $table) {
            $table->decimal('free', 5, 2)->change();
            $table->decimal('used', 5, 2)->change();
        });

        Schema::table('load_avgs', function (Blueprint $table) {
            $table->decimal('load_avg', 8, 4)->change();
            $table->decimal('load_avg_percent', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disk_usages', function (Blueprint $table) {
            $table->unsignedInteger('free')->change();
            $table->unsignedInteger('used')->change();
        });

        Schema::table('load_avgs', function (Blueprint $table) {
            $table->unsignedInteger('load_avg')->change();
            $table->unsignedInteger('load_avg_percent')->nullable()->change();
        });
    }
}
