import 'dart:convert';
import 'package:http/http.dart' as http;

class EditProfileService {
  static const String _base = "https://backend.moonlaunchapp.com/api";

  static Future<Map<String, dynamic>> editProfile({
    required int userId,
    required String name,
    required String imageBase64,
  }) async {
    final uri = Uri.parse("$_base/edit_profile");

    final body = {
      "user_id": userId,
      "name": name,
      "image": imageBase64,
    };

    print("➡️ POST $uri");
    print("➡️ BODY keys: user_id, name, image(base64)");

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

    throw _extractMessage(decoded).isNotEmpty
        ? _extractMessage(decoded)
        : "Edit profile failed";
  }

  static Map<String, dynamic> _safeJson(String body) {
    try {
      final decoded = jsonDecode(body);
      if (decoded is Map<String, dynamic>) return decoded;
      return {"message": decoded.toString()};
    } catch (_) {
      return {"message": body.toString()};
    }
  }

  static String _extractMessage(dynamic res) {
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
