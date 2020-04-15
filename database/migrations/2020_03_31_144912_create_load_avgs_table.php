<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadAvgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_avgs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('load_avg');
            $table->unsignedInteger('load_avg_percent')->nullable();
            $table->unsignedInteger('cpus');
            $table->timestamps();

            $table->index('created_at');
        });
    }
}
