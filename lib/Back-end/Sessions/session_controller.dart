import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class SessionController {
  SessionController._();
  static final SessionController instance = SessionController._();

  static const _kLoggedIn = "ml_logged_in";
  static const _kGuideSeen = "ml_guide_seen";
  static const _kToken = "ml_token";
  static const _kUser = "ml_user_json";

  bool _isLoggedIn = false;
  bool _guideSeen = false;

  String _token = "";
  Map<String, dynamic> _user = {};

  bool get isLoggedIn => _isLoggedIn;
  bool get guideSeen => _guideSeen;

  String get token => _token;
  Map<String, dynamic> get user => _user;

  // Safe getters
  String get id => (_user["id"] ?? _user["_id"] ?? "").toString();
  String get name => (_user["name"] ?? _user["username"] ?? "").toString();
  String get email => (_user["email"] ?? "").toString();
  String get phone => (_user["phone"] ?? _user["phone_number"] ?? "").toString();

  // New getters from API response
  String get status => (_user["status"] ?? "").toString();
  String get walletAddress => (_user["wallet_address"] ?? "").toString();
  String get turnkeyWalletId => (_user["turnkey_wallet_id"] ?? "").toString();
  String get turnkeySuborgId => (_user["turnkey_suborg_id"] ?? "").toString();
  String get turnkeyPrivateKeyId => (_user["turnkey_private_key_id"] ?? "").toString();
  String get emailVerifiedAt => (_user["email_verified_at"] ?? "").toString();
  String get createdAt => (_user["created_at"] ?? "").toString();

  /// base64 image string saved from API / edit profile
  String get profileImageBase64 =>
      (_user["profile_pick"] ?? _user["image"] ?? _user["profile_image"] ?? _user["avatar"] ?? "")
          .toString();

  Future<void> init() async {
    final sp = await SharedPreferences.getInstance();

    _isLoggedIn = sp.getBool(_kLoggedIn) ?? false;
    _guideSeen = sp.getBool(_kGuideSeen) ?? false;
    _token = sp.getString(_kToken) ?? "";

    final userStr = sp.getString(_kUser) ?? "";
    if (userStr.isNotEmpty) {
      try {
        final decoded = jsonDecode(userStr);
        if (decoded is Map<String, dynamic>) {
          _user = decoded;
        } else {
          _user = {};
        }
      } catch (_) {
        _user = {};
      }
    } else {
      _user = {};
    }

    // Safety Fix: agar loggedIn true hai but user empty hai to logout
    if (_isLoggedIn == true && _user.isEmpty) {
      await logout();
    }
  }

  Future<void> setGuideSeen(bool seen) async {
    _guideSeen = seen;
    final sp = await SharedPreferences.getInstance();
    await sp.setBool(_kGuideSeen, seen);
  }

  /// Save user session after successful login OTP
  Future<void> saveSessionFromApi(Map<String, dynamic> res) async {
    String t = "";

    final tokenCandidates = [
      res["token"],
      res["access_token"],
      res["accessToken"],
      res["data"]?["token"],
      res["data"]?["access_token"],
      res["data"]?["accessToken"],
    ];

    for (final c in tokenCandidates) {
      if (c != null && c.toString().trim().isNotEmpty) {
        t = c.toString();
        break;
      }
    }

    // Extract user object — supports flat response like your API returns
    Map<String, dynamic> u = {};
    dynamic uCandidate =
        res["user"] ??
        res["data"]?["user"] ??
        res["data"]?["data"]?["user"] ??
        res["data"]?["profile"] ??
        res["profile"];

    if (uCandidate is Map<String, dynamic>) {
      u = uCandidate;
    } else if (uCandidate is Map) {
      u = Map<String, dynamic>.from(uCandidate);
    }

    // Agar user object nahi mila to flat response ko hi user maan lo
    if (u.isEmpty) {
      // Try to build user from flat fields if present
      if (res["id"] != null || res["email"] != null) {
        u = Map<String, dynamic>.from(res);
      } else {
        u = {"raw": res};
      }
    }

    _token = t;
    _user = u;
    _isLoggedIn = true;

    final sp = await SharedPreferences.getInstance();
    await sp.setBool(_kLoggedIn, true);
    await sp.setString(_kToken, _token);
    await sp.setString(_kUser, jsonEncode(_user));
  }

  /// Update profile locally after edit_profile API success
  Future<void> updateProfileLocal({
    required String name,
    required String email,
    required String phone,
    required String imageBase64,
  }) async {
    _user = {
      ..._user,
      "name": name,
      "email": email,
      "phone": phone,
      "profile_pick": imageBase64,
    };

    final sp = await SharedPreferences.getInstance();
    await sp.setString(_kUser, jsonEncode(_user));
  }

  /// Update wallet address locally
  Future<void> updateWalletAddress(String walletAddress) async {
    _user = {
      ..._user,
      "wallet_address": walletAddress,
    };

    final sp = await SharedPreferences.getInstance();
    await sp.setString(_kUser, jsonEncode(_user));
  }

  /// FULL LOGOUT + CLEAR EVERYTHING
  Future<void> logout() async {
    final sp = await SharedPreferences.getInstance();

    _isLoggedIn = false;
    _token = "";
    _user = {};

    await sp.remove(_kLoggedIn);
    await sp.remove(_kToken);
    await sp.remove(_kUser);

    // guideSeen intentionally not removed
  }

  /// FORCE CLEAR SESSION (extra safe)
  Future<void> forceClearAllSession() async {
    final sp = await SharedPreferences.getInstance();

    _isLoggedIn = false;
    _token = "";
    _user = {};

    await sp.clear();
  }
}