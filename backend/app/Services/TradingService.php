<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Aws\SecretsManager\SecretsManagerClient;

class TradingService
{
    // PancakeSwap V2 Router on BSC
    const ROUTER_ADDRESS = '10ED43C718714eb63d5aA57B78B54704E256024E';
    // Wrapped BNB
    const WBNB_ADDRESS   = 'bb4CdB9CBd36B01bD1cBaEBF2De08d9173bc095c';
    // BSC mainnet RPC
    const BSC_RPC        = 'https://bsc-dataseed.binance.org/';
    const BSC_RPC_ALT    = 'https://bsc-dataseed1.defibit.io/';
    const CHAIN_ID       = 56;

    protected string $orgId;
    protected string $publicKey;
    protected string $privateKey;

    public function __construct()
    {
        $creds = $this->getTurnkeyCredentials();
        $this->orgId      = $creds['org_id'];
        $this->publicKey  = $creds['public_key'];
        $this->privateKey = $creds['private_key'];
    }

    // ── Public API ─────────────────────────────────────────────────────────────

    /**
     * Buy a token with BNB via PancakeSwap V2 swapExactETHForTokens.
     *
     * @param string $walletAddress  User's BSC wallet (0x...)
     * @param string $tokenAddress   Token contract to buy (0x...)
     * @param string $bnbAmountWei   BNB to spend, as decimal wei string
     * @param string $subOrgId       Turnkey sub-org ID for the user
     * @param int    $slippageBps    Slippage in basis points (500 = 5%)
     * @return string  Transaction hash
     */
    public function buy(
        string $walletAddress,
        string $tokenAddress,
        string $bnbAmountWei,
        string $subOrgId,
        string $subOrgKey,
        int    $slippageBps = 500
    ): string {
        $amountOut    = $this->getAmountsOut($bnbAmountWei, $tokenAddress);
        $amountOutMin = $this->applySlippage($amountOut, $slippageBps);

        Log::info("[Trade/Buy] {$bnbAmountWei} wei BNB → {$tokenAddress}, min out: {$amountOutMin}");

        $deadline = (string)(time() + 300);
        $calldata = $this->swapExactEthForTokensData($amountOutMin, $tokenAddress, $walletAddress, $deadline);

        return $this->buildSignAndBroadcast(
            from:     $walletAddress,
            to:       self::ROUTER_ADDRESS,
            valueWei: $bnbAmountWei,
            data:     $calldata,
            subOrgId: $subOrgId,
            subOrgKey: $subOrgKey,
        );
    }

    /**
     * Send native BNB to another address.
     */
    public function sendNative(
        string $from,
        string $to,
        string $amountWei,
        string $subOrgId,
        string $subOrgKey
    ): string {
        return $this->buildSignAndBroadcast(
            from:      $from,
            to:        ltrim($to, '0x'),
            valueWei:  $amountWei,
            data:      '',
            subOrgId:  $subOrgId,
            subOrgKey: $subOrgKey,
        );
    }

    /**
     * Send an ERC-20 token to another address.
     */
    public function sendToken(
        string $from,
        string $tokenAddress,
        string $toAddress,
        string $amountWei,
        string $subOrgId,
        string $subOrgKey
    ): string {
        $calldata = $this->erc20TransferData($toAddress, $amountWei);
        return $this->buildSignAndBroadcast(
            from:      $from,
            to:        ltrim($tokenAddress, '0x'),
            valueWei:  '0',
            data:      $calldata,
            subOrgId:  $subOrgId,
            subOrgKey: $subOrgKey,
        );
    }

    // ── Core Flow ──────────────────────────────────────────────────────────────

