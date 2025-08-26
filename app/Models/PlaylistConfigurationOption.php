<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlaylistConfigurationOption extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'playlist_configuration_options';

    public $timestamps = false;

    /**
     * Get the phone associated with the user.
     */
    public function configFields(): HasOne
    {
        return $this->hasOne(PlaylistConfigurationOptionField::class, 'option_id');
    }
}
