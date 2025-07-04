<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\User;
use App\Models\SpotifyAuth;
use App\Services\CacheService;

class SpotifyService
{
    public function __construct(protected CacheService $cacheService) {
        // Constructor.
    }

    /**
     * Connect to spotify and return the authorisation url.
     * @return string
     */
    public function connect() {
        // Spotify API credentials
        $clientId =     env('SPOTIFY_CLIENT_ID');
        $clientSecret = env('SPOTIFY_CLIENT_SECRET');

        // Authorization endpoint URL
        $authorizeUrl = 'https://accounts.spotify.com/authorize';
        // Token endpoint URL
        $tokenUrl = 'https://accounts.spotify.com/api/token';

        // Redirect URI where Spotify will redirect after authorization
        $redirectUri = env('SPOTIFY_AUTH_REDIRECT');

        // Scope (permissions)
        $scope = 'playlist-modify-public';

        // Set up GuzzleHTTP client
        $client = new Client();

        // Authorization flow: redirect user to Spotify's authorization page
        $authorizationParams = array(
            'response_type' => 'code',
            'client_id'     => $clientId,
            'scope'         => $scope,
            'redirect_uri'  => $redirectUri
        );

        return $authorizeUrl . '?' . http_build_query($authorizationParams);
    }

    /**
     * Get access data.
     * @param string $code The access code.
     * @return array
     */
    public function getAccessData($code)
    {
        $clientId     = env('SPOTIFY_CLIENT_ID');
        $clientSecret = env('SPOTIFY_CLIENT_SECRET');
        $redirectUri  = env('SPOTIFY_AUTH_REDIRECT');

        $tokenUrl = 'https://accounts.spotify.com/api/token';
        $tokenData = [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $redirectUri,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Get refresh token data.
     * @param string $refreshToken The refresh token.
     * @return array
     */
    public function getRefreshData($refreshToken)
    {
        $clientId     = env('SPOTIFY_CLIENT_ID');
        $clientSecret = env('SPOTIFY_CLIENT_SECRET');

        $tokenUrl = 'https://accounts.spotify.com/api/token';
        $tokenData = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Run an api request
     * @param string  $apiUrl      The api url.
     * @param integer $userId      The user id.
     * @param string  $accessToken The access token.
     * @param string  $protocol    The protocol if not GET.
     * @param string  $body        The body (JSON).
     *
     * @return string
     */
    public function apiRequest($apiUrl, $userId = null, $accessToken = null, $protocol = null, ?string $body = null)
    {
        // Get access token
        if ($accessToken === null) {
            $accessToken = $this->getSetAccessToken($userId);
        }

        // Set up cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        if ($protocol !== null) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $protocol);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);
        // Close cURL session
        curl_close($ch);

        return $response;
    }

    /**
     * Get/set access token
     * @return string|null
     */
    public function getSetAccessToken($userId)
    {
        $user = User::find($userId);

        $accessToken = $this->cacheService->loadCacheItem($user->spotify_id);

        // If the access token is null or different to the user requested token, then request a new one.
        if ($accessToken !== $user->spotifyAuth->spotify_access_token) {
            $refreshData = $this->getRefreshData($user->spotifyAuth->spotify_refresh_token);

            $accessToken = null;
            if (isset($refreshData['access_token'])) {
                $accessToken = $refreshData['access_token'];

                //Add token to cache.
                $this->cacheService->setCacheItem($user->spotify_id, $accessToken, now()->addMinutes(50));

                $sAuth = SpotifyAuth::where('user_id', $userId)->first();
                $sAuth->spotify_access_token = $accessToken;
                $sAuth->save();
            }
        }

        return $accessToken;
    }
}
