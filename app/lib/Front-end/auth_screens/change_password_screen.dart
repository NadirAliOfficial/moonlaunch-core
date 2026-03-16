import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:moon_launch/Front-end/auth_screens/login_screen.dart';
import '../widgets/app_background.dart';

class ChangePasswordScreen extends StatefulWidget {
  final int userId;
  final String otp;
  const ChangePasswordScreen({super.key, required this.userId, required this.otp});

  @override
  State<ChangePasswordScreen> createState() => _ChangePasswordScreenState();
}

class _ChangePasswordScreenState extends State<ChangePasswordScreen> {
  bool isPasswordVisible = false;
  bool isConfirmPasswordVisible = false;
  bool _loading = false;
  String? _error;

  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController();

  Future<void> _resetPassword() async {
    final password = _passwordController.text.trim();
    final confirm = _confirmPasswordController.text.trim();

    if (password.isEmpty || confirm.isEmpty) {
      setState(() => _error = 'Please fill in all fields');
      return;
    }
    if (password != confirm) {
      setState(() => _error = 'Passwords do not match');
      return;
    }
    if (password.length < 6) {
      setState(() => _error = 'Password must be at least 6 characters');
      return;
    }

    setState(() { _loading = true; _error = null; });
    try {
      final res = await http.post(
        Uri.parse('https://backend.moonlaunchapp.com/api/reset_password'),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode({
          'user_id': widget.userId,
          'otp': widget.otp,
          'new_password': password,
        }),
      );
      final body = jsonDecode(res.body);
      if (res.statusCode == 200 && body['success'] == true) {
        if (!mounted) return;
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (_) => const LoginScreen()),
          (_) => false,
        );
      } else {
        setState(() => _error = body['message'] ?? 'Failed to reset password');
      }
    } catch (_) {
      setState(() => _error = 'Network error. Please try again.');
    } finally {
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final Size mqSize = MediaQuery.of(context).size;

    return Scaffold(
      resizeToAvoidBottomInset: true,
      extendBodyBehindAppBar: true,

      appBar: AppBar(
        automaticallyImplyLeading: false,
        backgroundColor: Colors.transparent,
        toolbarHeight: 70,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        title: Padding(
          padding: const EdgeInsets.only(top: 30.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              InkWell(
                onTap: () => Navigator.pop(context),
                child: Container(
                  height: 38,
                  width: 38,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(50),
                    color: Color(0xFFDB2519).withOpacity(0.2),
                  ),
                  child: Padding(
                    padding: EdgeInsets.only(right: 3),
                    child: Icon(
                      Icons.arrow_back_ios_new,
                      color: Colors.white,
                      size: 24,
                    ),
                  ),
                ),
              ),

              Image.asset(
                'assets/images/moon_launch_logo.png',
                width: 109,
                height: 31,
              ),
            ],
          ),
        ),
      ),

      body: AppBackground(
        child: SafeArea(
          child: SingleChildScrollView(
            child: Column(
              children: [
                Image.asset(
                  'assets/images/crypto_security_icon.png',
                  height: 295,
                  width: 301,
                ),

                Padding(
                  padding: EdgeInsets.symmetric(
                    horizontal: mqSize.width * 0.05,
                  ),
                  child: Row(
                    children: [
                      Text(
                        'Change Password',
                        style: TextStyle(
                          fontFamily: 'BernardMTCondensed',
                          fontWeight: FontWeight.w400,
                          fontSize: mqSize.width * 0.07,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),
                Padding(
                  padding: EdgeInsets.symmetric(
                    horizontal: mqSize.width * 0.05,
                  ),
                  child: Row(
                    children: [
                      Text(
                        'Enter new password',
                        style: TextStyle(
                          fontFamily: 'Benne',
                          fontWeight: FontWeight.w400,
                          fontSize: mqSize.width * 0.04,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),
                SizedBox(height: mqSize.height * 0.015),

                Padding(
                  padding: EdgeInsets.symmetric(
                    horizontal: mqSize.width * 0.05,
                  ),
                  child: TextField(
                    controller: _passwordController,
                    obscureText: !isPasswordVisible,
                    style: const TextStyle(color: Colors.white),
                    decoration: InputDecoration(
                      contentPadding: EdgeInsets.symmetric(
                        vertical: 8.0,
                        horizontal: 12.0,
                      ),
                      hint: Text(
                        "New Password",
                        style: TextStyle(fontFamily: 'Benne', fontSize: 15, color: Colors.white54),
                      ),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                        borderSide: const BorderSide(color: Color(0xFFDB2519)),
                      ),
                      suffixIcon: IconButton(
                        icon: Icon(
                          isPasswordVisible
                              ? Icons.visibility
                              : Icons.visibility_off,
                          color: Colors.white,
                        ),
                        onPressed: () {
                          setState(() {
                            isPasswordVisible = !isPasswordVisible;
                          });
                        },
                      ),
                    ),
                  ),
                ),
                SizedBox(height: mqSize.height * 0.01),

                Padding(
                  padding: EdgeInsets.symmetric(
                    horizontal: mqSize.width * 0.05,
                  ),
                  child: TextField(
                    controller: _confirmPasswordController,
                    obscureText: !isConfirmPasswordVisible,
                    style: const TextStyle(color: Colors.white),
                    decoration: InputDecoration(
                      contentPadding: EdgeInsets.symmetric(
                        vertical: 8.0,
                        horizontal: 12.0,
                      ),
                      hint: Text(
                        "Confirm New Password",
                        style: TextStyle(fontFamily: 'Benne', fontSize: 15, color: Colors.white54),
                      ),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                        borderSide: const BorderSide(color: Color(0xFFDB2519)),
                      ),
                      suffixIcon: IconButton(
                        icon: Icon(
                          isConfirmPasswordVisible
                              ? Icons.visibility
                              : Icons.visibility_off,
                          color: Colors.white,
                        ),
                        onPressed: () {
                          setState(() {
                            isConfirmPasswordVisible =
                                !isConfirmPasswordVisible;
                          });
                        },
                      ),
                    ),
                  ),
                ),

                if (_error != null) ...[
                  SizedBox(height: mqSize.height * 0.015),
                  Padding(
                    padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.05),
                    child: Text(_error!, style: const TextStyle(color: Colors.redAccent, fontFamily: 'Benne', fontSize: 13)),
                  ),
                ],

                SizedBox(height: mqSize.height * 0.03),

                Padding(
                  padding: EdgeInsets.symmetric(
                    horizontal: mqSize.width * 0.05,
                  ),
                  child: InkWell(
                    onTap: _loading ? null : _resetPassword,
                    child: Container(
                      height: mqSize.height * 0.06,
                      width: double.infinity,
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: [Color(0xFFA21117), Color(0xFF251216)],
                          begin: Alignment.centerLeft,
                          end: Alignment.centerRight,
                        ),
                        borderRadius: BorderRadius.circular(50),
                      ),
                      child: Center(
                        child: _loading
                            ? const SizedBox(height: 22, width: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                            : const Text(
                                'Update',
                                style: TextStyle(
                                  fontFamily: 'BernardMTCondensed',
                                  fontWeight: FontWeight.w400,
                                  color: Colors.white,
                                  fontSize: 16,
                                ),
                              ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
