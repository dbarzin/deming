<?php

namespace App\Providers\Socialite;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Log;

/**
 * Generic OpenId Connect provider for Socialite.
 */
class GenericSocialiteProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'OIDC';

    /**
     * Scope definitions.
     */
    public const SCOPE_EMAIL = 'email';
    public const SCOPE_OPENID = 'openid';
    public const SCOPE_PROFILE = 'profile';

    /**
     * Adjust the available read / write attributes in cognito client app.
     *
     * {@inheritdoc}
     */
    protected $scopes = [
        self::SCOPE_OPENID,
        self::SCOPE_PROFILE,
        self::SCOPE_EMAIL,
    ];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * Return provider Url.
     *
     * @return string
     */
    public function getOIDCUrl()
    {
        return rtrim(config('services.oidc.host'), '/').config('services.oidc.suffix', '');
    }

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys(): array
    {
        return [
            'client_id',
            'host',
            'logout_uri',
            'redirect',
            'authorize_endpoint',
            'userinfo_endpoint',
            'token_endpoint',
            'map_user_attr',
        ];
    }

    /**
     * @param string $state
     *
     * @return string
     */
    protected function getAuthUrl($state)
    {
        $base_url = $this->getOIDCUrl().'/authorize';
        // If authorize endpoint set, use it instead
        if (config('services.oidc.authorize_endpoint')) {
            $base_url = config('services.oidc.authorize_endpoint');
        }
        Log::debug('Buiild auth url from base : '.$base_url);
        return $this->buildAuthUrlFromBase($base_url, $state);
    }

    /**
     * @return string
     */
    protected function getTokenUrl()
    {
        // If token endpoint set, use it instead
        if (config('services.oidc.token_endpoint')) {
            return config('services.oidc.token_endpoint');
        }
        return $this->getOIDCUrl() . '/token';
    }

   /**
     * {@inheritdoc}
     */
    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $user = $this->getUserByToken(Arr::get($response, 'access_token'), Arr::get($response, 'id_token'));

        return $this->userInstance($response, $user);
    }


    /**
     * @param string $token
     *
     * @throws GuzzleException
     *
     * @return array|mixed
     */
    protected function getUserByToken($token, $idToken = null)
    {
        $useIdToken = config('services.oidc.use_id_token', false);

        if ($useIdToken) {
            if (!$idToken) {
                throw new \Exception('OIDC_USE_ID_TOKEN=true but id_token not received');
            }
            return $this->decodeIdToken($idToken);
        }

        $base_url = $this->getOIDCUrl() . '/userinfo';
        // If userinfo endpoint set, use it instead
        if (config('services.oidc.userinfo_endpoint')) {
            $base_url = config('services.oidc.userinfo_endpoint');
        }

        Log::debug('Get user info from '.$base_url);
        $response = $this->getHttpClient()->post($base_url, [
            'headers' => [
                'cache-control' => 'no-cache',
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return User
     */
    protected function mapUserToObject(array $user)
    {
        Log::debug('Provider return user :'.var_export($user, true));
        $socialite_user = [];
        foreach (config('services.oidc.map_user_attr') as $socialite_attr => $provider_attr) {
            if (! array_key_exists($provider_attr, $user)) {
                Log::debug("'{$provider_attr}' not provided");
                continue;
            }
            Log::debug("Map socialite_user['{$socialite_attr}']=".$user[$provider_attr]);
            $socialite_user[$socialite_attr] = $user[$provider_attr];
        }
        return (new User())->setRaw($user)->map($socialite_user);
    }

    protected function decodeIdToken($idToken)
    {
        $alg = config('services.oidc.jwt_alg', 'RS256');
        $key = config('services.oidc.jwt_secret_or_key');

        if (!$key) {
            throw new \Exception('JWT secret or public key not configured');
        }

        try {
            $decoded = JWT::decode($idToken, new Key($key, $alg));
        } catch (\Exception $e) {
            throw new \Exception('Failed to decode ID token: '.$e->getMessage(), 0, $e);
        }

        $claims = (array) $decoded;
        $clientId = config('services.oidc.client_id');
        $expectedIssuer = rtrim(config('services.oidc.issuer', $this->getOIDCUrl()), '/');
        $aud = $claims['aud'] ?? null;
        $audiences = is_array($aud) ? $aud : ($aud !== null ? [$aud] : []);
        if (($claims['iss'] ?? null) !== $expectedIssuer) {
            throw new \Exception('Invalid ID token issuer');
        }
        if (!in_array($clientId, $audiences, true)) {
            throw new \Exception('Invalid ID token audience');
        }
        if (count($audiences) > 1 && ($claims['azp'] ?? null) !== $clientId) {
            throw new \Exception('Invalid ID token authorized party');
        }
        return $claims;
    }
}