    private function buildSignAndBroadcast(
        string $from,
        string $to,         // 40-char hex without 0x
        string $valueWei,   // decimal string
        string $data,       // hex without 0x, or ''
        string $subOrgId,
        string $subOrgKey
    ): string {
        $nonce       = $this->getNonce($from);
        $gasPrice    = $this->getGasPrice();
        $gasEstimate = $this->estimateGas($from, $to, $valueWei, $data);
        // 20% buffer, min 21000
        $gasLimit    = (string) max(21000, (int)((int)$gasEstimate * 120 / 100));

        Log::info("[Trade] nonce={$nonce} gasPrice={$gasPrice} gasLimit={$gasLimit}");

        $unsignedHex = $this->buildUnsignedTx($nonce, $gasPrice, $gasLimit, $to, $valueWei, $data);
        $signedHex   = $this->signWithTurnkey($unsignedHex, $from, $subOrgId, $subOrgKey);

        return $this->broadcastTx($signedHex);
    }

    // ── Transaction Builder ────────────────────────────────────────────────────

    /**
     * Build EIP-155 unsigned transaction and return hex string.
     * RLP([nonce, gasPrice, gasLimit, to, value, data, chainId=56, 0, 0])
     */
    private function buildUnsignedTx(
        string $nonce,
        string $gasPrice,
        string $gasLimit,
        string $to,       // 40-char hex, no 0x
        string $valueWei, // decimal
        string $data      // hex, no 0x, or ''
    ): string {
        $to   = strtolower(ltrim($to, '0x'));
        $data = strtolower(ltrim($data, '0x'));

        $items = [
            $this->rlpHexInt($nonce),
            $this->rlpHexInt($gasPrice),
            $this->rlpHexInt($gasLimit),
            $this->rlpBytes(hex2bin($to)),               // address = 20 bytes
            $this->rlpDecInt($valueWei),
            $data !== '' ? $this->rlpBytes(hex2bin($data)) : "\x80",
            $this->rlpHexInt(dechex(self::CHAIN_ID)),    // 56 = 0x38
            "\x80",                                       // v placeholder
            "\x80",                                       // r placeholder
        ];

        return bin2hex($this->rlpList($items));
    }

    // ── RLP Encoding ──────────────────────────────────────────────────────────

    /** RLP-encode an integer given as a hex string (no 0x). */
    private function rlpHexInt(string $hex): string
    {
        $hex = ltrim(strtolower($hex), '0');
        if ($hex === '') return "\x80";
        if (strlen($hex) % 2 !== 0) $hex = '0' . $hex;
        return $this->rlpBytes(hex2bin($hex));
    }

    /** RLP-encode an integer given as a decimal string. */
    private function rlpDecInt(string $dec): string
    {
        if ($dec === '0' || $dec === '') return "\x80";
        $hex = gmp_strval(gmp_init($dec, 10), 16);
        return $this->rlpHexInt($hex);
    }

    /** RLP-encode a byte string. */
    private function rlpBytes(string $bytes): string
    {
        $len = strlen($bytes);
        if ($len === 0) return "\x80";
        if ($len === 1 && ord($bytes[0]) < 0x80) return $bytes;
        if ($len <= 55) return chr(0x80 + $len) . $bytes;
        $lb = $this->bigEndianBytes($len);
        return chr(0xb7 + strlen($lb)) . $lb . $bytes;
    }

    /** RLP-encode a list of already-encoded items. */
    private function rlpList(array $encoded): string
    {
        $payload = implode('', $encoded);
        $len = strlen($payload);
        if ($len <= 55) return chr(0xc0 + $len) . $payload;
        $lb = $this->bigEndianBytes($len);
        return chr(0xf7 + strlen($lb)) . $lb . $payload;
    }

    private function bigEndianBytes(int $n): string
    {
        $hex = dechex($n);
        if (strlen($hex) % 2 !== 0) $hex = '0' . $hex;
        return hex2bin($hex);
    }

    // ── ABI Encoding ──────────────────────────────────────────────────────────

