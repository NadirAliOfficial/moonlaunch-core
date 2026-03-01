import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/views/activity_screen.dart';
import 'package:moon_launch/Front-end/widgets/profile_background.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final LinearGradient _mainGradient = const LinearGradient(
    colors: [Color(0xFFFFE600), Color(0xFFDB2519)],
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
  );

  /// ✅ Only coin image size changed here (no layout impact)
  Widget _coinImage({
    required double size,
  }) {
    return SizedBox(
      width: size,
      height: size,
      child: FittedBox(
        fit: BoxFit.contain,
        child: Image.asset('assets/images/bit_coin.png'),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final Size mq = MediaQuery.of(context).size;

    return Scaffold(
      extendBodyBehindAppBar: true,
      extendBody: true,
      resizeToAvoidBottomInset: true,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        elevation: 0,
        backgroundColor: Colors.transparent,
        surfaceTintColor: Colors.transparent,
        actions: [
          Padding(
            padding: EdgeInsets.only(
              right: mq.width * 0.045,
              top: mq.height * 0.02,
            ),
            child: Image.asset(
              'assets/images/moon_launch_logo.png',
              width: 104,
              height: 31,
              fit: BoxFit.contain,
            ),
          ),
        ],
      ),
      body: ProfileBackground(
        child: SafeArea(
          child: LayoutBuilder(
            builder: (context, constraints) {
              return SingleChildScrollView(
                padding: EdgeInsets.only(
                  bottom: MediaQuery.of(context).viewInsets.bottom,
                ),
                child: ConstrainedBox(
                  constraints: BoxConstraints(minHeight: constraints.maxHeight),
                  child: IntrinsicHeight(
                    child: Padding(
                      padding: EdgeInsets.only(top: mq.height * 0.01),
                      child: Column(
                        children: [
                          // Search
                          Padding(
                            padding:
                                EdgeInsets.symmetric(horizontal: mq.width * 0.06),
                            child: Container(
                              height: mq.height * 0.055,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(40),
                                border: Border.all(
                                  color: Colors.white.withOpacity(0.35),
                                  width: 1,
                                ),
                                color: Colors.transparent,
                              ),
                              child: Row(
                                children: [
                                  SizedBox(width: mq.width * 0.04),
                                  Icon(Icons.search,
                                      color: Colors.white.withOpacity(0.85),
                                      size: 20),
                                  SizedBox(width: mq.width * 0.03),
                                  Expanded(
                                    child: TextField(
                                      style: const TextStyle(color: Colors.white),
                                      textAlignVertical: TextAlignVertical.center,
                                      decoration: InputDecoration(
                                        hintText: "Search...",
                                        hintStyle: TextStyle(
                                          color: Colors.white.withOpacity(0.70),
                                          fontFamily: 'Benne',
                                          fontSize: 14,
                                        ),
                                        border: InputBorder.none,
                                        isCollapsed: true,
                                        contentPadding:
                                            const EdgeInsets.symmetric(vertical: 0),
                                      ),
                                    ),
                                  ),
                                  SizedBox(width: mq.width * 0.04),
                                ],
                              ),
                            ),
                          ),

                          SizedBox(height: mq.height * 0.03),

                          // Total wallet value
                          Text(
                            'Total Wallet Value',
                            style: TextStyle(
                              fontFamily: 'Benne',
                              fontWeight: FontWeight.w400,
                              fontSize: mq.width * 0.040,
                              color: const Color(0xFFC9C9C9),
                            ),
                          ),
                          SizedBox(height: mq.height * 0.010),

                          RichText(
                            text: TextSpan(
                              style: const TextStyle(
                                fontFamily: 'BernardMTCondensed',
                                fontWeight: FontWeight.w400,
                                color: Colors.white,
                              ),
                              children: [
                                TextSpan(
                                  text: '\$7,765,431',
                                  style: TextStyle(fontSize: mq.width * 0.105),
                                ),
                                WidgetSpan(
                                  alignment: PlaceholderAlignment.baseline,
                                  baseline: TextBaseline.alphabetic,
                                  child: Padding(
                                    padding: const EdgeInsets.only(left: 3),
                                    child: Text(
                                      'usd',
                                      style: TextStyle(
                                        fontFamily: 'BernardMTCondensed',
                                        fontWeight: FontWeight.w400,
                                        fontSize: mq.width * 0.045,
                                        color: Colors.white,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),

                          SizedBox(height: mq.height * 0.003),

                          RichText(
                            text: TextSpan(
                              style: const TextStyle(
                                fontFamily: 'BernardMTCondensed',
                                fontWeight: FontWeight.w400,
                                color: Colors.white,
                              ),
                              children: [
                                TextSpan(
                                  text: '27.654',
                                  style: TextStyle(fontSize: mq.width * 0.070),
                                ),
                                WidgetSpan(
                                  alignment: PlaceholderAlignment.baseline,
                                  baseline: TextBaseline.alphabetic,
                                  child: Padding(
                                    padding: const EdgeInsets.only(left: 2),
                                    child: Text(
                                      'BNB',
                                      style: TextStyle(
                                        fontFamily: 'BernardMTCondensed',
                                        fontWeight: FontWeight.w400,
                                        fontSize: mq.width * 0.040,
                                        color: Colors.white,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),

                          SizedBox(height: mq.height * 0.01),
                          RichText(
                            text: TextSpan(
                              style: const TextStyle(
                                fontFamily: 'BernardMTCondensed',
                                fontWeight: FontWeight.w400,
                                color: Colors.white70,
                              ),
                              children: [
                                TextSpan(
                                  text: 'Available : 19.999',
                                  style: TextStyle(fontSize: mq.width * 0.050),
                                ),
                                WidgetSpan(
                                  alignment: PlaceholderAlignment.baseline,
                                  baseline: TextBaseline.alphabetic,
                                  child: Padding(
                                    padding: const EdgeInsets.only(left: 2),
                                    child: Text(
                                      'BNB',
                                      style: TextStyle(
                                        fontFamily: 'BernardMTCondensed',
                                        fontWeight: FontWeight.w400,
                                        fontSize: mq.width * 0.035,
                                        color: Colors.white70,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 8),

                          _gradientPillButton(
                            mq: mq,
                            title: "ADD BNB",
                            onTap: () {},
                          ),

                          SizedBox(height: mq.height * 0.035),

                          // Highlight / Rewards titles
                          Padding(
                            padding:
                                EdgeInsets.symmetric(horizontal: mq.width * 0.08),
                            child: Row(
                              children: [
                                Text(
                                  'Highlights',
                                  style: TextStyle(
                                    fontFamily: 'Benne',
                                    fontWeight: FontWeight.w400,
                                    fontSize: mq.width * 0.04,
                                    color: const Color(0xFFC9C9C9),
                                  ),
                                ),
                                const SizedBox(width: 110),
                                Text(
                                  'Rewards',
                                  style: TextStyle(
                                    fontFamily: 'Benne',
                                    fontWeight: FontWeight.w400,
                                    fontSize: mq.width * 0.04,
                                    color: const Color(0xFFC9C9C9),
                                  ),
                                ),
                              ],
                            ),
                          ),

                          SizedBox(height: mq.height * 0.007),

                          // Highlight cards row
                          Padding(
                            padding:
                                EdgeInsets.symmetric(horizontal: mq.width * 0.06),
                            child: Row(
                              children: [
                                Expanded(
                                  child: InkWell(
                                    onTap: () {
                                      Navigator.push(
                                        context,
                                        MaterialPageRoute(
                                            builder: (_) => const ActivityScreen()),
                                      );
                                    },
                                    child: _highlightPill(
                                      mq: mq,
                                      gradient: _mainGradient,
                                      leading: _coinImage(size: 58), // ✅ bigger
                                      title: "MemeCoin1",
                                      subtitle: "Value",
                                    ),
                                  ),
                                ),
                                SizedBox(width: mq.width * 0.03),
                                Expanded(
                                  child: _highlightPill(
                                    mq: mq,
                                    gradient: _mainGradient,
                                    leading: Stack(
                                      alignment: Alignment.center,
                                      children: [
                                        Image.asset(
                                          "assets/images/plate.png",
                                          width: 42,
                                          height: 39,
                                          fit: BoxFit.contain,
                                        ),
                                        Image.asset(
                                          "assets/images/bx_dollar.png",
                                          width: 19,
                                          height: 19,
                                          fit: BoxFit.contain,
                                        ),
                                      ],
                                    ),
                                    title: "Referral Bonus",
                                    subtitle: "BNB and Meme Coins",
                                    subtitleSize: mq.width * 0.020,
                                  ),
                                ),
                              ],
                            ),
                          ),

                          SizedBox(height: mq.height * 0.025),

                          // Top coins header
                          Padding(
                            padding:
                                EdgeInsets.symmetric(horizontal: mq.width * 0.07),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(
                                  'Top Coins',
                                  style: TextStyle(
                                    fontFamily: 'Benne',
                                    fontWeight: FontWeight.w400,
                                    fontSize: mq.width * 0.05,
                                    color: Colors.white,
                                  ),
                                ),
                                InkWell(
                                  onTap: () {},
                                  child: ShaderMask(
                                    shaderCallback: (bounds) =>
                                        _mainGradient.createShader(bounds),
                                    child: Text(
                                      'See All',
                                      style: TextStyle(
                                        fontFamily: 'Benne',
                                        fontWeight: FontWeight.w400,
                                        fontSize: mq.width * 0.05,
                                        color: Colors.white,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),

                          SizedBox(height: mq.height * 0.012),

                          // List of coins
                          SizedBox(
                            height: mq.height * 0.36,
                            child: ListView.builder(
                              padding: EdgeInsets.only(
                                left: mq.width * 0.04,
                                right: mq.width * 0.04,
                                bottom: mq.height * 0.02,
                              ),
                              itemCount: 10,
                              itemBuilder: (context, index) {
                                return Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 10, vertical: 14),
                                  decoration: const BoxDecoration(
                                    border: Border(
                                      top: BorderSide(
                                          width: 1.0, color: Color(0xFFCDCDCD)),
                                    ),
                                  ),
                                  child: Row(
                                    children: [
                                      _coinImage(size: 46), // ✅ bigger
                                      SizedBox(width: mq.width * 0.02),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment:
                                              CrossAxisAlignment.start,
                                          children: const [
                                            Text(
                                              'MemeCoin1',
                                              style: TextStyle(
                                                fontFamily: 'BernardMTCondensed',
                                                fontWeight: FontWeight.w400,
                                                fontSize: 16,
                                                color: Colors.white,
                                              ),
                                            ),
                                            SizedBox(height: 2),
                                            Text(
                                              'Value',
                                              style: TextStyle(
                                                fontFamily: 'Benne',
                                                fontWeight: FontWeight.w400,
                                                fontSize: 12,
                                                color: Color(0xFFC9C9C9),
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.end,
                                        children: [
                                          const Text(
                                            '\$15,981',
                                            style: TextStyle(
                                              fontFamily: 'BernardMTCondensed',
                                              fontWeight: FontWeight.w400,
                                              fontSize: 16,
                                              color: Colors.white,
                                            ),
                                          ),
                                          Row(
                                            children: const [
                                              Icon(Icons.arrow_drop_up_sharp,
                                                  color: Colors.green),
                                              Text(
                                                '0.67%',
                                                style: TextStyle(
                                                  fontFamily: 'BernardMTCondensed',
                                                  fontWeight: FontWeight.w400,
                                                  fontSize: 12,
                                                  color: Colors.green,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                );
                              },
                            ),
                          ),

                          const Spacer(),
                        ],
                      ),
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

  Widget _gradientPillButton({
    required Size mq,
    required String title,
    required VoidCallback onTap,
  }) {
    return Container(
      decoration: BoxDecoration(
        gradient: _mainGradient,
        borderRadius: BorderRadius.circular(50),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(50),
          onTap: onTap,
          child: Padding(
            padding: EdgeInsets.symmetric(
              horizontal: mq.width * 0.10,
              vertical: mq.height * 0.014,
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  title,
                  style: TextStyle(
                    fontFamily: 'BernardMTCondensed',
                    fontWeight: FontWeight.w400,
                    fontSize: mq.width * 0.040,
                    color: Colors.white,
                  ),
                ),
                SizedBox(width: mq.width * 0.03),
                const Icon(Icons.arrow_upward, color: Colors.white, size: 18),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _highlightPill({
    required Size mq,
    required Gradient gradient,
    required Widget leading,
    required String title,
    required String subtitle,
    double? subtitleSize,
  }) {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: mq.width * 0.03,
        vertical: mq.height * 0.010,
      ),
      decoration: BoxDecoration(
        gradient: gradient,
        borderRadius: BorderRadius.circular(60),
      ),
      child: Row(
        children: [
          SizedBox(
            width: 42, // ✅ fixed width so layout doesn't change
            height: 39, // ✅ fixed height so layout doesn't change
            child: Center(child: leading),
          ),
          SizedBox(width: mq.width * 0.02),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                FittedBox(
                  fit: BoxFit.scaleDown,
                  alignment: Alignment.centerLeft,
                  child: Text(
                    title,
                    style: TextStyle(
                      fontFamily: 'BernardMTCondensed',
                      fontWeight: FontWeight.w400,
                      fontSize: mq.width * 0.034,
                      color: Colors.white,
                    ),
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  subtitle,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    fontFamily: 'Benne',
                    fontWeight: FontWeight.w700,
                    fontSize: subtitleSize ?? (mq.width * 0.036),
                    color: Colors.white,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
