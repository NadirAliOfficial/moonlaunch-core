# MoonLaunch

MoonLaunch is a mobile application for discovering and trading newly launched PancakeSwap tokens on BNB Smart Chain (BSC). Users sign up, get a self-custodial wallet powered by Turnkey, and can buy/send tokens directly from their phone.

## Repository Structure

```
moonlaunch-core/
├── app/          Flutter mobile app (iOS & Android)
├── backend/      Laravel 12 REST API (PHP 8.2, AWS EC2 + RDS)
├── admin/        Admin dashboard (planned)
├── contracts/    Smart contracts (planned)
├── db/           Database schemas & migrations reference
├── infra/        AWS infrastructure docs
└── docs/         Engineering directives (private, not tracked)
```

---

## Tech Stack

| Layer | Technology |
|---|---|
| Mobile | Flutter 3 (Dart) |
| Backend API | Laravel 12 / PHP 8.2 |
| Database | MySQL (AWS RDS) |
| Wallet | Turnkey (self-custodial, secp256k1) |
| Token data | Moralis Web3 API |
| Token feed | PancakeSwap V2 PairCreated webhook |
| Trading | PancakeSwap V2 Router (BSC mainnet) |
| Email | Postmark |
| Hosting | AWS EC2 (eu-north-1) |
| Secrets | AWS Secrets Manager |

---

## Phase 1 Features (Current)

- **Auth** — email signup with OTP (Postmark), login, password reset
- **Wallets** — auto-created Turnkey sub-org wallet on signup (BSC address)
- **Token Feed** — live PancakeSwap launches via webhook → `EnrichTokenJob` → Moralis metadata
- **Wallet Balance** — BNB + ERC-20 holdings via Moralis
- **Buy** — swap BNB → token via PancakeSwap V2 (`swapExactETHForTokens`), signed by Turnkey
- **Send** — transfer BNB or ERC-20 to any BSC address, signed by Turnkey
- **Receive** — QR code display of wallet address

---

## Getting Started

### Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env
# Fill in DB credentials and AWS region in .env
php artisan key:generate
php artisan migrate
php artisan serve
```

**Required AWS Secrets Manager entries:**
| Secret Name | Keys |
|---|---|
| `moonlaunch/turnkey/backend` | `TURNKEY_ORG_ID`, `TURNKEY_API_PUBLIC_KEY`, `TURNKEY_API_PRIVATE_KEY` |
| `moonlaunch/postmark/stockholm/prod/server-token` | `POSTMARK_SERVER_TOKEN` |
| `moralis/api-key` | `api_key` |

### Flutter App

```bash
cd app
flutter pub get
flutter run
```

Requires Flutter 3.x and Dart 3.x. The app targets iOS and Android.

---

## API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/signup` | Register (sends OTP) |
| POST | `/api/verify-otp` | Confirm OTP → create user + Turnkey wallet |
| POST | `/api/request_login` | Login (sends OTP) |
| POST | `/api/verify-login-otp` | Confirm login OTP |
| POST | `/api/login` | Simple login (email + password) |
| POST | `/api/reset_password` | Reset password |
| POST | `/api/change_password` | Change password |
| GET | `/api/new-launches` | PancakeSwap new token launches |
| GET | `/api/token/{address}` | Token detail |
| GET | `/api/wallet/balance` | BNB + token balances |
| POST | `/api/trade/buy` | Buy token with BNB (PancakeSwap V2) |
| POST | `/api/wallet/send` | Send BNB or ERC-20 to an address |
| POST | `/api/webhook/pancakeswap` | PancakeSwap PairCreated webhook (internal) |

---

## Architecture Notes

- All tokens carry `source_type` (`pancakeswap`) and `trade_mode` (`router_swap`) fields for Phase 2 expansion (MoonLaunch internal liquidity pools) without requiring a rebuild.
- Turnkey creates a sub-organization per user. The backend (parent org API keys) signs transactions on behalf of each sub-org wallet.
- Transaction signing uses EIP-155 legacy transactions on BSC (chain ID 56).

---

## License

Private — all rights reserved. See `docs/` for IP ownership details.
