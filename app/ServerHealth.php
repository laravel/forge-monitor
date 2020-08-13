<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServerHealth extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'server_health';
}
