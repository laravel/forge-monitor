<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('monitor_id');
            $table->string('monitor_type');
            $table->char('monitor_state', 7)->default('UNKNOWN');
            $table->timestamps();

            $table->index('created_at');
        });
    }
}
