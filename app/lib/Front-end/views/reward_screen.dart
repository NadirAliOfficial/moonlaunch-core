import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/widgets/profile_background.dart';

class RewardScreen extends StatelessWidget {
  const RewardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final Size mq = MediaQuery.of(context).size;

    return Scaffold(
      extendBodyBehindAppBar: true,
      extendBody: true,
      backgroundColor: Colors.black,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        elevation: 0,
        backgroundColor: Colors.transparent,
        surfaceTintColor: Colors.transparent,

        // ✅ Left side (Text + icon) perfectly centered vertically
        titleSpacing: mq.width * 0.045,
        title: Padding(
          padding: EdgeInsets.only(top: mq.height * 0.02), // ✅ SAME TOP as logo
          child: Row(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Text(
                'Life Time Rewards',
                style: TextStyle(
                  fontFamily: 'Benne',
                  fontSize: mq.width * 0.044,
                  color: const Color(0xFFC9C9C9),
                ),
              ),
            ],
          ),
        ),

        // ✅ Right side (Logo) same top padding
        actions: [
          Padding(
            padding: EdgeInsets.only(
              right: mq.width * 0.045,
              top: mq.height * 0.02, // ✅ SAME TOP as left
            ),
            child: Align(
              alignment: Alignment.center,
              child: Image.asset(
                'assets/images/moon_launch_logo.png',
                width: 104,
                height: 31,
                fit: BoxFit.contain,
              ),
            ),
          ),
        ],
      ),

      body: ProfileBackground(
        child: SafeArea(
          child: Center(
            child: Padding(
              padding: EdgeInsets.symmetric(horizontal: mq.width * 0.08),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Image.asset(
                    'assets/images/reward_coin.png',
                    height: 328,
                    width: 328,
                    fit: BoxFit.contain,
                    cacheWidth: 656,
                    cacheHeight: 656,
                  ),
                  SizedBox(height: mq.height * 0.03),
                  Text(
                    'Earn BNB coins whenever your family, friends and their friends buy memes.',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontFamily: 'Benne',
                      fontWeight: FontWeight.w400,
                      fontSize: mq.width * 0.070,
                      height: 1.15,
                      color: const Color(0xFFC9C9C9),
                    ),
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
