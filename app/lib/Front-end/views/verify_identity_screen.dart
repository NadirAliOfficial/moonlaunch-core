import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:moon_launch/Front-end/auth_screens/otp_screen.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';

class VerifyIdentityScreen extends StatefulWidget {
  const VerifyIdentityScreen({super.key});

  @override
  State<VerifyIdentityScreen> createState() => _VerifyIdentityScreenState();
}

class _VerifyIdentityScreenState extends State<VerifyIdentityScreen> {
  final TextEditingController _emailController = TextEditingController();
  bool _loading = false;
  String? _error;

  Future<void> _sendOtp() async {
    final email = _emailController.text.trim();
    if (email.isEmpty) {
      setState(() => _error = 'Please enter your email');
      return;
    }
    setState(() { _loading = true; _error = null; });
    try {
      final res = await http.post(
        Uri.parse('https://backend.moonlaunchapp.com/api/resend_otp'),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode({'email': email}),
      );
      final body = jsonDecode(res.body);
      if (res.statusCode == 200 && body['success'] == true) {
        final rawId = body['user_id'];
        if (rawId == null) {
          setState(() => _error = 'Server error: no user ID returned. Please try again.');
          return;
        }
        final userId = rawId is int ? rawId : int.parse(rawId.toString());
        if (!mounted) return;
        Navigator.push(context, MaterialPageRoute(
          builder: (_) => OtpScreen(userId: userId),
        ));
      } else {
        setState(() => _error = body['message'] ?? 'Failed to send OTP');
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
                  child: const Padding(
                    padding: EdgeInsets.only(right: 3),
                    child: Icon(Icons.arrow_back_ios_new, color: Colors.white, size: 24),
                  ),
                ),
              ),
              Image.asset('assets/images/moon_launch_logo.png', width: 104, height: 31),
            ],
          ),
        ),
      ),
      body: AppBackground(
        child: SafeArea(
          child: SingleChildScrollView(
            child: Column(
              children: [
                Image.asset('assets/images/verification_image.png', height: 311, width: 311),
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.07),
                  child: const Row(children: [
                    Text('Verify Your Identity',
                      style: TextStyle(fontFamily: 'BernardMTCondensed', fontWeight: FontWeight.w600, fontSize: 28, color: Colors.white),
                    ),
                  ]),
                ),
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.07),
                  child: const Row(children: [
                    Text('Enter your email to receive a reset code',
                      style: TextStyle(fontFamily: 'Benne', fontWeight: FontWeight.w400, fontSize: 14, color: Colors.white),
                    ),
                  ]),
                ),
                SizedBox(height: mqSize.height * 0.025),
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.07),
                  child: TextField(
                    controller: _emailController,
                    keyboardType: TextInputType.emailAddress,
                    style: const TextStyle(color: Colors.white),
                    decoration: InputDecoration(
                      hintText: 'Email Address',
                      hintStyle: const TextStyle(fontFamily: 'Benne', fontSize: 15, color: Colors.white54),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(mqSize.width * 0.5)),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                        borderSide: const BorderSide(color: Color(0xFFDB2519)),
                      ),
                    ),
                  ),
                ),
                if (_error != null) ...[
                  SizedBox(height: mqSize.height * 0.015),
                  Padding(
                    padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.07),
                    child: Text(_error!, style: const TextStyle(color: Colors.redAccent, fontFamily: 'Benne', fontSize: 13)),
                  ),
                ],
                SizedBox(height: mqSize.height * 0.03),
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.05),
                  child: InkWell(
                    onTap: _loading ? null : _sendOtp,
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 8),
                      child: Container(
                        height: mqSize.height * 0.06,
                        width: double.infinity,
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [Color(0xFFA21117), Color(0xFF251216)],
                            begin: Alignment.centerLeft,
                            end: Alignment.centerRight,
                          ),
                          borderRadius: BorderRadius.circular(50),
                        ),
                        child: Center(
                          child: _loading
                              ? const SizedBox(height: 22, width: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                              : const Text('Send Code',
                                  style: TextStyle(fontFamily: 'BernardMTCondensed', fontWeight: FontWeight.w600, color: Colors.white, fontSize: 16)),
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
