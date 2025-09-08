<?php

use App\Console\Commands\RunConfig;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:run-config')->everyMinute();
