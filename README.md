<div align="center">

```
╔╦╗╔═╗╔═╗╔╗╔╦  ╔═╗╦ ╦╔╗╔╔═╗╦ ╦
║║║║ ║║ ║║║║║  ╠═╣║ ║║║║║  ╠═╣
╩ ╩╚═╝╚═╝╝╚╝╩═╝╩ ╩╚═╝╝╚╝╚═╝╩ ╩
```

**Discover. Trade. Launch.**
*A self-custodial mobile app for catching newly launched tokens on BNB Smart Chain — the moment they hit PancakeSwap.*

[![Flutter](https://img.shields.io/badge/Flutter-3.x-02569B?style=flat-square&logo=flutter)](https://flutter.dev)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php)](https://php.net)
[![BSC](https://img.shields.io/badge/BNB_Smart_Chain-mainnet-F0B90B?style=flat-square&logo=binance)](https://bscscan.com)
[![AWS](https://img.shields.io/badge/AWS-EC2_+_RDS-232F3E?style=flat-square&logo=amazon-aws)](https://aws.amazon.com)
[![License](https://img.shields.io/badge/License-Private-red?style=flat-square)](#license)

</div>

---

## What is MoonLaunch?

MoonLaunch listens to the PancakeSwap V2 `PairCreated` event stream in real time. The moment a new token launches on BSC, it gets enriched with metadata and pricing — and surfaced to users who can **buy it instantly** from their phone using a non-custodial wallet they own.

No seed phrase copying. No browser extensions. No CEX middleman. Just: see token, tap buy, tokens land in your wallet.

---

## Table of Contents

- [Features](#features)
- [Architecture](#architecture)
- [Tech Stack](#tech-stack)
- [Repository Structure](#repository-structure)
- [Getting Started](#getting-started)
- [API Reference](#api-reference)
- [Wallet & Security Model](#wallet--security-model)
- [Data Flow](#data-flow)
- [Background Jobs](#background-jobs)
- [Phase 2 Roadmap](#phase-2-roadmap)
- [License](#license)

---

## Features

### Authentication
- Email-based signup with OTP verification (via Postmark)
- Login via OTP or email + password
- Secure password reset flow

### Self-Custodial Wallet
- Wallet auto-created on signup via **Turnkey** sub-organization model
- User owns the wallet — the backend never holds private keys in plaintext
- Supports BNB (native) and all BEP-20 tokens

### Token Discovery Feed
- Real-time ingestion of new PancakeSwap V2 pairs via webhook
- Background enrichment: Moralis metadata + DexScreener pricing fallback
- Token feed sorted by price availability — priced tokens shown first

### Trading
- **Buy** — swap BNB → token via PancakeSwap V2 Router (`swapExactETHForTokens`)
- **Send** — transfer BNB or any ERC-20 to any BSC address
- **Receive** — display wallet address as a scannable QR code

### Portfolio
- View BNB balance + all ERC-20 holdings
- Prices fetched in parallel from Moralis and DexScreener for speed
- Background price updater refreshes the DB every 60 seconds for instant responses

---

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     Mobile App (Flutter)                │
│   Auth · Token Feed · Portfolio · Buy · Send · Receive  │
└─────────────────────┬───────────────────────────────────┘
                      │ HTTPS REST
┌─────────────────────▼───────────────────────────────────┐
│                  Laravel 12 API (EC2)                   │
│                                                         │
│  Controllers   Services          Jobs                   │
│  ──────────    ────────          ────                   │
│  Auth          MoralisService    EnrichTokenJob         │
│  Token         TradingService    PriceUpdaterJob        │
│  Wallet        TurnkeyService                           │
│  Trade                                                  │
│  Webhook                                                │
└──────┬──────────────┬──────────────┬────────────────────┘
       │              │              │
  ┌────▼────┐   ┌─────▼─────┐  ┌────▼──────────┐
  │ MySQL   │   │  Turnkey  │  │  Moralis API  │
  │ AWS RDS │   │  Wallets  │  │  DexScreener  │
  └─────────┘   └───────────┘  └───────────────┘
                                        ▲
                               PancakeSwap V2
                               PairCreated Webhook
```

---

## Tech Stack

| Layer | Technology | Purpose |
|---|---|---|
| Mobile | Flutter 3 / Dart 3 | iOS & Android client |
| Backend API | Laravel 12 / PHP 8.2 | REST API + job queue |
| Database | MySQL on AWS RDS | Persistent storage |
| Wallet | Turnkey (secp256k1) | Self-custodial key management |
| Token Data | Moralis Web3 API | Metadata, balances, pricing |
| Price Fallback | DexScreener | Prices for tokens Moralis can't find |
| Token Feed | PancakeSwap V2 Webhook | Real-time new pair detection |
| Trading | PancakeSwap V2 Router | On-chain swap execution |
| Email | Postmark | OTP & transactional email |
| Hosting | AWS EC2 (eu-north-1) | API server |
| Secrets | AWS Secrets Manager | API keys, private credentials |
| Transactions | EIP-155 (BSC chain ID 56) | Legacy-compatible BSC signing |

---

## Repository Structure

```
moonlaunch-core/
├── app/                  Flutter mobile app (iOS & Android)
│   ├── lib/
│   │   ├── main.dart
│   │   ├── Front-end/
│   │   │   ├── views/            Screens (home, coin detail, wallet, etc.)
│   │   │   ├── auth_screens/     Login, signup, OTP, reset password
│   │   │   └── widgets/          Shared UI components
│   │   └── Back-end/             API client, state management
│   └── pubspec.yaml
│
├── backend/              Laravel 12 REST API
│   ├── app/
│   │   ├── Http/Controllers/     Auth, Token, Wallet, Trade, Webhook
│   │   ├── Services/             Moralis, Trading, Turnkey
│   │   ├── Jobs/                 EnrichTokenJob, PriceUpdaterJob
│   │   └── Models/
│   ├── database/migrations/
│   └── routes/api.php
│
├── admin/                Admin dashboard (planned)
├── contracts/            Smart contracts (planned)
├── db/                   Database schema reference
├── infra/                AWS infrastructure docs
└── docs/                 Engineering directives (private, not tracked)
```

---

## Getting Started

### Prerequisites

- PHP 8.2+, Composer
- Flutter 3.x, Dart 3.x
- MySQL database (local or RDS)
- AWS account with Secrets Manager entries configured

### Backend Setup

```bash
cd backend
composer install
cp .env.example .env
```

Edit `.env` with your database credentials and AWS region, then:

```bash
php artisan key:generate
php artisan migrate
php artisan serve
```

**Required AWS Secrets Manager entries:**

| Secret Name | Keys Required |
|---|---|
| `moonlaunch/turnkey/backend` | `TURNKEY_ORG_ID`, `TURNKEY_API_PUBLIC_KEY`, `TURNKEY_API_PRIVATE_KEY` |
| `moonlaunch/postmark/stockholm/prod/server-token` | `POSTMARK_SERVER_TOKEN` |
| `moralis/api-key` | `api_key` |

### Flutter App Setup

```bash
cd app
flutter pub get
flutter run
```

Targets iOS and Android. Requires Flutter 3.x and Dart 3.x.

---

## API Reference

### Authentication

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/signup` | Register with email (sends OTP) |
| `POST` | `/api/verify-otp` | Verify OTP → create user + Turnkey wallet |
| `POST` | `/api/request_login` | Request login OTP |
| `POST` | `/api/verify-login-otp` | Verify login OTP → return token |
| `POST` | `/api/login` | Login with email + password |
| `POST` | `/api/reset_password` | Request password reset |
| `POST` | `/api/change_password` | Change password (authenticated) |

### Tokens

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/new-launches` | Paginated PancakeSwap new launches |
| `GET` | `/api/token/{address}` | Token detail + price |

### Wallet

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/wallet/balance` | BNB balance + ERC-20 holdings |
| `POST` | `/api/wallet/send` | Transfer BNB or ERC-20 to address |

### Trading

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/trade/buy` | Swap BNB → token via PancakeSwap V2 |

### Internal

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/webhook/pancakeswap` | PairCreated webhook (internal use) |

---

## Wallet & Security Model

MoonLaunch uses [Turnkey](https://turnkey.com) for key management. The model:

1. On signup, a **Turnkey sub-organization** is created for the user.
2. The sub-org contains a secp256k1 key pair — the BSC wallet address.
3. The backend (parent org API keys) can **request signatures** from the sub-org.
4. Private keys are **never extracted** — signing happens inside Turnkey's secure enclave.
5. Users control their wallet. The backend facilitates but does not own funds.

Transactions use **EIP-155 encoding** with BSC chain ID `56` for replay protection.

---

## Data Flow

### New Token Launch

```
PancakeSwap V2 emits PairCreated event
        │
        ▼
POST /api/webhook/pancakeswap
        │
        ▼
Token saved to DB (raw pair data)
        │
        ▼
EnrichTokenJob dispatched (async)
        │
        ▼
Moralis: fetch name, symbol, decimals, logo, price
        │
        ▼ (fallback if Moralis has no price)
DexScreener: fetch USD price
        │
        ▼
Token record updated → appears in /api/new-launches
```

### Buy Flow

```
User taps "Buy" in app
        │
        ▼
POST /api/trade/buy { token_address, bnb_amount }
        │
        ▼
Backend builds swapExactETHForTokens calldata
        │
        ▼
Turnkey signs the transaction for user's sub-org wallet
        │
        ▼
Signed tx broadcast to BSC mainnet
        │
        ▼
Tx hash returned to app
```

---

## Background Jobs

| Job | Schedule | Purpose |
|---|---|---|
| `EnrichTokenJob` | On-demand (webhook trigger) | Fetch and store token metadata + initial price |
| `PriceUpdaterJob` | Every 60 seconds | Refresh prices in DB so API responses are instant |

Prices are fetched **in parallel** from Moralis and DexScreener — whichever resolves first wins.

---

## Phase 2 Roadmap

The schema is already built to support Phase 2 without a rebuild:

- **`source_type`** field on tokens — currently `pancakeswap`, will expand to other DEXes and launchpad platforms
- **`trade_mode`** field — currently `router_swap`, will support MoonLaunch internal liquidity pools
- Admin dashboard (`/admin`) for token moderation and analytics
- Smart contracts for MoonLaunch-native liquidity mechanisms

---

## License

Private — all rights reserved.
Unauthorized use, reproduction, or distribution is prohibited.
See `docs/` for IP ownership and contractual details.
