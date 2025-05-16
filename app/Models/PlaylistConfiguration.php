<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaylistConfiguration extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'playlist_configurations';

    public $timestamps = false;

    protected $fillable = [
        'option_id', 'playlist_id', 'config'
    ];
}
