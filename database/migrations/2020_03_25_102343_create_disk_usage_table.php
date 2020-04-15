<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiskUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disk_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('total');
            $table->unsignedInteger('free');
            $table->unsignedInteger('used');
            $table->timestamps();

            $table->index('created_at');
        });
    }
}
