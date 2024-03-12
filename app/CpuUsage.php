<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CpuUsage extends Model
{
    /**
     * The name of the table in which this Model stored.
     *
     * @var string
     */
    protected $table = 'cpu_usage';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
