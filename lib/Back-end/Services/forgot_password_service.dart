import 'dart:convert';
import 'package:http/http.dart' as http;

class ForgotPasswordService {
  static const String _base = "https://backend.moonlaunchapp.com/api";

  // SEND OTP
  static Future<Map<String, dynamic>> sendOtp(String email) async {
    final uri = Uri.parse("$_base/send_otp");
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

    throw extractMessage(decoded).isNotEmpty
        ? extractMessage(decoded)
        : "Send OTP failed";
  }

  // RESEND OTP
  static Future<Map<String, dynamic>> resendOtp(String email) async {
    final uri = Uri.parse("$_base/resend_otp");
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

    throw extractMessage(decoded).isNotEmpty
        ? extractMessage(decoded)
        : "Resend OTP failed";
  }

  // RESET PASSWORD
  static Future<Map<String, dynamic>> resetPassword({
    required int userId,
    required String newPassword,
  }) async {
    final uri = Uri.parse("$_base/reset_password");

    final body = {
      "user_id": userId,
      "new_password": newPassword,
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

    throw extractMessage(decoded).isNotEmpty
        ? extractMessage(decoded)
        : "Reset password failed";
  }

  // HELPERS
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
        if (res["message"] != null) return res["message"].toString();
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
