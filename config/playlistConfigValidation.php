<?php

return [
    'TrackLimiter' => [
        'config.limitTo' => 'required|integer|between:1,10000',
        'config.byRemovingOption' => 'required|in:default-end,default_start,oldest,newest,random',
    ],
];
