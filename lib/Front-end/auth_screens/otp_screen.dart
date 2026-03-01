import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/auth_screens/change_password_screen.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';
import 'package:moon_launch/Back-end/Services/forgot_password_service.dart';

class OtpScreen extends StatefulWidget {
  final String email;
  final int userId;
  final String serverOtp;

  const OtpScreen({
    super.key,
    required this.email,
    required this.userId,
    required this.serverOtp,
  });

  @override
  State<OtpScreen> createState() => _OtpScreenState();
}

class _OtpScreenState extends State<OtpScreen> {
  final List<TextEditingController> otpControllers =
      List.generate(6, (index) => TextEditingController());

  bool isLoading = false;
  bool isResending = false;

  late String _serverOtp;

  @override
  void initState() {
    super.initState();
    _serverOtp = widget.serverOtp;
  }

  @override
  void dispose() {
    for (final c in otpControllers) {
      c.dispose();
    }
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
                  'assets/images/verification_image.png',
                  height: 311,
                  width: 311,
                ),
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.05),
                  child: Row(
                    children: [
                      Text(
                        'Enter Code',
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
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.05),
                  child: Row(
                    children: [
                      Text(
                        'Enter your code to verify your identity',
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
                SizedBox(height: mqSize.height * 0.03),

                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.05),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: List.generate(6, (index) {
                      return SizedBox(
                        width: 45,
                        height: 50,
                        child: TextField(
                          controller: otpControllers[index],
                          maxLength: 1,
                          textAlign: TextAlign.center,
                          keyboardType: TextInputType.number,
                          style: const TextStyle(
                            fontSize: 16,
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                          ),
                          decoration: InputDecoration(
                            counterText: '',
                            filled: true,
                            fillColor: const Color(0xFFFFE600).withOpacity(0.1),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(15),
                              borderSide: BorderSide(
                                color: const Color(0xFFFFE600).withOpacity(0.3),
                              ),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(15),
                              borderSide: BorderSide(
                                color: const Color(0xFFFFE600).withOpacity(0.7),
                              ),
                            ),
                          ),
                          onChanged: (value) {
                            if (value.isNotEmpty && index < 5) {
                              FocusScope.of(context).nextFocus();
                            } else if (value.isEmpty && index > 0) {
                              FocusScope.of(context).previousFocus();
                            }
                          },
                        ),
                      );
                    }),
                  ),
                ),

                SizedBox(height: mqSize.height * 0.02),

                // ✅ RESEND BUTTON
                InkWell(
                  onTap: isResending ? null : _resendOtp,
                  child: Padding(
                    padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.05),
                    child: Align(
                      alignment: Alignment.centerRight,
                      child: Text(
                        isResending ? "Resending..." : "Resend OTP",
                        style: TextStyle(
                          fontFamily: "Benne",
                          fontSize: mqSize.width * 0.04,
                          color: const Color(0xFFFFE600),
                          decoration: TextDecoration.underline,
                        ),
                      ),
                    ),
                  ),
                ),

                SizedBox(height: mqSize.height * 0.03),

                Padding(
                  padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.05),
                  child: InkWell(
                    onTap: isLoading ? null : _verifyOtp,
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
                        borderRadius: BorderRadius.circular(40),
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
                                'Verify Code',
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

  Future<void> _verifyOtp() async {
    final otp = otpControllers.map((c) => c.text.trim()).join();

    if (otp.length != 6) {
      _showSnackThemed("Please enter complete 6 digit OTP");
      return;
    }

    if (otp != _serverOtp) {
      _showSnackThemed("Invalid OTP");
      return;
    }

    Navigator.pushReplacement(
      context,
      MaterialPageRoute(
        builder: (_) => ChangePasswordScreen(userId: widget.userId),
      ),
    );
  }

  Future<void> _resendOtp() async {
    setState(() => isResending = true);

    try {
      final res = await ForgotPasswordService.resendOtp(widget.email);

      final data = res["data"] ?? {};
      final otp = data["otp"];

      setState(() {
        _serverOtp = otp.toString();
        isResending = false;
      });

      _showSnackThemed("OTP resent successfully");
    } catch (e) {
      setState(() => isResending = false);
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
