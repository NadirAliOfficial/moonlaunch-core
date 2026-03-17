import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  static const String _base = "https://backend.moonlaunchapp.com/api";

  // ---------------- SIGNUP (OTP) ----------------
  static Future<Map<String, dynamic>> sendOtp(String email) async {
    final uri = Uri.parse("$_base/signup");
    final body = {"email": email};

    print("➡️ POST $uri");
    print("➡️ BODY: $body");

    final res = await http.post(
      uri,
      headers: {"Content-Type": "application/json"},
      body: jsonEncode(body),
    );

    print("⬅️ STATUS: ${res.statusCode}");
    print("⬅️ BODY: ${res.body}");

    final decoded = _safeJson(res.body);

    if (res.statusCode >= 200 && res.statusCode < 300) {
      return decoded;
    }
    throw extractMessage(decoded).isNotEmpty ? extractMessage(decoded) : "Request failed";
  }

  static Future<Map<String, dynamic>> verifyOtp({
    required String name,
    required String email,
    required String password,
    required String otp,
  }) async {
    final uri = Uri.parse("$_base/verify-otp");
    final body = {
      "name": name,
      "email": email,
      "password": password,
      "otp": otp,
    };

    print("➡️ POST $uri");
    print("➡️ BODY: $body");

    final res = await http.post(
      uri,
      headers: {"Content-Type": "application/json"},
      body: jsonEncode(body),
    );

    print("⬅️ STATUS: ${res.statusCode}");
    print("⬅️ BODY: ${res.body}");

    final decoded = _safeJson(res.body);

    if (res.statusCode >= 200 && res.statusCode < 300) {
      return decoded;
    }
    throw extractMessage(decoded).isNotEmpty ? extractMessage(decoded) : "Verification failed";
  }

  // ---------------- LOGIN (OTP) ----------------
  static Future<Map<String, dynamic>> requestLoginOtp(String email) async {
    final uri = Uri.parse("$_base/request_login");
    final body = {"email": email};

    print("➡️ POST $uri");
    print("➡️ BODY: $body");

    final res = await http.post(
      uri,
      headers: {"Content-Type": "application/json"},
      body: jsonEncode(body),
    );

    print("⬅️ STATUS: ${res.statusCode}");
    print("⬅️ BODY: ${res.body}");

    final decoded = _safeJson(res.body);

    if (res.statusCode >= 200 && res.statusCode < 300) {
      return decoded;
    }
    throw extractMessage(decoded).isNotEmpty ? extractMessage(decoded) : "Login request failed";
  }

  static Future<Map<String, dynamic>> verifyLoginOtp({
    required String email,
    required String password,
    required String otp,
  }) async {
    final uri = Uri.parse("$_base/verify-login-otp");
    final body = {
      "email": email,
      "password": password,
      "otp": otp,
    };

    print("➡️ POST $uri");
    print("➡️ BODY: $body");

    final res = await http.post(
      uri,
      headers: {"Content-Type": "application/json"},
      body: jsonEncode(body),
    );

    print("⬅️ STATUS: ${res.statusCode}");
    print("⬅️ BODY: ${res.body}");

    final decoded = _safeJson(res.body);

    if (res.statusCode >= 200 && res.statusCode < 300) {
      return decoded;
    }
    throw extractMessage(decoded).isNotEmpty ? extractMessage(decoded) : "OTP verification failed";
  }

  // ---------------- UPDATE PROFILE ----------------
  static Future<Map<String, dynamic>> updateProfile({
    required int userId,
    String? name,
    String? profileImageBase64,
  }) async {
    final uri = Uri.parse("$_base/update-profile");
    final body = <String, dynamic>{"user_id": userId};
    if (name != null) body["name"] = name;
    if (profileImageBase64 != null) body["profile_pick"] = profileImageBase64;

    print("➡️ POST $uri");

    final res = await http.post(
      uri,
      headers: {"Content-Type": "application/json"},
      body: jsonEncode(body),
    );

    print("⬅️ STATUS: ${res.statusCode}");
    print("⬅️ BODY: ${res.body}");

    final decoded = _safeJson(res.body);

    if (res.statusCode >= 200 && res.statusCode < 300) {
      return decoded;
    }
    throw extractMessage(decoded).isNotEmpty ? extractMessage(decoded) : "Update failed";
  }

  // ---------------- HELPERS ----------------
  static Map<String, dynamic> _safeJson(String body) {
    try {
      final decoded = jsonDecode(body);
      if (decoded is Map<String, dynamic>) return decoded;
      return {"message": decoded.toString()};
    } catch (_) {
      return {"message": body.toString()};
    }
  }

  static String extractMessage(dynamic res) {
    try {
      if (res is String) return res;

      if (res is Map) {
        if (res["message"] != null) {
          final msg = res["message"].toString();
          final debug = res["debug"]?.toString() ?? '';
          return debug.isNotEmpty ? '$msg — $debug' : msg;
        }
        if (res["error"] != null) return res["error"].toString();
        if (res["msg"] != null) return res["msg"].toString();
        if (res["errors"] != null) return res["errors"].toString();
      }

      return res.toString();
    } catch (_) {
      return "";
    }
  }
}
