<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemoryUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memory_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('total');
            $table->unsignedInteger('available');
            $table->unsignedInteger('used');
            $table->unsignedInteger('free');
            $table->timestamps();

            $table->index('created_at');
        });
    }
}
