import 'dart:convert';
import 'package:http/http.dart' as http;

class WalletTokenModel {
  final String tokenAddress;
  final String? name;
  final String? symbol;
  final String? logo;
  final int decimals;
  final String balance;
  final String sourceType;

  WalletTokenModel({
    required this.tokenAddress,
    this.name,
    this.symbol,
    this.logo,
    this.decimals = 18,
    required this.balance,
    this.sourceType = 'pancakeswap',
  });

  factory WalletTokenModel.fromJson(Map<String, dynamic> json) {
    return WalletTokenModel(
      tokenAddress: json['token_address'] as String? ?? '',
      name: json['name'] as String?,
      symbol: json['symbol'] as String?,
      logo: json['logo'] as String?,
      decimals: (json['decimals'] as num?)?.toInt() ?? 18,
      balance: json['balance']?.toString() ?? '0',
      sourceType: json['source_type'] as String? ?? 'pancakeswap',
    );
  }

  String get displayName => name ?? symbol ?? tokenAddress.substring(0, 8);
}

class WalletBalanceModel {
  final String address;
  final String bnbBalance;
  final List<WalletTokenModel> tokens;

  WalletBalanceModel({
    required this.address,
    required this.bnbBalance,
    required this.tokens,
  });

  factory WalletBalanceModel.fromJson(Map<String, dynamic> json) {
    final tokenList = (json['tokens'] as List<dynamic>? ?? [])
        .map((t) => WalletTokenModel.fromJson(t as Map<String, dynamic>))
        .toList();
    return WalletBalanceModel(
      address: json['address'] as String? ?? '',
      bnbBalance: json['bnb_balance']?.toString() ?? '0',
      tokens: tokenList,
    );
  }

  String get displayBnb {
    try {
      final val = double.parse(bnbBalance);
      return val.toStringAsFixed(4);
    } catch (_) {
      return bnbBalance;
    }
  }
}

class WalletService {
  static const String _base = 'https://backend.moonlaunchapp.com/api';

  static Future<WalletBalanceModel> getBalance(String address) async {
    final uri =
        Uri.parse('$_base/wallet/balance?address=${Uri.encodeComponent(address)}');

    final res = await http.get(uri, headers: {'Accept': 'application/json'});

    if (res.statusCode == 200) {
      final decoded = jsonDecode(res.body) as Map<String, dynamic>;
      return WalletBalanceModel.fromJson(decoded);
    }
    throw 'Failed to load wallet balance (${res.statusCode})';
  }
}
