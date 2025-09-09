<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\SpotifyAuth;
use App\Models\User;
use App\Services\CacheService;
use App\Services\SpotifyService;
use Illuminate\Http\RedirectResponse;

class SpotifyConnectController extends Controller
{
    /**
     * Constructor.
     * @param SpotifyService $spotifyService The spotify service class.
     */
    public function __construct(
        protected SpotifyService $spotifyService,
        protected CacheService $cacheService
    ) {
        // Constructor
    }

    /**
     * Connect to spotify and get authorisation url. Pass this to the front end.
     * @return Response
     */
    public function connect(): Response|RedirectResponse
    {
        $authorizationUrl = $this->spotifyService->connect();

        if (Auth::check()) {
            return redirect($authorizationUrl);
        }

        return Inertia::render('Auth/Connect', [
            'link' => $authorizationUrl
        ]);
    }

    /**
     * Authorise the spotify request with the generated code.
     * @param Request $request The request.
     * @throws Exception If spotify cannot authorise.
     * @return Redirect
     */
    public function spotifyAuth(Request $request)
    {
        $responseData = $this->spotifyService->getAccessData($request->input('code'));

        if (isset($responseData['access_token'])) {
            $accessToken = $responseData['access_token'];
            // API endpoint URL
            $apiUrl = env('SPOTIFY_API_URL');

            $response = $this->spotifyService->apiRequest($apiUrl . 'me', null, $accessToken);

            // Check for errors
            if ($response !== false && $response !== 'Error: Unable to retrieve access token.') {
                // Decode the response JSON.
                $data = json_decode($response, true);

                // [TODO: Handle this with error page.]
                if (isset($data['error'])) {
                    echo $data['error']['message'];
                    return;
                }

                $spotUser = User::where('spotify_id', $data['id'])->first();
                if ($spotUser === null) {
                    $spotUser = new User();
                }
                $spotUser->name          = $data['display_name'];
                $spotUser->spotify_id    = $data['id'];
                $spotUser->profile_image = $data['images'][1]['url'];
                $spotUser->save();

                $sAuth = SpotifyAuth::where('user_id', $spotUser->id)->first();

                if ($sAuth === null) {
                    $sAuth = new SpotifyAuth();
                }

                $sAuth->spotify_access_token  = $responseData['access_token'];
                $sAuth->spotify_refresh_token = $responseData['refresh_token'];
                $sAuth->user_id               = $spotUser->id;
                $sAuth->save();

                Auth::login($spotUser);

                //Add token to cache.
                $this->cacheService->setCacheItem($spotUser->spotify_id, $responseData['access_token'], now()->addDays(50));

                return redirect()->route('dashboard');
            }
        } else {
            // [TODO: Handle this with error page.]
            echo "Error: Unable to retrieve access token.";
            return;
        }
    }
}