    /**
     * swapExactETHForTokens(uint256 amountOutMin, address[] path, address to, uint256 deadline)
     * Selector: 7ff36ab5
     */
    private function swapExactEthForTokensData(
        string $amountOutMin,
        string $tokenAddress,
        string $toAddress,
        string $deadline
    ): string {
        $wbnb  = strtolower(ltrim(self::WBNB_ADDRESS, '0x'));
        $token = strtolower(ltrim($tokenAddress, '0x'));
        $to    = strtolower(ltrim($toAddress, '0x'));

        // ABI layout: 4 static head slots (128 bytes), path is dynamic
        // path offset = 128 = 0x80
        return '7ff36ab5'
            . $this->abiUint256($amountOutMin, true)  // decimal
            . str_pad('80', 64, '0', STR_PAD_LEFT)   // offset to path
            . str_pad($to, 64, '0', STR_PAD_LEFT)     // to address
            . $this->abiUint256($deadline, true)       // deadline (decimal)
            . str_pad('2', 64, '0', STR_PAD_LEFT)     // path.length = 2
            . str_pad($wbnb, 64, '0', STR_PAD_LEFT)   // path[0] WBNB
            . str_pad($token, 64, '0', STR_PAD_LEFT); // path[1] token
    }

    /**
     * transfer(address to, uint256 amount)
     * Selector: a9059cbb
     */
    private function erc20TransferData(string $toAddress, string $amountWei): string
    {
        $to = strtolower(ltrim($toAddress, '0x'));
        return 'a9059cbb'
            . str_pad($to, 64, '0', STR_PAD_LEFT)
            . $this->abiUint256($amountWei, true);
    }

    /** Encode integer as ABI uint256 (64 hex chars). $isDecimal = true for decimal strings. */
    private function abiUint256(string $value, bool $isDecimal = false): string
    {
        $hex = $isDecimal
            ? gmp_strval(gmp_init($value, 10), 16)
            : ltrim($value, '0x');
        return str_pad(strtolower($hex), 64, '0', STR_PAD_LEFT);
    }

    // ── BSC RPC ────────────────────────────────────────────────────────────────

    private function bscRpc(array $payload): mixed
    {
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(15)
            ->post(self::BSC_RPC, $payload);

        if (!$response->successful()) {
            // Try backup RPC
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(15)
                ->post(self::BSC_RPC_ALT, $payload);
        }

        $data = $response->json();
        if (isset($data['error'])) {
            throw new \Exception('BSC RPC: ' . json_encode($data['error']));
        }
        return $data['result'];
    }

    private function getNonce(string $address): string
    {
        $hex = $this->bscRpc([
            'jsonrpc' => '2.0',
            'method'  => 'eth_getTransactionCount',
            'params'  => ['0x' . strtolower(ltrim($address, '0x')), 'pending'],
            'id'      => 1,
        ]);
        return ltrim(strtolower($hex), '0x') ?: '0';
    }

    private function getGasPrice(): string
    {
        $hex = $this->bscRpc([
            'jsonrpc' => '2.0',
            'method'  => 'eth_gasPrice',
            'params'  => [],
            'id'      => 1,
        ]);
        return ltrim(strtolower($hex), '0x') ?: '1';
    }

    private function estimateGas(string $from, string $to, string $valueWei, string $data): string
    {
        try {
            $params = [
                'from'  => '0x' . strtolower(ltrim($from, '0x')),
                'to'    => '0x' . strtolower(ltrim($to, '0x')),
                'value' => '0x' . ($valueWei !== '0' && $valueWei !== ''
                    ? gmp_strval(gmp_init($valueWei, 10), 16)
                    : '0'),
            ];
            if ($data !== '') {
                $params['data'] = '0x' . $data;
            }

            $hex = $this->bscRpc([
                'jsonrpc' => '2.0',
                'method'  => 'eth_estimateGas',
                'params'  => [$params],
                'id'      => 1,
            ]);
            return (string)hexdec(ltrim($hex, '0x'));
        } catch (\Exception $e) {
            Log::warning('[Trade] estimateGas fallback: ' . $e->getMessage());
            return '300000';
        }
    }

