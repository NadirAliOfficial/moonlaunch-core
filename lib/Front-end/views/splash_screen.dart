import 'dart:async';
import 'package:flutter/material.dart';
import 'package:moon_launch/Back-end/Sessions/session_controller.dart';
import 'package:moon_launch/Front-end/tutorial_screen/guid_screen.dart';
import 'package:moon_launch/Front-end/auth_screens/login_screen.dart';
import 'package:moon_launch/Front-end/widgets/widget_tree.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _scaleAnimation;

  @override
  void initState() {
    super.initState();

    _controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 3),
    );

    _scaleAnimation = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _controller,
        curve: Curves.easeOutBack,
      ),
    );

    _controller.forward();

    Timer(const Duration(seconds: 5), () async {
      if (!mounted) return;

      // Make sure session is loaded
      await SessionController.instance.init();

      if (SessionController.instance.isLoggedIn) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => WidgetTree()),
        );
        return;
      }

      // Not logged in
      if (SessionController.instance.guideSeen) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => const LoginScreen()),
        );
      } else {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => const GuidScreen()),
        );
      }
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final mqSize = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.black,
      body: Stack(
        fit: StackFit.expand,
        children: [
          Positioned.fill(
            child: Image.asset(
              'assets/images/bg_splash_screen.png',
              fit: BoxFit.contain,
              alignment: Alignment.center,
            ),
          ),
          Center(
            child: ScaleTransition(
              scale: _scaleAnimation,
              child: SizedBox(
                width: mqSize.width * 0.8,
                height: mqSize.width * 0.8,
                child: Image.asset(
                  'assets/images/moon_launch_logo.png',
                  fit: BoxFit.contain,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
