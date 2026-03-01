import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';

class ExportKey extends StatefulWidget {
  const ExportKey({super.key});

  @override
  State<ExportKey> createState() => _ExportKeyState();
}

class _ExportKeyState extends State<ExportKey> {
  static const Color _yellow = Color(0xFFFFE600);
  static const Color _red = Color(0xFFDB2519);

  Widget _gradientIcon(IconData icon, {double size = 24}) {
    return ShaderMask(
      shaderCallback: (Rect bounds) {
        return const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [_yellow, _red],
        ).createShader(bounds);
      },
      child: Icon(icon, size: size, color: Colors.white),
    );
  }

  @override
  Widget build(BuildContext context) {
    final Size mqSize = MediaQuery.of(context).size;

    return Scaffold(
      resizeToAvoidBottomInset: true,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        toolbarHeight: 65,
        automaticallyImplyLeading: false,
        backgroundColor: Colors.transparent,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        title: Padding(
          padding: const EdgeInsets.only(top: 25.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              InkWell(
                onTap: () {
                  Navigator.pop(context);
                },
                child: Container(
                  height: 38,
                  width: 38,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(50),
                    color: const Color(0xFFDB2519).withOpacity(0.2),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.only(right: 3),
                    child: Center(
                      child: Image.asset(
                        "assets/images/back1.png",
                        height: 24,
                        width: 24,
                        color: Colors.white,
                      ),
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
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 17),
            child: SingleChildScrollView(
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    children: [
                      Text(
                        'Export Keys',
                        style: TextStyle(
                          fontFamily: 'BernardMTCondensed',
                          fontWeight: FontWeight.w400,
                          fontSize: mqSize.width * 0.07,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: mqSize.height * 0.05),
                  Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                      "Your Wallet",
                      style: TextStyle(
                        fontFamily: "BernardMTCondensed",
                        fontSize: mqSize.width * 0.054,
                        fontWeight: FontWeight.w900,
                        color: Colors.white,
                      ),
                    ),
                  ),
                  const SizedBox(height: 7),
                  const Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                      "Your assets are held in a self-custodial cryptocurrency wallet that you own and control. Only you have access to your private keys and secret recovery phrase. MoonLaunch does not store or control your keys.",
                      style: TextStyle(
                        fontFamily: "Benne",
                        fontSize: 12,
                        fontWeight: FontWeight.w900,
                        color: Colors.white,
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Row(
                    children: [
                      const Image(
                        image: AssetImage("assets/images/s1.png"),
                        height: 45,
                        width: 47.28,
                      ),
                      const SizedBox(width: 7),
                      const Text(
                        "Coin",
                        style: TextStyle(
                          fontWeight: FontWeight.w800,
                          fontSize: 14,
                          fontFamily: "Benne",
                          color: Colors.white,
                        ),
                      ),
                      const Spacer(),
                      const Text(
                        "C39S..Ced8",
                        style: TextStyle(
                          fontFamily: "Benne",
                          fontSize: 13,
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      const SizedBox(width: 5),
                      ShaderMask(
                        shaderCallback: (Rect bounds) {
                          return const LinearGradient(
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                            colors: [_red, _yellow],
                          ).createShader(bounds);
                        },
                        child: const Icon(Icons.copy, size: 21, color: Colors.white),
                      ),
                    ],
                  ),
                  const Divider(
                    height: 30,
                    thickness: 1,
                    color: Colors.grey,
                  ),
                  const Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                      "Advanced",
                      style: TextStyle(
                        fontWeight: FontWeight.w900,
                        fontSize: 20,
                        fontFamily: "BernardMTCondensed",
                        color: Colors.white,
                      ),
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                      "Export your recovery phrase to access your wallet outside of MoonLaunch.",
                      style: TextStyle(
                        fontWeight: FontWeight.w900,
                        fontSize: 14,
                        fontFamily: "Benne",
                        color: Colors.white,
                      ),
                    ),
                  ),
                  const SizedBox(height: 15),

                  /// ✅ Secret Phrase row (icons gradient)
                  Row(
                    children: [
                      _gradientIcon(Icons.lock, size: 31), // ✅ gradient lock
                      const SizedBox(width: 5),
                      const Padding(
                        padding: EdgeInsets.only(top: 8.0),
                        child: Text(
                          "Secret Phrase",
                          style: TextStyle(
                            fontFamily: "Benne",
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: Colors.white,
                          ),
                        ),
                      ),
                      const Spacer(),
                      _gradientIcon(Icons.chevron_right_sharp, size: 30), // ✅ gradient arrow
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
