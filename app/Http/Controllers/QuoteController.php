<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetOfferRequest;
use App\Http\Requests\StoreQuoteRequest;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool; // Required for parallel requests
use Exception;
use Illuminate\Support\Facades\Log;

class QuoteController extends Controller
{
    /**
     * Get the list of insurance providers with their codes
     * Authentication uses the main API account, only the code varies per provider
     */
    private function getProviders()
    {
        return [
            'Allianz' => [
                'businessName' => 'allianz',
                'code' => env('ALLIANZ_CODE', 'allianz'),
            ],
            'Asirom' => [
                'businessName' => 'asirom',
                'code' => env('ASIROM_CODE', 'asirom'),
            ],
            'Axeria' => [
                'businessName' => 'axeria',
                'code' => env('AXERIA_CODE', 'axeria'),
            ],
            'Generali' => [
                'businessName' => 'generali',
                'code' => env('GENERALI_CODE', 'generali'),
            ],
            'Groupama' => [
                'businessName' => 'groupama',
                'code' => env('GROUPAMA_CODE', 'groupama'),
            ],
            'Hellas Autonom' => [
                'businessName' => 'hellas_autonom',
                'code' => env('HELLAS_AUTONOM_CODE', 'hellas_autonom'),
            ],
            'Hellas NextIns' => [
                'businessName' => 'hellas_nextins',
                'code' => env('HELLAS_NEXTINS_CODE', 'hellas_nextins'),
            ],
            'Omniasig' => [
                'businessName' => 'omniasig',
                'code' => env('OMNIASIG_CODE', 'omniasig'),
            ],
            'Grawe' => [
                'businessName' => 'grawe',
                'code' => env('GRAWE_CODE', 'grawe'),
            ],
            'Eazy Insure' => [
                'businessName' => 'eazy_insure',
                'code' => env('EAZY_INSURE_CODE', 'eazy_insure'),
            ],
            'DallBogg' => [
                'businessName' => 'dall_bogg',
                'code' => env('DALLBOGG_CODE', 'dall_bogg'),
            ],
        ];
    }