    private function getAmountsOut(string $bnbAmountWei, string $tokenAddress): string
    {
        // getAmountsOut(uint256 amountIn, address[] path) — selector: d06ca61f
        $wbnb     = strtolower(ltrim(self::WBNB_ADDRESS, '0x'));
        $token    = strtolower(ltrim($tokenAddress, '0x'));
        $amtHex   = gmp_strval(gmp_init($bnbAmountWei, 10), 16);

        $calldata = 'd06ca61f'
            . $this->abiUint256($amtHex)      // amountIn
            . str_pad('40', 64, '0', STR_PAD_LEFT)  // offset to path = 64
            . str_pad('2', 64, '0', STR_PAD_LEFT)   // path.length
            . str_pad($wbnb, 64, '0', STR_PAD_LEFT)
            . str_pad($token, 64, '0', STR_PAD_LEFT);

        $result = $this->bscRpc([
            'jsonrpc' => '2.0',
            'method'  => 'eth_call',
            'params'  => [[
                'to'   => '0x' . strtolower(self::ROUTER_ADDRESS),
                'data' => '0x' . $calldata,
            ], 'latest'],
            'id' => 1,
        ]);

        // Decode uint256[] return: word0=offset, word1=length, word2=amounts[0], word3=amounts[1]
        $hex        = ltrim(strtolower($result), '0x');
        $amountHex  = ltrim(substr($hex, 192, 64), '0');
        if ($amountHex === '') return '0';
        return gmp_strval(gmp_init($amountHex, 16), 10);
    }

    private function applySlippage(string $amount, int $bps): string
    {
        return (string)(int)((int)$amount * (10000 - $bps) / 10000);
    }

    private function broadcastTx(string $signedHex): string
    {
        $result = $this->bscRpc([
            'jsonrpc' => '2.0',
            'method'  => 'eth_sendRawTransaction',
            'params'  => ['0x' . $signedHex],
            'id'      => 1,
        ]);
        return $result; // tx hash
    }

    // ── Turnkey Signing ────────────────────────────────────────────────────────

    private function signWithTurnkey(string $unsignedTxHex, string $walletAddress, string $subOrgId, string $subOrgKey): string
    {
        $payload = [
            'type'           => 'ACTIVITY_TYPE_SIGN_TRANSACTION_V2',
            'timestampMs'    => (string)(int)(microtime(true) * 1000),
            'organizationId' => $subOrgId,
            'parameters'     => [
                'signWith'            => $walletAddress,
                'unsignedTransaction' => $unsignedTxHex,
                'type'                => 'TRANSACTION_TYPE_ETHEREUM',
            ],
        ];

        $data   = $this->turnkeyPostWithKey('/public/v1/submit/sign_transaction', $payload, $subOrgKey);
        $result = $data['activity']['result']['signTransactionResult'] ?? null;

        if (!$result || empty($result['signedTransaction'])) {
            throw new \Exception('Turnkey signTransaction failed: ' . json_encode($data));
        }

        return ltrim($result['signedTransaction'], '0x');
    }

