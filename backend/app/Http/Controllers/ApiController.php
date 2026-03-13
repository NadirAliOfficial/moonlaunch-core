<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Mail\ResetOtpMail;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use Aws\SecretsManager\SecretsManagerClient;

class ApiController extends Controller
{
    public function signup(Request $request)         
    {
        
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            // CORRECT SECRET - ✅ WORKING (verified by your CLI test)
            $secret = $this->getSecret('moonlaunch/postmark/stockholm/prod/server-token');
            $postmarkToken = $secret['POSTMARK_SERVER_TOKEN']
                ?? $secret['server_token']
                ?? $secret['token']
                ?? throw new \Exception('Postmark token not found in secret.');                     

            $client = new Client();
            $response = $client->post('https://api.postmarkapp.com/email', [                         
                'headers' => [ 
                    'Accept'                  => 'application/json',
                    'Content-Type'            => 'application/json',
                    'X-Postmark-Server-Token' => $postmarkToken,
                ],
                'json' => [
                    'From'          => 'noreply@moonlaunchapp.com',
                    'To'            => $request->email,
                    'Subject'       => 'Your MoonLaunch OTP Code',
                    'TextBody'      => "Your MoonLaunch OTP code is: {$otp}\n\nThis code expires in 10 minutes.\n\nDo not share this code with anyone.",
                    'HtmlBody'      => "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <h2 style='color: #1a1a2e;'>MoonLaunch Verification</h2>
                            <p>Your OTP verification code is:</p>
                            <div style='background: #f4f4f4; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;'>
                                <h1 style='color: #e94560; letter-spacing: 8px; margin: 0;'>{$otp}</h1>
                            </div>
                            <p>This code expires in <strong>10 minutes</strong>.</p>
                            <p style='color: #999; font-size: 12px;'>Do not share this code with anyone. MoonLaunch will never ask for your OTP.</p>
                        </div>
                    ",
                    'MessageStream' => 'outbound',
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);                        
            if (($result['ErrorCode'] ?? 0) !== 0) {
                throw new \Exception('Postmark error: ' . ($result['Message'] ?? 'Unknown'));
            }

        } catch (\Exception $e) {
            Log::error('Postmark OTP failed for ' . $request->email . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send OTP, please try again.',
                'debug'   => app()->environment('local') ? $e->getMessage() : 'Email service unavailable',
            ], 500);
        }

        // ── 4. Return response to Flutter 
        return response()->json([
            'message' => 'OTP sent to your email',
            'otp'     => app()->environment('local') ? $otp : null,  // Hide in production
            'email'   => $request->email,
        ], 200);
    }   
    private function getSecret(string $secretName): array
    {
        $client = new SecretsManagerClient([          
            'version' => 'latest',
            'region'  => 'eu-north-1',
        ]);

        $result = $client->getSecretValue(['SecretId' => $secretName]);
        return json_decode($result['SecretString'], true);
    }

    private function getTurnkeyCredentials(): array              
    {
        $secret = $this->getSecret('moonlaunch/turnkey/backend');                           

        $orgId      = $secret['TURNKEY_ORG_ID']           ?? null;
        $publicKey  = $secret['TURNKEY_API_PUBLIC_KEY']    ?? null;
        $privateKey = $secret['TURNKEY_API_PRIVATE_KEY']   ?? null;

        if (!$orgId || !$publicKey || !$privateKey) {
            throw new \Exception('Missing Turnkey credentials.');
        }

        // Strip ONLY the literal "0x" prefix — DO NOT use ltrim('0x') as it corrupts keys!
        $publicKey  = preg_replace('/^0x/i', '', trim($publicKey));
        $privateKey = preg_replace('/^0x/i', '', trim($privateKey));

        if (strlen($publicKey) !== 66 && strlen($publicKey) !== 130) {
            throw new \Exception(
                'Public key must be 66 chars (compressed) or 130 chars (uncompressed). Got: ' . strlen($publicKey)
            );                                                
        }

        if (strlen($privateKey) !== 64 || !ctype_xdigit($privateKey)) {                                   
            throw new \Exception('Private key must be 64 hex chars. Got: ' . strlen($privateKey) . ' / "' . $privateKey . '"');
        }

        Log::info('[Turnkey] Keys OK: pub=' . substr($publicKey, 0, 10) . '... (' . strlen($publicKey) . ')');

        return [                             
            'org_id'      => $orgId,
            'public_key'  => $publicKey,
            'private_key' => $privateKey,
        ];
    }
    private function derLength(int $len): string                                   
    {                   
        if ($len < 0x80) return chr($len);
        elseif ($len < 0x100) return "\x81" . chr($len);
        else return "\x82" . chr($len >> 8) . chr($len & 0xFF);
    }                   
                                  
