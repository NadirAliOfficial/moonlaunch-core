import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/auth_screens/register_screen.dart';
import 'package:moon_launch/Front-end/views/verify_identity_screen.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';
import 'package:moon_launch/Front-end/widgets/widget_tree.dart';
import 'package:moon_launch/Back-end/Services/auth_service.dart';
import 'package:moon_launch/Front-end/auth_screens/two_factor.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  bool rememberMe = false;
  bool isPasswordVisible = false;
  bool isLoading = false;

  final TextEditingController emailCtrl = TextEditingController();
  final TextEditingController passCtrl = TextEditingController();

  @override
  void dispose() {
    emailCtrl.dispose();
    passCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final Size mqSize = MediaQuery.of(context).size;

    return Scaffold(
      resizeToAvoidBottomInset: true,
      body: AppBackground(
        child: SafeArea(
          child: LayoutBuilder(
            builder: (context, constraints) {
              return SingleChildScrollView(
                keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
                child: ConstrainedBox(
                  constraints: BoxConstraints(minHeight: constraints.maxHeight),
                  child: IntrinsicHeight(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Image.asset(
                          'assets/images/moon_launch_logo.png',
                          width: mqSize.width * 0.7,
                        ),
                        SizedBox(height: mqSize.height * 0.12),

                        Padding(
                          padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.08),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              Text(
                                'Sign In',
                                textAlign: TextAlign.left,
                                style: TextStyle(
                                  fontFamily: 'BernardMTCondensed',
                                  fontWeight: FontWeight.w400,
                                  fontSize: mqSize.width * 0.08,
                                  color: Colors.white,
                                ),
                              ),
                            ],
                          ),
                        ),
                        SizedBox(height: mqSize.height * 0.01),

                        Padding(
                          padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.08),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              Text(
                                'Welcome Back! Enter Your Account Details',
                                textAlign: TextAlign.left,
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
                        SizedBox(height: mqSize.height * 0.02),

                        Padding(
                          padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.07),
                          child: TextField(
                            controller: emailCtrl,
                            decoration: InputDecoration(
                              hint: Text(
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
                        SizedBox(height: mqSize.height * 0.01),

                        Padding(
                          padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.07),
                          child: TextField(
                            controller: passCtrl,
                            obscureText: !isPasswordVisible,
                            decoration: InputDecoration(
                              hint: Text(
                                "Password",
                                style: TextStyle(fontFamily: 'Benne', fontSize: 15),
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
                                  isPasswordVisible ? Icons.visibility : Icons.visibility_off,
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
                        SizedBox(height: mqSize.height * 0.03),

                        Padding(
                          padding: EdgeInsets.only(left: mqSize.width * 0.07, right: mqSize.width * 0.08),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Row(
                                children: [
                                  GestureDetector(
                                    onTap: () {
                                      setState(() {
                                        rememberMe = !rememberMe;
                                      });
                                    },
                                    child: Container(
                                      width: 18,
                                      height: 18,
                                      decoration: BoxDecoration(
                                        shape: BoxShape.circle,
                                        gradient: rememberMe
                                            ? const LinearGradient(
                                                colors: [
                                                  Color(0xFFFFE600),
                                                  Color(0xFFDB2519),
                                                ],
                                              )
                                            : null,
                                        border: Border.all(
                                          color: Colors.red,
                                          width: 1.5,
                                        ),
                                      ),
                                      child: rememberMe
                                          ? const Icon(
                                              Icons.check,
                                              size: 12,
                                              color: Colors.white,
                                            )
                                          : null,
                                    ),
                                  ),
                                  SizedBox(width: mqSize.width * 0.02),
                                  Padding(
                                    padding: EdgeInsets.only(top: mqSize.height * 0.006),
                                    child: Text(
                                      'Remember me',
                                      style: TextStyle(
                                        fontFamily: 'Benne',
                                        fontSize: mqSize.width * 0.04,
                                        color: Colors.white,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              GestureDetector(
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(builder: (_) => VerifyIdentityScreen()),
                                  );
                                },
                                child: ShaderMask(
                                  shaderCallback: (bounds) {
                                    return const LinearGradient(
                                      colors: [Color(0xFFFFE600), Color(0xFFDB2519)],
                                    ).createShader(bounds);
                                  },
                                  child: Text(
                                    'Forgot Password?',
                                    style: TextStyle(
                                      fontFamily: 'Benne',
                                      fontSize: mqSize.width * 0.04,
                                      color: Colors.white,
                                      decoration: TextDecoration.underline,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                        SizedBox(height: mqSize.height * 0.04),

                        Padding(
                          padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.05),
                          child: InkWell(
                            onTap: isLoading ? null : _onLogin,
                            child: Container(
                              height: mqSize.height * 0.06,
                              width: double.infinity,
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  colors: [Color(0xFFFFE600), Color(0xFFDB2519)],
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
                                    : Text(
                                        'Login',
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
                        SizedBox(height: mqSize.height * 0.06),

                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              'Don’t have an account? ',
                              style: TextStyle(
                                fontFamily: 'BernardMTCondensed',
                                fontWeight: FontWeight.w400,
                                color: Colors.white,
                                fontSize: 16,
                              ),
                            ),
                            InkWell(
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(builder: (_) => RegisterScreen()),
                                );
                              },
                              child: ShaderMask(
                                shaderCallback: (bounds) {
                                  return const LinearGradient(
                                    colors: [Color(0xFFFFE600), Color(0xFFDB2519)],
                                  ).createShader(bounds);
                                },
                                child: Text(
                                  'Sign Up',
                                  style: TextStyle(
                                    fontFamily: 'Benne',
                                    fontWeight: FontWeight.w400,
                                    fontSize: 14,
                                    color: Colors.white,
                                    decoration: TextDecoration.underline,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        )
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        ),
      ),
    );
  }

  Future<void> _onLogin() async {
    final email = emailCtrl.text.trim();
    final password = passCtrl.text;

    if (email.isEmpty || password.isEmpty) {
      _showSnackThemed("Please fill all the fields");
      return;
    }

    if (!email.contains("@")) {
      _showSnackThemed("Please enter a valid email");
      return;
    }

    setState(() => isLoading = true);

    try {
      print("📤 request_login API hit: $email");
      final res = await AuthService.requestLoginOtp(email);
      print("📥 request_login response: $res");

      setState(() => isLoading = false);

      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) => TwoFactor.login(
            email: email,
            password: password,
            onSuccessNavigateTo: WidgetTree(),
          ),
        ),
      );
    } catch (e) {
      setState(() => isLoading = false);
      print("❌ request_login error: $e");
      final msg = AuthService.extractMessage(e);
      _showSnackThemed(msg.isNotEmpty ? msg : "Login request failed");
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