    // Signs a Turnkey request using a specified hex private key (for sub-org operations).
    private function turnkeyPostWithKey(string $path, array $payload, string $hexPrivKey): array
    {
        $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $stamp    = $this->makeStampWithKey($bodyJson, $hexPrivKey);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
            'X-Stamp'      => $stamp,
        ])->withBody($bodyJson, 'application/json')
          ->timeout(30)
          ->post('https://api.turnkey.com' . $path);

        if (!$response->successful()) {
            throw new \Exception('Turnkey HTTP error: ' . $response->body());
        }

        return $response->json();
    }

    private function makeStampWithKey(string $bodyJson, string $hexPrivKey): string
    {
        $pem     = $this->buildPem($hexPrivKey);
        $key     = openssl_pkey_get_private($pem);
        if (!$key) throw new \Exception('Failed to load sub-org PEM');

        // Derive compressed public key for the stamp header
        $details = openssl_pkey_get_details($key);
        $pubDer  = base64_decode(
            str_replace(['-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----', "\n", "\r"], '', $details['key'])
        );
        $point  = bin2hex(substr($pubDer, -65)); // 04 + x(64) + y(64)
        $y      = substr($point, 66, 64);
        $compPub = (hexdec(substr($y, -2)) % 2 === 0 ? '02' : '03') . substr($point, 2, 64);

        $derSig = '';
        if (!openssl_sign($bodyJson, $derSig, $key, OPENSSL_ALGO_SHA256)) {
            throw new \Exception('ECDSA sign failed');
        }

        $stamp = json_encode([
            'publicKey' => $compPub,
            'scheme'    => 'SIGNATURE_SCHEME_TK_API_P256',
            'signature' => strtolower(bin2hex($derSig)),
        ], JSON_UNESCAPED_SLASHES);

        return rtrim(strtr(base64_encode($stamp), '+/', '-_'), '=');
    }

    // ── Turnkey Infrastructure ─────────────────────────────────────────────────

    private function getTurnkeyCredentials(): array
    {
        return Cache::remember('turnkey_creds', 3600, function () {
            $client = new SecretsManagerClient(['version' => 'latest', 'region' => 'eu-north-1']);
            $result = $client->getSecretValue(['SecretId' => 'moonlaunch/turnkey/backend']);
            $secret = json_decode($result['SecretString'], true);

            $orgId      = $secret['TURNKEY_ORG_ID']           ?? null;
            $publicKey  = $secret['TURNKEY_API_PUBLIC_KEY']    ?? null;
            $privateKey = $secret['TURNKEY_API_PRIVATE_KEY']   ?? null;

            if (!$orgId || !$publicKey || !$privateKey) {
                throw new \Exception('Missing Turnkey credentials');
            }

            return [
                'org_id'      => $orgId,
                'public_key'  => preg_replace('/^0x/i', '', trim($publicKey)),
                'private_key' => preg_replace('/^0x/i', '', trim($privateKey)),
            ];
        });
    }

    private function turnkeyPost(string $path, array $payload): array
    {
        $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $stamp    = $this->makeTurnkeyStamp($bodyJson);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
            'X-Stamp'      => $stamp,
        ])->withBody($bodyJson, 'application/json')
          ->timeout(30)
          ->post('https://api.turnkey.com' . $path);

        if (!$response->successful()) {
            throw new \Exception('Turnkey HTTP error: ' . $response->body());
        }

        return $response->json();
    }

    private function makeTurnkeyStamp(string $bodyJson): string
    {
        $pem = $this->buildPem($this->privateKey);
        $key = openssl_pkey_get_private($pem);
        if (!$key) throw new \Exception('Failed to load Turnkey PEM');

        $derSig = '';
        if (!openssl_sign($bodyJson, $derSig, $key, OPENSSL_ALGO_SHA256)) {
            throw new \Exception('ECDSA sign failed');
        }

        $stamp = json_encode([
            'publicKey' => $this->publicKey,
            'scheme'    => 'SIGNATURE_SCHEME_TK_API_P256',
            'signature' => strtolower(bin2hex($derSig)),
        ], JSON_UNESCAPED_SLASHES);

        return rtrim(strtr(base64_encode($stamp), '+/', '-_'), '=');
    }

    private function buildPem(string $hexKey): string
    {
        $raw = hex2bin(preg_replace('/^0x/i', '', trim($hexKey)));

        $oidEcPub   = "\x06\x07\x2a\x86\x48\xce\x3d\x02\x01";
        $oidP256    = "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07";
        $ecPrivBody = "\x02\x01\x01" . "\x04\x20" . $raw
            . "\xa0" . chr(strlen($oidP256)) . $oidP256;
        $ecPrivKey  = "\x30" . chr(strlen($ecPrivBody)) . $ecPrivBody;

        $algoSeq    = "\x30" . chr(strlen($oidEcPub . $oidP256)) . $oidEcPub . $oidP256;
        $pkcs8Body  = "\x02\x01\x00" . $algoSeq
            . "\x04" . chr(strlen($ecPrivKey)) . $ecPrivKey;
        $pkcs8Der   = "\x30" . chr(strlen($pkcs8Body)) . $pkcs8Body;

        return "-----BEGIN PRIVATE KEY-----\n"
            . chunk_split(base64_encode($pkcs8Der), 64, "\n")
            . "-----END PRIVATE KEY-----\n";
    }
}