    /**
     * Get main API credentials (used for all provider requests)
     */
    private function getApiCredentials()
    {
        return [
            'account' => env('RCA_V2_API_ACCOUNT'),
            'password' => env('RCA_V2_API_PASSWORD'),
        ];
    }

    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'account'  => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $response = Http::post(env('RCA_V2_API_URL') . '/auth', $validated);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success'       => true,
                    'token'         => $data['data']['token'] ?? null,
                    'expires_at'    => $data['data']['expires_at'] ?? null,
                    'refresh_token' => $data['data']['refresh_token'] ?? null,
                ], 200);
            }

            return response()->json([
                'error'   => 'Authentication failed',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            return response()->json([
                'error'   => 'Authentication error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of counties from external API
     */
    public function getCounties(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $response = Http::withHeaders(['Token' => $token])
                ->get(env('RCA_V2_API_URL') . '/nomenclature/county');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Failed to fetch counties',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching counties',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of localities for a county from external API
     */
    public function getLocalities(Request $request, string $countyCode)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $response = Http::withHeaders(['Token' => $token])
                ->get(env('RCA_V2_API_URL') . '/nomenclature/locality/' . $countyCode);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Failed to fetch localities',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching localities',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get vehicle types from external API
     */
    public function getVehicleTypes(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $response = Http::withHeaders(['Token' => $token])
                ->get(env('RCA_V2_API_URL') . '/nomenclature/vehicle-type');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Failed to fetch vehicle types',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching vehicle types',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get vehicle info by VIN or license plate
     */
    public function getVehicleInfo(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $vin = $request->query('vin');
            $licensePlate = $request->query('licensePlate');

            $queryParams = [];
            if ($vin) $queryParams['vin'] = $vin;
            if ($licensePlate) $queryParams['licensePlate'] = $licensePlate;

            $response = Http::withHeaders(['Token' => $token])
                ->get(env('RCA_V2_API_URL') . '/vehicle', $queryParams);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Failed to fetch vehicle info',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching vehicle info',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get company info by tax ID (CUI)
     */
    public function getCompanyInfo(Request $request, string $taxId)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $response = Http::withHeaders(['Token' => $token])
                ->get(env('RCA_V2_API_URL') . '/company/' . $taxId);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Failed to fetch company info',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching company info',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOffer(GetOfferRequest $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $basePayload = $request->validated();
            $providers = $this->getProviders();
            $apiCredentials = $this->getApiCredentials();

            Log::channel('offers')->info('=== Starting Offer Request ===', [
                'timestamp' => now()->toDateTimeString(),
                'total_providers' => count($providers),
                'token_present' => !empty($token),
                'token_length' => strlen($token),
            ]);

            // 1. Execute Parallel Requests
            $responses = Http::pool(function (Pool $pool) use ($providers, $basePayload, $token, $apiCredentials) {
                $requests = [];
                $isFirstProvider = true;

                foreach ($providers as $key => $provider) {
                    // Deep copy the payload to avoid reference issues
                    $payload = json_decode(json_encode($basePayload), true);

                    // Add provider info for this specific insurer
                    // Uses main API credentials with provider-specific code
                    $payload['provider'] = [
                        'organization' => ['businessName' => $provider['businessName']],
                        'authentication' => [
                            'account' => $apiCredentials['account'],
                            'password' => $apiCredentials['password'],
                            'code' => $provider['code'],
                        ],
                    ];

                    // Log full payload for first provider only (to avoid excessive logging)
                    if ($isFirstProvider) {
                        Log::channel('offers')->info("Full payload sample (first provider)", [
                            'payload' => $payload,
                        ]);
                        $isFirstProvider = false;
                    }

                    Log::channel('offers')->info("Requesting offer from: {$key}", [
                        'provider' => $key,
                        'insurer_code' => $provider['code'],
                    ]);

                    // Add to pool, keyed by provider name for easy retrieval
                    // API expects token in 'Token' header, not 'Authorization: Bearer'
                    $requests[] = $pool->as($key)
                        ->withHeaders(['Token' => $token])
                        ->timeout(30)
                        ->post(env('RCA_V2_API_URL') . '/offer', $payload);
                }

                return $requests;
            });

            // 2. Process Results
            $allOffers = [];
            $errors = [];
            $successfulCount = 0;

            foreach ($responses as $providerName => $response) {
                Log::channel('offers')->info("--- Response from: {$providerName} ---", [
                    'provider' => $providerName,
                    'status_code' => $response->status(),
                    'successful' => $response->successful(),
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    Log::channel('offers')->info("SUCCESS: {$providerName}", [
                        'provider' => $providerName,
                        'response_body' => $data,
                    ]);

                    if (isset($data['data']['offers'])) {
                        // Get provider business name from response
                        $providerBusinessName = $data['data']['provider']['organization']['businessName'] ?? $providerName;

                        foreach ($data['data']['offers'] as $offer) {
                            $offer['providerName'] = $providerName;
                            $offer['providerBusinessName'] = $providerBusinessName;
                            $allOffers[] = $offer;
                        }
                        $successfulCount++;
                    }
                } else {
                    $errorBody = $response->json() ?? $response->body();

                    Log::channel('offers')->error("FAILED: {$providerName}", [
                        'provider' => $providerName,
                        'status_code' => $response->status(),
                        'error_body' => $errorBody,
                    ]);

                    $errors[$providerName] = [
                        'status' => $response->status(),
                        'error' => $errorBody,
                    ];
                }
            }

            // Sort by premiumAmount (ascending - cheapest first)
            usort($allOffers, function ($a, $b) {
                $priceA = $a['premiumAmount'] ?? $a['premiumAmountNet'] ?? 0;
                $priceB = $b['premiumAmount'] ?? $b['premiumAmountNet'] ?? 0;
                return $priceA <=> $priceB;
            });

            Log::channel('offers')->info('=== Offer Request Complete ===', [
                'total_offers' => count($allOffers),
                'successful_providers' => $successfulCount,
                'failed_providers' => count($errors),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'offers' => $allOffers,
                    'totalProviders' => count($providers),
                    'successfulProviders' => $successfulCount,
                ],
                'errors' => $errors,
            ], 200);

        } catch (Exception $e) {
            Log::channel('offers')->error('Exception in getOffer', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Error fetching offers',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $quote = Quote::find($id);
        if (!$quote) {
            return response()->json(['error' => 'Quote not found'], 404);
        }
        return response()->json($quote);
    }

    public function store(StoreQuoteRequest $request)
    {
        $quote = Quote::create($request->validated());
        return response()->json(['success' => true, 'data' => $quote], 201);
    }
}
