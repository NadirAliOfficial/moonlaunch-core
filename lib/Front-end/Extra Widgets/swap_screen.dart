import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';

class SwapScreen extends StatelessWidget {
  const SwapScreen({super.key});

  static const LinearGradient _btnGradient = LinearGradient(
    colors: [Color(0xFFFFE600), Color(0xFFDB2519)],
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
  );

  static const LinearGradient _swapCircleGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [Color(0xFFFFE600), Color(0xFFDB2519)],
  );

  @override
  Widget build(BuildContext context) {
    final mq = MediaQuery.of(context).size;

    return Scaffold(
      extendBodyBehindAppBar: true,
      backgroundColor: Colors.black,
      appBar: _topBar(context, mq),
      body: AppBackground(
        child: SafeArea(
          child: Padding(
            padding: EdgeInsets.symmetric(horizontal: mq.width * 0.06),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SizedBox(height: mq.height * 0.02),
                Text(
                  "Swap",
                  style: TextStyle(
                    fontFamily: "BernardMTCondensed",
                    fontSize: mq.width * 0.085,
                    color: Colors.white,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                SizedBox(height: mq.height * 0.03),

                // ✅ PERFECT: swap icon EXACTLY between both cards (center)
                Stack(
                  alignment: Alignment.center,
                  children: [
                    Column(
                      children: [
                        _swapCard(
                          mq: mq,
                          tag: "From",
                          leftImage: "assets/images/bit_coin.png",
                          title: "MemeCoin1",
                          subtitle: "BNB Smart Chain",
                          rightValue: "\0",
                        ),
                        SizedBox(height: mq.height * 0.020),
                        _swapCard(
                          mq: mq,
                          tag: "To",
                          leftImage: "assets/images/bnb.png",
                          title: "BNB",
                          subtitle: "BNB Smart Chain",
                          rightValue: "\0",
                        ),
                      ],
                    ),

                    // ✅ center circle always in middle (no manual top)
                    Transform.translate(
                      offset: const Offset(0, 0),
                      child: Container(
                        width: 58,
                        height: 58,
                        decoration: const BoxDecoration(
                          shape: BoxShape.circle,
                          gradient: _swapCircleGradient,
                        ),
                        child: const Icon(
                          Icons.swap_vert,
                          color: Colors.white,
                          size: 30,
                        ),
                      ),
                    ),
                  ],
                ),

                const Spacer(),
                _bottomGradientButton(text: "Continue", onTap: () {}),
                SizedBox(height: mq.height * 0.05),
              ],
            ),
          ),
        ),
      ),
    );
  }

  PreferredSizeWidget _topBar(BuildContext context, Size mq) {
    return AppBar(
      automaticallyImplyLeading: false,
      backgroundColor: Colors.transparent,
      surfaceTintColor: Colors.transparent,
      elevation: 0,
      toolbarHeight: 70,
      titleSpacing: mq.width * 0.05,
      title: const Padding(
        padding: EdgeInsets.only(top: 30),
        child: _TopRow(),
      ),
    );
  }

  Widget _swapCard({
    required Size mq,
    required String tag,
    required String leftImage,
    required String title,
    required String subtitle,
    required String rightValue,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: Colors.white.withOpacity(.40), width: 1),
        color: Colors.black.withOpacity(.10),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // ✅ From/To ABOVE the coin image (exact like SS)
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                tag,
                style: TextStyle(
                  fontFamily: "Benne",
                  color: Colors.white.withOpacity(.55),
                  fontSize: 12,
                ),
              ),
              const SizedBox(height: 8),

              // ✅ coin row
              Row(
                children: [
                  Image.asset(
                    leftImage,
                    width: 36,
                    height: 36,
                    fit: BoxFit.contain,
                  ),
                  const SizedBox(width: 12),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        title,
                        style: const TextStyle(
                          fontFamily: "BernardMTCondensed",
                          color: Colors.white,
                          fontSize: 16,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        subtitle,
                        style: TextStyle(
                          fontFamily: "Benne",
                          color: Colors.white.withOpacity(.65),
                          fontSize: 12,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ],
          ),

          const Spacer(),

          Padding(
            padding: const EdgeInsets.only(top: 18),
            child: Text(
              rightValue,
              style: const TextStyle(
                fontFamily: "BernardMTCondensed",
                color: Colors.white,
                fontSize: 16,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _bottomGradientButton({
    required String text,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(40),
      child: Container(
        height: 56,
        width: double.infinity,
        decoration: BoxDecoration(
          gradient: _btnGradient,
          borderRadius: BorderRadius.circular(40),
        ),
        child: Center(
          child: Text(
            text,
            style: const TextStyle(
              fontFamily: "BernardMTCondensed",
              color: Colors.white,
              fontSize: 18,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ),
    );
  }
}

class _TopRow extends StatelessWidget {
  const _TopRow();

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        InkWell(
          onTap: () => Navigator.pop(context),
          borderRadius: BorderRadius.circular(999),
          child: Container(
            height: 38,
            width: 38,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(999),
              color: const Color(0xFFDB2519).withOpacity(0.20),
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
        const Spacer(),
        Image.asset(
          'assets/images/moon_launch_logo.png',
          width: 104,
          height: 31,
          fit: BoxFit.contain,
        ),
      ],
    );
  }
}