    private function getTurnkeyPem(string $hexKey): string
    {                             
        // Strip ONLY the literal "0x" prefix
        $hex = preg_replace('/^0x/i', '', trim($hexKey));

        if (strlen($hex) !== 64 || !ctype_xdigit($hex)) {
            throw new \Exception('Private key must be 64 hex chars. Got: ' . strlen($hex));
        }

        $rawKey = hex2bin($hex);

        // P-256 (secp256r1) OIDs — this is what Turnkey uses for API keys
        $oidEcPublicKey = "\x06\x07\x2a\x86\x48\xce\x3d\x02\x01";
        $oidPrime256v1  = "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07";

        // ECPrivateKey (RFC 5915)
        $ecVersion   = "\x02\x01\x01";
        $ecPrivOctet = "\x04\x20" . $rawKey;
        $ecParams    = "\xa0" . $this->derLength(strlen($oidPrime256v1)) . $oidPrime256v1;
        $ecPrivBody  = $ecVersion . $ecPrivOctet . $ecParams;
        $ecPrivKey   = "\x30" . $this->derLength(strlen($ecPrivBody)) . $ecPrivBody;

        // AlgorithmIdentifier
        $algoBody = $oidEcPublicKey . $oidPrime256v1;
        $algoSeq  = "\x30" . $this->derLength(strlen($algoBody)) . $algoBody;

        // PKCS#8 wrapper
        $pkcs8Version = "\x02\x01\x00";
        $privWrapped  = "\x04" . $this->derLength(strlen($ecPrivKey)) . $ecPrivKey;
        $pkcs8Body    = $pkcs8Version . $algoSeq . $privWrapped;
        $pkcs8Der     = "\x30" . $this->derLength(strlen($pkcs8Body)) . $pkcs8Body;

        $pem = "-----BEGIN PRIVATE KEY-----\n"
            . chunk_split(base64_encode($pkcs8Der), 64, "\n")
            . "-----END PRIVATE KEY-----\n";

        $key = openssl_pkey_get_private($pem);
        if (!$key) {
            throw new \Exception('Invalid PEM generated: ' . openssl_error_string());
        }

        return $pem;
    }                                                                       
                             
    private function makeTurnkeyStamp(string $bodyJson, string $publicKey, string $hexPrivateKey): string                         
    {                     
        $pem = $this->getTurnkeyPem($hexPrivateKey);
        $privateKeyRes = openssl_pkey_get_private($pem);
        if (!$privateKeyRes) throw new \Exception('Failed to load PEM');

        $derSig = '';
        if (!openssl_sign($bodyJson, $derSig, $privateKeyRes, OPENSSL_ALGO_SHA256)) {
            throw new \Exception('ECDSA signing failed');
        }

        $signatureHex = strtolower(bin2hex($derSig));  // lowercase hex

        $stampObj = json_encode([
            'publicKey' => $publicKey,                    // 66-char compressed key
            'scheme'    => 'SIGNATURE_SCHEME_TK_API_P256',
            'signature' => $signatureHex,
        ], JSON_UNESCAPED_SLASHES);

        return rtrim(strtr(base64_encode($stampObj), '+/', '-_'), '=');
    }                
                            
