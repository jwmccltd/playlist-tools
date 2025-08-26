<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaylistConfigurationOptionField extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'playlist_configuration_option_fields';

    public $timestamps = false;

    protected $casts = [
        'config_fields' => 'array',
    ];
}
