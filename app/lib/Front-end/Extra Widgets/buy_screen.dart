import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';

class BuyScreen extends StatelessWidget {
  const BuyScreen({super.key});

  static const LinearGradient _btnGradient = LinearGradient(
    colors: [Color(0xFFFFE600), Color(0xFFDB2519)],
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
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
            padding: EdgeInsets.symmetric(horizontal: mq.width * 0.07),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SizedBox(height: mq.height * 0.02),

                Text(
                  "Buy",
                  style: TextStyle(
                    fontFamily: "BernardMTCondensed",
                    fontSize: mq.width * 0.085,
                    color: Colors.white,
                    fontWeight: FontWeight.w700,
                  ),
                ),

                SizedBox(height: mq.height * 0.03),

                // ✅ center coin (NO container behind, just image bigger)
                Center(
                  child: Column(
                    children: [
                      Image.asset(
                        "assets/images/bit_coin.png",
                        width: mq.width * 0.28,
                        height: mq.width * 0.28,
                        fit: BoxFit.contain,
                      ),
                      const SizedBox(height: 10),
                      const Text(
                        "MemeCoin1",
                        style: TextStyle(
                          fontFamily: "BernardMTCondensed",
                          color: Colors.white,
                          fontSize: 16,
                        ),
                      ),
                    ],
                  ),
                ),

                SizedBox(height: mq.height * 0.03),

                // amount field pill
                _pill(
                  mq,
                  child: Row(
                    children: [
                      const SizedBox(width: 18),
                      const Expanded(
                        child: Text(
                          "\$250.00",
                          style: TextStyle(
                            fontFamily: "BernardMTCondensed",
                            color: Colors.white,
                            fontSize: 18,
                          ),
                        ),
                      ),
                      Text(
                        "USD",
                        style: TextStyle(
                          fontFamily: "Benne",
                          color: Colors.white.withOpacity(.75),
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(width: 18),
                    ],
                  ),
                ),
                SizedBox(height: 20,),

                // conversion row
                Center(
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Text(
                        "0.269 BNB ~ 98,209.654532",
                        style: TextStyle(
                          fontFamily: "BernardMTCondensed",
                          color: Colors.white,
                          fontSize: 16,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Image.asset("assets/images/bit_coin.png",height: 25,width: 25,),
                      const Icon(Icons.chevron_right,
                          size:25, color: Color(0xFFDB2519)),
                    ],
                  ),
                ),
                SizedBox(height: 20,),

                // Pay with card
                _pill(
                  mq,
                  child: Row(
                    children: [
                      const SizedBox(width: 16),
                      const Icon(Icons.credit_card,
                          color: Colors.white, size: 22),
                      const SizedBox(width: 10),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          SizedBox(height: 10,),
                          const Text(
                            "Pay with",
                            style: TextStyle(
                              fontFamily: "Benne",
                              color: Colors.white,
                              fontSize: 16,
                            ),
                          ),
                          Text(
                            "Buy",
                            style: TextStyle(
                              fontFamily: "Benne",
                              color: Colors.white.withOpacity(.6),
                              fontSize: 12,
                            ),
                          ),
                        ],
                      ),
                      const Spacer(),
                      const Icon(Icons.chevron_right,
                          color: Color(0xFFDB2519), size: 30),
                      const SizedBox(width: 10),
                    ],
                  ),
                ),

                const Spacer(),

                _bottomGradientButton(
                  text: "Continue",
                  onTap: () {},
                ),

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
      title: Padding(
        padding: const EdgeInsets.only(top: 30),
        child: Row(
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
                  child: Icon(Icons.arrow_back_ios_new,
                      color: Colors.white, size: 24),
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
        ),
      ),
    );
  }

  Widget _pill(Size mq, {required Widget child}) {
    return Container(
      height: mq.height * 0.065,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(40),
        border: Border.all(color: Colors.white.withOpacity(.35), width: 1),
        color: Colors.black.withOpacity(.10),
      ),
      child: child,
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
