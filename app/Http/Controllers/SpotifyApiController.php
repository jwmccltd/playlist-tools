<?php

namespace App\Http\Controllers;

use App\Services\DataService;

class SpotifyApiController extends Controller
{
    /**
     * Constructor.
     * @param DataService $dataService The data service.
     */
    public function __construct(
        protected DataService $dataService
    ) {
        // Constructor
    }

    /**
     * Function to get data from spotify api, or cache.
     *
     * @param string $identifier The identifier.
     * @param string $url        The url.
     * @return array
     */
    public function getData($identifier, $url)
    {
        return $this->dataService->getData($identifier, $url);
    }
}
