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
     * Auto-authenticate using credentials from .env file
     * This allows the frontend to skip the login form
     */
    public function autoAuthenticate()
    {
        $credentials = $this->getApiCredentials();

        if (empty($credentials['account']) || empty($credentials['password'])) {
            return response()->json([
                'error' => 'API credentials not configured in environment',
            ], 500);
        }

        try {
            $response = Http::post(env('RCA_V2_API_URL') . '/auth', $credentials);

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
                'error'   => 'Auto-authentication failed',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            return response()->json([
                'error'   => 'Auto-authentication error',
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

    /**
     * Download offer as PDF
     * Uses the external API to generate a PDF for the offer
     * External API: GET /offer/{id} with optional withDirectCompensation query param
     */
    public function downloadOfferPdf(Request $request, string $offerId)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Get optional withDirectCompensation parameter
            $withDirectCompensation = $request->query('withDirectCompensation', 0);

            Log::channel('offers')->info('Downloading offer PDF', [
                'offerId' => $offerId,
                'withDirectCompensation' => $withDirectCompensation,
            ]);

            // Call the external API to get the offer PDF
            // API endpoint: GET /offer/{id}
            $response = Http::withHeaders(['Token' => $token])
                ->timeout(60)
                ->get(env('RCA_V2_API_URL') . '/offer/' . $offerId, [
                    'withDirectCompensation' => $withDirectCompensation,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Response structure: { data: { files: [{ type, name, content (base64) }] } }
                if (isset($data['data']['files']) && count($data['data']['files']) > 0) {
                    $file = $data['data']['files'][0];
                    $pdfContent = base64_decode($file['content']);
                    $fileName = $file['name'] ?? 'offer_' . $offerId . '.pdf';

                    return response($pdfContent, 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
                }

                // If no files in response, return the JSON data
                return response()->json($data);
            }

            Log::channel('offers')->error('Failed to download offer PDF', [
                'offerId' => $offerId,
                'status' => $response->status(),
                'error' => $response->json(),
            ]);

            return response()->json([
                'error' => 'Failed to download offer PDF',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            Log::channel('offers')->error('Exception downloading offer PDF', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Error downloading offer PDF',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if a policy already exists for a given offer
     * External API: GET /offer/{id} to check status
     */
    public function checkPolicyExists(Request $request, string $offerId)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Log::channel('offers')->info('Checking if policy exists for offer', [
                'offerId' => $offerId,
            ]);

            // Call the external API to get offer details and check its status
            // If a policy was created from an offer, the offer status should reflect this
            $response = Http::withHeaders(['Token' => $token])
                ->timeout(30)
                ->get(env('RCA_V2_API_URL') . '/offer/' . $offerId);

            if ($response->successful()) {
                $data = $response->json();

                // Check the offer data to see if a policy was created
                // The API may return policy info or status indicating policy exists
                $offerData = $data['data'] ?? $data;

                // Check for policy-related fields in the response
                $policyExists = false;
                $policyInfo = null;

                // Common indicators that a policy exists:
                // 1. status field indicating 'converted', 'policy_created', etc.
                // 2. policyId or policyNumber field present
                // 3. policies array present and non-empty
                if (isset($offerData['status']) && in_array(strtolower($offerData['status']), ['converted', 'policy_created', 'issued', 'completed'])) {
                    $policyExists = true;
                }

                if (isset($offerData['policyId']) || isset($offerData['policyNumber']) || isset($offerData['policy'])) {
                    $policyExists = true;
                    $policyInfo = [
                        'policyId' => $offerData['policyId'] ?? $offerData['policy']['id'] ?? null,
                        'policyNumber' => $offerData['policyNumber'] ?? null,
                    ];
                }

                if (isset($offerData['policies']) && is_array($offerData['policies']) && count($offerData['policies']) > 0) {
                    $policyExists = true;
                    $policyInfo = $offerData['policies'][0];
                }

                Log::channel('offers')->info('Policy check result', [
                    'offerId' => $offerId,
                    'policyExists' => $policyExists,
                    'policyInfo' => $policyInfo,
                ]);

                return response()->json([
                    'success' => true,
                    'policyExists' => $policyExists,
                    'policyInfo' => $policyInfo,
                    'offerStatus' => $offerData['status'] ?? null,
                ]);
            }

            // If we get a 404, the offer might not exist anymore (possibly already converted)
            if ($response->status() === 404) {
                Log::channel('offers')->info('Offer not found - may have been converted to policy', [
                    'offerId' => $offerId,
                ]);
                return response()->json([
                    'success' => true,
                    'policyExists' => true, // Assume policy exists if offer not found
                    'message' => 'Offer not found - may have been converted to policy',
                ]);
            }

            Log::channel('offers')->error('Failed to check policy status', [
                'offerId' => $offerId,
                'status' => $response->status(),
                'error' => $response->json(),
            ]);

            return response()->json([
                'error' => 'Failed to check policy status',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            Log::channel('offers')->error('Exception checking policy status', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Error checking policy status',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a policy from an offer
     * Converts the selected offer into an actual insurance policy
     * External API: POST /policy with ApiPolicyRequest body
     */
    public function createPolicy(Request $request, string $offerId = null)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Get offerId from URL parameter or request body
            $offerId = $offerId ?? $request->input('offerId');
            if (!$offerId) {
                return response()->json(['error' => 'Offer ID is required'], 400);
            }

            // Get premium amount from the offer data passed by frontend
            $premiumAmount = $request->input('premiumAmount', 0);
            $currency = $request->input('currency', 'RON');

            // Build the policy request payload as per ApiPolicyRequest schema
            $payload = [
                'offerId' => (int) $offerId,
                'includeDirectCompensation' => $request->input('includeDirectCompensation', false),
                'payment' => $request->input('payment', [
                    'method' => 'receipt',
                    'currency' => $currency,
                    'amount' => $premiumAmount,
                    'date' => date('Y-m-d'),
                    'documentNumber' => $request->input('documentNumber', 'AUTO-' . time()),
                ]),
            ];

            Log::channel('offers')->info('Creating policy from offer', [
                'offerId' => $offerId,
                'payload' => $payload,
            ]);

            // Call the external API to create the policy
            // API endpoint: POST /policy
            $response = Http::withHeaders(['Token' => $token])
                ->timeout(60)
                ->post(env('RCA_V2_API_URL') . '/policy', $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::channel('offers')->info('Policy created successfully', [
                    'offerId' => $offerId,
                    'response' => $data,
                ]);

                // Extract the policy from the response structure: { data: { policies: [...] } }
                $policyData = null;
                if (isset($data['data']['policies']) && count($data['data']['policies']) > 0) {
                    $policyData = $data['data']['policies'][0];
                } elseif (isset($data['data'])) {
                    $policyData = $data['data'];
                } else {
                    $policyData = $data;
                }

                return response()->json([
                    'success' => true,
                    'data' => $policyData,
                ]);
            }

            Log::channel('offers')->error('Failed to create policy', [
                'offerId' => $offerId,
                'status' => $response->status(),
                'error' => $response->json(),
            ]);

            return response()->json([
                'error' => 'Failed to create policy',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            Log::channel('offers')->error('Exception creating policy', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Error creating policy',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download policy as PDF
     * Downloads the insurance policy document as PDF
     * External API: GET /policy/{id}
     */
    public function downloadPolicyPdf(Request $request, string $policyId)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Log::channel('offers')->info('Downloading policy PDF', [
                'policyId' => $policyId,
            ]);

            // Call the external API to get the policy PDF
            // API endpoint: GET /policy/{id}
            $response = Http::withHeaders(['Token' => $token])
                ->timeout(60)
                ->get(env('RCA_V2_API_URL') . '/policy/' . $policyId);

            if ($response->successful()) {
                $contentType = $response->header('Content-Type');

                // If the response is directly a PDF binary
                if ($contentType && str_contains($contentType, 'application/pdf')) {
                    Log::channel('offers')->info('Policy PDF downloaded successfully (binary)', [
                        'policyId' => $policyId,
                    ]);
                    return response($response->body(), 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'attachment; filename="policy_' . $policyId . '.pdf"');
                }

                // Try to parse as JSON
                $data = $response->json();

                Log::channel('offers')->info('Policy PDF response received', [
                    'policyId' => $policyId,
                    'response_keys' => is_array($data) ? array_keys($data) : 'not_array',
                    'data_keys' => isset($data['data']) && is_array($data['data']) ? array_keys($data['data']) : 'no_data',
                ]);

                // Response structure: { data: { files: [{ type, name, content (base64) }] } }
                if (isset($data['data']['files']) && count($data['data']['files']) > 0) {
                    $file = $data['data']['files'][0];
                    $pdfContent = base64_decode($file['content']);
                    $fileName = $file['name'] ?? 'policy_' . $policyId . '.pdf';

                    Log::channel('offers')->info('Policy PDF downloaded successfully (base64)', [
                        'policyId' => $policyId,
                        'fileName' => $fileName,
                    ]);

                    return response($pdfContent, 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
                }

                // If the response contains a URL to download
                if (isset($data['data']['url'])) {
                    Log::channel('offers')->info('Policy PDF URL received', [
                        'policyId' => $policyId,
                        'url' => $data['data']['url'],
                    ]);
                    return response()->json([
                        'success' => true,
                        'pdfUrl' => $data['data']['url'],
                    ]);
                }

                // If no files in response, return the JSON data for debugging
                Log::channel('offers')->warning('Policy PDF - unexpected response format', [
                    'policyId' => $policyId,
                    'response' => $data,
                ]);
                return response()->json($data);
            }

            Log::channel('offers')->error('Failed to download policy PDF', [
                'policyId' => $policyId,
                'status' => $response->status(),
                'error' => $response->json(),
            ]);

            return response()->json([
                'error' => 'Failed to download policy PDF',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            Log::channel('offers')->error('Exception downloading policy PDF', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Error downloading policy PDF',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get policy PDF file
     * Downloads a policy PDF file (general endpoint)
     * External API: GET /policy (Policy PDF file)
     */
    public function getPolicyPdfFile(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Log::channel('offers')->info('Getting policy PDF file');

            // Call the external API to get the policy PDF file
            // API endpoint: GET /policy
            $response = Http::withHeaders(['Token' => $token])
                ->timeout(60)
                ->get(env('RCA_V2_API_URL') . '/policy');

            if ($response->successful()) {
                $contentType = $response->header('Content-Type');

                // If the response is a PDF, return it
                if ($contentType && str_contains($contentType, 'application/pdf')) {
                    return response($response->body(), 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'attachment; filename="policy.pdf"');
                }

                // If the response contains files (base64 encoded)
                $data = $response->json();
                if (isset($data['data']['files']) && count($data['data']['files']) > 0) {
                    $file = $data['data']['files'][0];
                    $pdfContent = base64_decode($file['content']);
                    $fileName = $file['name'] ?? 'policy.pdf';

                    return response($pdfContent, 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
                }

                // If the response contains a URL to download
                if (isset($data['data']['url'])) {
                    return response()->json([
                        'success' => true,
                        'pdfUrl' => $data['data']['url'],
                    ]);
                }

                return response()->json($data);
            }

            Log::channel('offers')->error('Failed to get policy PDF file', [
                'status' => $response->status(),
                'error' => $response->json(),
            ]);

            return response()->json([
                'error' => 'Failed to get policy PDF file',
                'details' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {
            Log::channel('offers')->error('Exception getting policy PDF file', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Error getting policy PDF file',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
