import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/auth_screens/otp_screen.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';
import 'package:moon_launch/Back-end/Services/forgot_password_service.dart';

class VerifyIdentityScreen extends StatefulWidget {
  const VerifyIdentityScreen({super.key});

  @override
  State<VerifyIdentityScreen> createState() => _VerifyIdentityScreenState();
}

class _VerifyIdentityScreenState extends State<VerifyIdentityScreen> {
  final TextEditingController emailCtrl = TextEditingController();
  bool isLoading = false;

  @override
  void dispose() {
    emailCtrl.dispose();
    super.dispose();
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
                    color: const Color(0xFFDB2519).withOpacity(0.2),
                  ),
                  child: const Padding(
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
                width: 104,
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
                  'assets/images/verification_image.png',
                  height: 311,
                  width: 311,
                ),
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.07),
                  child: Row(
                    children: [
                      Text(
                        'Verify Your Identity',
                        style: TextStyle(
                          fontFamily: 'BernardMTCondensed',
                          fontWeight: FontWeight.w600,
                          fontSize: mqSize.width * 0.07,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.07),
                  child: Row(
                    children: [
                      Text(
                        'Enter your mail to verify your identity',
                        style: TextStyle(
                          fontFamily: 'Benne',
                          fontWeight: FontWeight.w400,
                          fontSize: mqSize.width * 0.040,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),
                SizedBox(height: mqSize.height * 0.025),

                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.07),
                  child: TextField(
                    controller: emailCtrl,
                    decoration: InputDecoration(
                      hint: const Text(
                        "Email Address",
                        style: TextStyle(fontFamily: 'Benne', fontSize: 15),
                      ),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                        borderSide: const BorderSide(color: Color(0xFFDB2519)),
                      ),
                    ),
                  ),
                ),

                SizedBox(height: mqSize.height * 0.03),

                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.05),
                  child: InkWell(
                    onTap: isLoading ? null : _sendOtp,
                    child: Padding(
                      padding: const EdgeInsets.only(left: 8.0, right: 8),
                      child: Container(
                        height: mqSize.height * 0.06,
                        width: double.infinity,
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [
                              Color(0xFFFFE600),
                              Color(0xFFDB2519),
                            ],
                            begin: Alignment.centerLeft,
                            end: Alignment.centerRight,
                          ),
                          borderRadius: BorderRadius.circular(50),
                        ),
                        child: Center(
                          child: isLoading
                              ? const SizedBox(
                                  width: 22,
                                  height: 22,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2.5,
                                    color: Colors.white,
                                  ),
                                )
                              : const Text(
                                  'Send Code',
                                  style: TextStyle(
                                    fontFamily: 'BernardMTCondensed',
                                    fontWeight: FontWeight.w600,
                                    color: Colors.white,
                                    fontSize: 16,
                                  ),
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

  Future<void> _sendOtp() async {
   final email = emailCtrl.text.trim().toLowerCase();


    if (email.isEmpty) {
      _showSnackThemed("Please enter email");
      return;
    }

    if (!email.contains("@")) {
      _showSnackThemed("Please enter valid email");
      return;
    }

    setState(() => isLoading = true);

    try {
      final res = await ForgotPasswordService.sendOtp(email);

      setState(() => isLoading = false);

      final msg = ForgotPasswordService.extractMessage(res);
      _showSnackThemed(msg.isNotEmpty ? msg : "OTP sent successfully");

      final data = res["data"] ?? {};
      final userId = data["user_id"];
      final otp = data["otp"];

      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) => OtpScreen(
            email: email,
            userId: userId is int ? userId : int.tryParse(userId.toString()) ?? 0,
            serverOtp: otp.toString(),
          ),
        ),
      );
    } catch (e) {
      setState(() => isLoading = false);
      _showSnackThemed(e.toString().replaceAll("Exception:", "").trim());
    }
  }

  void _showSnackThemed(String msg) {
    ScaffoldMessenger.of(context).clearSnackBars();
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        behavior: SnackBarBehavior.floating,
        backgroundColor: Colors.transparent,
        elevation: 0,
        margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        content: Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
          decoration: BoxDecoration(
            color: Colors.black,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              width: 1.2,
              color: const Color(0xFFDB2519),
            ),
          ),
          child: Text(
            msg,
            style: const TextStyle(
              fontFamily: 'Benne',
              color: Colors.white,
            ),
          ),
        ),
      ),
    );
  }
}
