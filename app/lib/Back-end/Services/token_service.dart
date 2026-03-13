import 'dart:convert';
import 'package:http/http.dart' as http;

class TokenModel {
  final String tokenAddress;
  final String? pairAddress;
  final String? name;
  final String? symbol;
  final String? decimals;
  final String? logo;
  final String? initialPrice;
  final String? priceCurrency;
  final String? detectedAt;
  final String? txHash;
  final String sourceType;
  final String tradeMode;
  final String status;
  final bool isTradeable;

  TokenModel({
    required this.tokenAddress,
    this.pairAddress,
    this.name,
    this.symbol,
    this.decimals,
    this.logo,
    this.initialPrice,
    this.priceCurrency,
    this.detectedAt,
    this.txHash,
    this.sourceType = 'pancakeswap',
    this.tradeMode = 'router_swap',
    this.status = 'active',
    this.isTradeable = true,
  });

  factory TokenModel.fromJson(Map<String, dynamic> json) {
    return TokenModel(
      tokenAddress: json['token_address'] as String? ?? '',
      pairAddress: json['pair_address'] as String?,
      name: json['name'] as String?,
      symbol: json['symbol'] as String?,
      decimals: json['decimals']?.toString(),
      logo: json['logo'] as String?,
      initialPrice: json['initial_price']?.toString(),
      priceCurrency: json['price_currency'] as String?,
      detectedAt: json['detected_at'] as String?,
      txHash: json['tx_hash'] as String?,
      sourceType: json['source_type'] as String? ?? 'pancakeswap',
      tradeMode: json['trade_mode'] as String? ?? 'router_swap',
      status: json['status'] as String? ?? 'active',
      isTradeable: json['is_tradeable'] == true || json['is_tradeable'] == 1,
    );
  }

  String get displayName => name ?? symbol ?? tokenAddress.substring(0, 8);
  String get displayPrice =>
      initialPrice != null ? '\$$initialPrice' : '--';
}

class TokenService {
  static const String _base = 'https://backend.moonlaunchapp.com/api';

  static Future<List<TokenModel>> getNewLaunches({
    int limit = 20,
    int offset = 0,
  }) async {
    final uri = Uri.parse('$_base/new-launches?limit=$limit&offset=$offset');

    final res = await http.get(uri, headers: {'Accept': 'application/json'});

    if (res.statusCode == 200) {
      final decoded = jsonDecode(res.body) as Map<String, dynamic>;
      final data = decoded['data'] as List<dynamic>? ?? [];
      return data
          .map((e) => TokenModel.fromJson(e as Map<String, dynamic>))
          .toList();
    }
    throw 'Failed to load launches (${res.statusCode})';
  }

  static Future<TokenModel> getTokenDetail(String address) async {
    final uri = Uri.parse('$_base/token/$address');

    final res = await http.get(uri, headers: {'Accept': 'application/json'});

    if (res.statusCode == 200) {
      final decoded = jsonDecode(res.body) as Map<String, dynamic>;
      return TokenModel.fromJson(decoded['data'] as Map<String, dynamic>);
    }
    if (res.statusCode == 404) throw 'Token not found';
    throw 'Failed to load token detail (${res.statusCode})';
  }
}