    private function turnkeyPost(string $path, array $payload, string $publicKey, string $hexPrivateKey): array
    {                            
        $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $stamp = $this->makeTurnkeyStamp($bodyJson, $publicKey, $hexPrivateKey);

        Log::info('[Turnkey] POST ' . $path);
        Log::info('[Turnkey] Stamp: ' . substr($stamp, 0, 40) . '...');

        $client = new Client(['base_uri' => 'https://api.turnkey.com']);
        $response = $client->post($path, [
            'body' => $bodyJson,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Stamp' => $stamp,
            ],
            'timeout' => 30,
        ]);

        return json_decode($response->getBody()->getContents(), true);                    
    }

    public function verifyOtp(Request $request)                                                 
    {
        $request->validate([                                                  
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);                     

        $userId = DB::table('users')->insertGetId([                                                             
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => bcrypt($request->password),
            'status'            => 'Active',
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);                       

        try {                        
            Log::info('[Turnkey] Creating wallet for: ' . $request->email);                    

            $creds = $this->getTurnkeyCredentials();                    
            $payload = [
                'type'           => 'ACTIVITY_TYPE_CREATE_SUB_ORGANIZATION_V7',
                'timestampMs'    => (string)(int)(microtime(true) * 1000),
                'organizationId' => $creds['org_id'],
                'parameters'     => [
                    'subOrganizationName' => 'user-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', $request->email),
                    'rootUsers' => [
                        [
                            'userName'       => $request->name,
                            'userEmail'      => $request->email,
                            'apiKeys'        => [],
                            'authenticators' => [],
                            'oauthProviders' => [],
                        ],
                        [
                            'userName'       => 'MoonLaunch Backend',
                            'apiKeys'        => [[
                                'apiKeyName' => 'moonlaunch-backend-signer',
                                'publicKey'  => $creds['public_key'],
                            ]],
                            'authenticators' => [],
                            'oauthProviders' => [],
                        ],
                    ],
                    'rootQuorumThreshold' => 1,
                    'wallet' => [
                        'walletName' => 'Default Wallet',
                        'accounts' => [[
                            'curve' => 'CURVE_SECP256K1',
                            'pathFormat' => 'PATH_FORMAT_BIP32',
                            'path' => "m/44'/60'/0'/0/0",
                            'addressFormat' => 'ADDRESS_FORMAT_ETHEREUM',
                        ]],
                    ],
                ],
            ];

            $data = $this->turnkeyPost(                   
                '/public/v1/submit/create_sub_organization',
                $payload,
                $creds['public_key'],  // 66-char compressed
                $creds['private_key']
            );

            $activityResult = $data['activity']['result'] ?? null;
            if (!$activityResult) {
                throw new \Exception('No activity result: ' . json_encode($data));
            }

            $subOrgResult = $activityResult['createSubOrganizationResultV7'] ?? 
                            $activityResult['createSubOrganizationResultV4'] ?? null;
            if (!$subOrgResult) {
                throw new \Exception('No sub-org result');               
            }

            $subOrgId = $subOrgResult['subOrganizationId'] ?? null;
            $walletId = $subOrgResult['wallet']['walletId'] ?? null;
            $walletAddress = $subOrgResult['wallet']['addresses'][0] ?? 
                            $subOrgResult['wallet']['accounts'][0]['address'] ?? null;

            if (!$subOrgId || !$walletId || !$walletAddress) {
                throw new \Exception('Missing required fields in response');
            }

            DB::table('users')->where('id', $userId)->update([
                'wallet_address'    => $walletAddress,
                'turnkey_suborg_id' => $subOrgId,
                'turnkey_wallet_id' => $walletId,
                'updated_at'        => now(),
            ]);

            Log::info('[Turnkey] SUCCESS: ' . $walletAddress);

            $user = DB::table('users')->find($userId);
            return response()->json([
                'message' => 'User created successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'wallet_address' => $user->wallet_address,             
                ],
            ], 201);                  

        } catch (\GuzzleHttp\Exception\ClientException $e) {                  
            $body = $e->getResponse()->getBody()->getContents();                 
            Log::error('[Turnkey] Client error: ' . $body);          
            DB::table('users')->where('id', $userId)->delete();             
            return response()->json(['message' => 'Wallet creation failed', 'debug' => $body], 400);              
        } catch (\Exception $e) {           
            Log::error('[Turnkey] Error: ' . $e->getMessage());         
            DB::table('users')->where('id', $userId)->delete();          
            return response()->json(['message' => 'Account creation failed.', 'debug' => $e->getMessage()], 500);             
        }          
    }                                                     
    public function login(Request $request)                                                                
    {
        // Validate incoming request                                      
        $request->validate(['email' => 'required|email']);                             

        // Check if user exists                  
        $user = \DB::table('users')->where('email', $request->email)->first();                

        if (!$user) {                                          
            return response()->json([              
                'message' => 'User not found with this email'              
            ], 404);                                         
        }                       

        // Generate a random OTP                                                                             
        $otp = rand(100000, 999999);                                      
                   
        // Update OTP in database with 10 minute expiry                                                                                                                     
        \DB::table('users')
            ->where('email', $request->email)
            ->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),                           
            ]);                                     

        // ── Send OTP via Postmark (SAME as signup)                                                  
        try {
            $secret = $this->getSecret('moonlaunch/postmark/stockholm/prod/server-token');                             
            $postmarkToken = $secret['POSTMARK_SERVER_TOKEN']
                ?? $secret['server_token']
                ?? $secret['token']
                ?? throw new \Exception('Postmark token not found in secret.');             

            $client = new Client();
            $response = $client->post('https://api.postmarkapp.com/email', [                                     
                'headers' => [
                    'Accept'                  => 'application/json',
                    'Content-Type'            => 'application/json',
                    'X-Postmark-Server-Token' => $postmarkToken,                        
                ],
                'json' => [                      
                    'From'          => 'noreply@moonlaunchapp.com',                
                    'To'            => $request->email,
                    'Subject'       => 'Your MoonLaunch Login OTP',        
                    'TextBody'      => "Your MoonLaunch login OTP code is: {$otp}\n\nThis code expires in 10 minutes.\n\nDo not share this code with anyone.",
                    'HtmlBody'      => "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <h2 style='color: #1a1a2e;'>MoonLaunch Login</h2>
                            <p>Your login OTP verification code is:</p>
                            <div style='background: #f4f4f4; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;'>
                                <h1 style='color: #e94560; letter-spacing: 8px; margin: 0;'>{$otp}</h1>
                            </div>
                            <p>This code expires in <strong>10 minutes</strong>.</p>
                            <p style='color: #999; font-size: 12px;'>Do not share this code with anyone. MoonLaunch will never ask for your OTP.</p>
                        </div>
                    ",
                    'MessageStream' => 'outbound',
                ],          
            ]);                                          

            $result = json_decode($response->getBody()->getContents(), true);                        
            if (($result['ErrorCode'] ?? 0) !== 0) {
                throw new \Exception('Postmark error: ' . ($result['Message'] ?? 'Unknown'));
            }          

        } catch (\Exception $e) {                         
            Log::error('Login OTP failed for ' . $request->email . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send OTP, please try again.',
            ], 500);
        }

        return response()->json([                                
            'message' => 'OTP sent to your email',                             
            'email' => $request->email
        ], 200);
    }                                                                                                                 

    
    public function verifyLoginOtp(Request $request)                              
    {
        // Validate request                       
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'otp' => 'required|numeric',
        ]);

        // Get user with matching email                     
        $user = \DB::table('users')
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return response()->json([         
                'message' => 'Invalid email'
            ], 400);
        }

        // Check if password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid password'
            ], 400);
        }

        // Check if OTP matches                     
        if ($user->otp != $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 400);
        }
                                    
        // Check if OTP has expired
        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'message' => 'OTP has expired. Please request a new one.'
            ], 400);
        }

        // Clear OTP after successful verification                                   
        \DB::table('users')
            ->where('email', $request->email)
            ->update([
                'otp' => null,
                'otp_expires_at' => null,
            ]);

        // Convert user object to array and remove sensitive fields
        $userData = (array) $user;
        unset($userData['password'], $userData['otp'], $userData['otp_expires_at']);

        // Return user data
        return response()->json([
            'message' => 'Login successful',
            'user' => $userData
        ], 200);
    }                                         
    public function simpleLogin(Request $request)           
    {
        // Validate incoming request                   
        $request->validate([               
            'email' => 'required|email',
            'password' => 'required|string',
        ]);            
    
        // Get user by email
        $user = \DB::table('users')->where('email', $request->email)->first();           
    
        // Check if user exists           
        if (!$user) {
            return response()->json([            
                'message' => 'Invalid email or password'           
            ], 401);            
        }                
    
        // Verify password             
        if (!\Hash::check($request->password, $user->password)) {          
            return response()->json([           
                'message' => 'Invalid email or password'            
            ], 401);          
        }           
    
        // Login successful             
        return response()->json([             
            'message' => 'Login successful',           
            'user' => [              
                'id' => $user->id,         
                'name' => $user->name,          
                'email' => $user->email,             
                'created_at' => $user->created_at,                 
            ]
        ], 200);
    }                                                                                      

    // Edit Profile                                                                                  
    public function editProfile(Request $request)                  
    {                  
        try {
            // Step 1: Validate input
            $validator = Validator::make($request->all(), [                   
                'user_id' => 'required|integer|exists:users,id', 
                'name'    => 'required|string|max:255',
                'image'   => 'nullable|string',
            ]);

            if ($validator->fails()) {                                                   
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),                     
                ], 422);
            }

            // Step 2: Prepare data for update                                                                                      
            $data = [
                'name' => $request->name,
            ];

            // Step 3: If image is present, decode and store it                                                                                                                  
            if ($request->has('image') && $request->image != '') {
                $base64Image = $request->image;
                $imageName   = time() . '_' . uniqid() . '.png'; // you can detect mime type if needed
                $imagePath   = public_path('uploads/users/' . $imageName);

                // Decode and store
                file_put_contents($imagePath, base64_decode($base64Image));

                $data['profile_pick'] = 'uploads/users/' . $imageName;
            }

            // Step 4: Update user in DB                                                                          
            DB::table('users')
                ->where('id', $request->user_id)
                ->update($data);

            // Step 5: Return success
            return response()->json([
                'status'  => true,
                'message' => 'Profile updated successfully',
                'data'    => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }                             
// Edit Profile                                                                                                                            
    // Send OTP                                                             
    public function sendUserOtp(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if user exists with this email using DB facade
            $user = DB::table('users')->where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No user exists with that email'
                ], 404);
            }

            // Generate 6 digit OTP
            $otp = rand(100000, 999999);

            // ── Send OTP via Postmark (SAME as signup) 
            $secret = $this->getSecret('moonlaunch/postmark/stockholm/prod/server-token');
            $postmarkToken = $secret['POSTMARK_SERVER_TOKEN']
                ?? $secret['server_token']
                ?? $secret['token']
                ?? throw new \Exception('Postmark token not found in secret.');

            $client = new Client();
            $response = $client->post('https://api.postmarkapp.com/email', [
                'headers' => [
                    'Accept'                  => 'application/json',
                    'Content-Type'            => 'application/json',
                    'X-Postmark-Server-Token' => $postmarkToken,
                ],
                'json' => [
                    'From'          => 'noreply@moonlaunchapp.com',
                    'To'            => $request->email,
                    'Subject'       => 'MoonLaunch Password Reset OTP',
                    'TextBody'      => "Your MoonLaunch password reset OTP code is: {$otp}\n\nThis code expires in 10 minutes.\n\nDo not share this code with anyone.",
                    'HtmlBody'      => "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <h2 style='color: #1a1a2e;'>MoonLaunch Password Reset</h2>
                            <p>Your password reset OTP verification code is:</p>
                            <div style='background: #f4f4f4; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;'>
                                <h1 style='color: #e94560; letter-spacing: 8px; margin: 0;'>{$otp}</h1>
                            </div>
                            <p>This code expires in <strong>10 minutes</strong>.</p>
                            <p style='color: #999; font-size: 12px;'>Do not share this code with anyone. MoonLaunch will never ask for your OTP.</p>
                        </div>
                    ",
                    'MessageStream' => 'outbound',
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);                        
            if (($result['ErrorCode'] ?? 0) !== 0) {
                throw new \Exception('Postmark error: ' . ($result['Message'] ?? 'Unknown'));
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Reset OTP failed for ' . $request->email . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP, please try again.'
            ], 500);
        }
    }
    public function sendOtp(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if user exists with this email using DB facade
            $user = DB::table('users')->where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No user exists with that email'
                ], 404);
            }

            // Generate 6 digit OTP
            $otp = rand(100000, 999999);

            // ── Send OTP via Postmark (SAME as signup) ───────────────────────────
            $secret = $this->getSecret('moonlaunch/postmark/stockholm/prod/server-token');
            $postmarkToken = $secret['POSTMARK_SERVER_TOKEN']
                ?? $secret['server_token']
                ?? $secret['token']
                ?? throw new \Exception('Postmark token not found in secret.');

            $client = new Client();
            $response = $client->post('https://api.postmarkapp.com/email', [
                'headers' => [
                    'Accept'                  => 'application/json',
                    'Content-Type'            => 'application/json',
                    'X-Postmark-Server-Token' => $postmarkToken,
                ],
                'json' => [
                    'From'          => 'noreply@moonlaunchapp.com',
                    'To'            => $request->email,
                    'Subject'       => 'MoonLaunch Password Reset OTP',
                    'TextBody'      => "Your MoonLaunch password reset OTP code is: {$otp}\n\nThis code expires in 10 minutes.\n\nDo not share this code with anyone.",
                    'HtmlBody'      => "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <h2 style='color: #1a1a2e;'>MoonLaunch Password Reset</h2>
                            <p>Your password reset OTP verification code is:</p>
                            <div style='background: #f4f4f4; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;'>
                                <h1 style='color: #e94560; letter-spacing: 8px; margin: 0;'>{$otp}</h1>
                            </div>
                            <p>This code expires in <strong>10 minutes</strong>.</p>
                            <p style='color: #999; font-size: 12px;'>Do not share this code with anyone. MoonLaunch will never ask for your OTP.</p>
                        </div>
                    ",
                    'MessageStream' => 'outbound',
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);                        
            if (($result['ErrorCode'] ?? 0) !== 0) {
                throw new \Exception('Postmark error: ' . ($result['Message'] ?? 'Unknown'));
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Reset OTP failed for ' . $request->email . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP, please try again.'
            ], 500);
        }
    }                                                       
                                                                                                                

    // Send OTP                                                                                                         
    // Reset Password                                                                                                                              
    public function resetPassword(Request $request)
    {
        // Validate incoming request                 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {            
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
                         
        try {
            // Find user using DB facade                                                                           
            $user = DB::table('users')->where('id', $request->user_id)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Update password using DB facade                                                                              
            DB::table('users')
                ->where('id', $request->user_id)
                ->update([
                    'password' => Hash::make($request->new_password),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',                       
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]
            ], 200);

        } catch (\Exception $e) {                     
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }                                               
    // Reset Password                                                               
    // Change Password                                                                                                                                              
    public function changePassword(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {                
            // Find user using DB facade                                                                                                                                                                 
            $user = DB::table('users')->where('id', $request->user_id)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }       

            // Check if old password is correct                                                                                       
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Old password is incorrect'
                ], 401);
            }

            // Update password using DB facade                      
            DB::table('users')
                ->where('id', $request->user_id)
                ->update([
                    'password' => Hash::make($request->new_password),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password',
                'error' => $e->getMessage()
            ], 500);
        }
    }                                
    // Change Password                                          
}
