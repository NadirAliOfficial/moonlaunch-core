import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/Extra%20Widgets/buy_screen.dart';
import 'package:moon_launch/Front-end/Extra%20Widgets/receive_screen.dart';
import 'package:moon_launch/Front-end/Extra%20Widgets/sell_screen.dart';
import 'package:moon_launch/Front-end/Extra%20Widgets/swap_screen.dart';
import 'package:moon_launch/Front-end/widgets/profile_background.dart';
import 'package:moon_launch/Front-end/widgets/wallet_chart.dart';

class WalletScreen extends StatefulWidget {
  const WalletScreen({super.key});

  @override
  State<WalletScreen> createState() => _WalletScreenState();
}

class _WalletScreenState extends State<WalletScreen> {
  final LinearGradient _circleGradient = const LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      Color(0xFFFFE600),
      Color(0xFFDB2519),
    ],
  );

  int _selectedRangeIndex = 0;

  final List<String> _ranges = ['Live', '1D', '1M', '3M', '1Y', 'All'];

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
        titleSpacing: mq.width * 0.045,
        title: Padding(
          padding: EdgeInsets.only(top: mq.height * 0.02),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'Total Wallet Value',
                style: TextStyle(
                  fontFamily: 'Benne',
                  fontSize: mq.width * 0.038,
                  color: const Color(0xFFC9C9C9),
                ),
              ),
              SizedBox(width: mq.width * 0.02),
              Image.asset(
                'assets/images/close_eye_icon.png',
                width: mq.width * 0.06,
              ),
            ],
          ),
        ),
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
            ),
          ),
        ],
      ),
      body: ProfileBackground(
        child: SafeArea(
          child: Column(
            children: [
              Padding(
                padding: EdgeInsets.symmetric(horizontal: mq.width * 0.05),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    RichText(
                      text: TextSpan(
                        style: const TextStyle(
                          fontFamily: 'BernardMTCondensed',
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
                                style:
                                    TextStyle(fontSize: mq.width * 0.045),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    Row(
                      children: const [
                        Icon(Icons.arrow_drop_up,
                            color: Colors.green, size: 26),
                        Text(
                          '0.72%',
                          style: TextStyle(
                            fontFamily: 'BernardMTCondensed',
                            fontSize: 16,
                            color: Colors.green,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 10),

              /// 📈 CHART
              SizedBox(
                height: mq.height * 0.23,
                width: mq.width * 0.92,
                child: const WalletChart(),
              ),

              const SizedBox(height: 8),

              /// 🔥 LIVE / RANGE SELECTOR (NOW BELOW GRAPH)
              Padding(
                padding: EdgeInsets.symmetric(horizontal: mq.width * 0.05),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: List.generate(_ranges.length, (index) {
                    final bool isActive = _selectedRangeIndex == index;

                    return GestureDetector(
                      onTap: () {
                        setState(() {
                          _selectedRangeIndex = index;
                        });
                      },
                      child: Row(
                        children: [
                          if (_ranges[index] == 'Live') ...[
                            Container(
                              width: 6,
                              height: 6,
                              decoration: const BoxDecoration(
                                color: Colors.red,
                                shape: BoxShape.circle,
                              ),
                            ),
                            const SizedBox(width: 4),
                          ],
                          Text(
                            _ranges[index],
                            style: TextStyle(
                              fontFamily: 'Benne',
                              fontSize: 14,
                              fontWeight: isActive
                                  ? FontWeight.w600
                                  : FontWeight.w400,
                              color: isActive
                                  ? Colors.white
                                  : Colors.white.withOpacity(0.55),
                            ),
                          ),
                        ],
                      ),
                    );
                  }),
                ),
              ),

              const SizedBox(height: 16),

              /// 🔘 ACTION BUTTONS
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  _filledGradientActionButton(
                    mq: mq,
                    icon: Icons.add,
                    label: 'Buy',
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => const BuyScreen())),
                  ),
                  SizedBox(width: mq.width * 0.05),
                  _filledGradientActionButton(
                    mq: mq,
                    icon: Icons.arrow_upward,
                    label: 'Send',
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => const SellScreen())),
                  ),
                  SizedBox(width: mq.width * 0.05),
                  _filledGradientActionButton(
                    mq: mq,
                    icon: Icons.arrow_downward,
                    label: 'Receive',
                    onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) => const ReceiveScreen())),
                  ),
                  SizedBox(width: mq.width * 0.05),
                  _filledGradientActionButton(
                    mq: mq,
                    icon: Icons.swap_vert,
                    label: 'Swap',
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => const SwapScreen())),
                  ),
                ],
              ),

              SizedBox(height: mq.height * 0.02),

              /// 🪙 YOUR COINS
              Padding(
                padding: EdgeInsets.symmetric(horizontal: mq.width * 0.05),
                child: Row(
                  children: const [
                    Text(
                      'Your Coins',
                      style: TextStyle(
                        fontFamily: 'Benne',
                        fontSize: 18,
                        color: Colors.white,
                      ),
                    ),
                  ],
                ),
              ),

              SizedBox(height: mq.height * 0.012),

              Expanded(
                child: ListView.separated(
                  padding:
                      EdgeInsets.symmetric(horizontal: mq.width * 0.05),
                  itemCount: 6,
                  separatorBuilder: (_, __) => Divider(
                    color: Colors.white.withOpacity(0.18),
                    height: 18,
                  ),
                  itemBuilder: (context, index) {
                    return Row(
                      children: [
                        Image.asset(
                          'assets/images/bit_coin.png',
                          width: 40,
                          height: 40,
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: const [
                              Text(
                                'MemeCoin1',
                                style: TextStyle(
                                  fontFamily: 'BernardMTCondensed',
                                  fontSize: 16,
                                  color: Colors.white,
                                ),
                              ),
                              SizedBox(height: 2),
                              Text(
                                'Value',
                                style: TextStyle(
                                  fontFamily: 'Benne',
                                  fontSize: 12,
                                  color: Color(0xFFC9C9C9),
                                ),
                              ),
                            ],
                          ),
                        ),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: const [
                            Text(
                              '\$15,981',
                              style: TextStyle(
                                fontFamily: 'BernardMTCondensed',
                                fontSize: 16,
                                color: Colors.white,
                              ),
                            ),
                            Row(
                              children: [
                                Icon(Icons.arrow_drop_up,
                                    color: Colors.green, size: 22),
                                Text(
                                  '0.67%',
                                  style: TextStyle(
                                    fontFamily: 'BernardMTCondensed',
                                    fontSize: 12,
                                    color: Colors.green,
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ],
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _filledGradientActionButton({
    required Size mq,
    required IconData icon,
    required String label,
    required VoidCallback onTap,
  }) {
    final double size = mq.width * 0.165;

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(100),
      child: Column(
        children: [
          Container(
            width: size,
            height: size,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              gradient: _circleGradient,
            ),
            child: Center(
              child: Icon(icon, color: Colors.white, size: 34),
            ),
          ),
          const SizedBox(height: 8),
          Text(
            label,
            style: const TextStyle(
              fontFamily: 'Benne',
              fontSize: 14,
              color: Colors.white,
            ),
          ),
        ],
      ),
    );
  }
}
