import 'package:flutter/material.dart';
import 'package:moon_launch/Back-end/Services/token_service.dart';
import 'package:moon_launch/Back-end/Services/wallet_service.dart';
import 'package:moon_launch/Front-end/Extra%20Widgets/buy_screen.dart';
import 'package:moon_launch/Front-end/Extra%20Widgets/sell_screen.dart';
import 'package:moon_launch/Front-end/Extra%20Widgets/swap_screen.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';
import 'package:moon_launch/Front-end/widgets/wallet_chart.dart';

class CoinDetailScreen extends StatefulWidget {
  final String? tokenAddress;
  const CoinDetailScreen({super.key, this.tokenAddress});

  @override
  State<CoinDetailScreen> createState() => _CoinDetailScreenState();
}

class _CoinDetailScreenState extends State<CoinDetailScreen> {
  final LinearGradient _circleGradient = const LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      Color(0xFFFFE600),
      Color(0xFFDB2519),
    ],
  );

  final LinearGradient _topIconCircleGradient = const LinearGradient(
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
    colors: [
      Color(0xFFFFE600),
      Color(0xFFDB2519),
    ],
  );

  int _selectedRangeIndex = 0;
  final List<String> _ranges = ['Live', '1D', '1M', '3M', '1Y', 'All'];

  TokenModel? _token;
  bool _loading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    if (widget.tokenAddress != null) {
      _loadToken();
    } else {
      _loading = false;
    }
  }

  Future<void> _loadToken() async {
    try {
      final token = await TokenService.getTokenDetail(widget.tokenAddress!);
      setState(() {
        _token = token;
        _loading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _loading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final Size mq = MediaQuery.of(context).size;

    return Scaffold(
      extendBodyBehindAppBar: true,
      backgroundColor: Colors.black,
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.transparent,
        automaticallyImplyLeading: false,
        toolbarHeight: mq.height * 0.12,
        titleSpacing: mq.width * 0.05,
        title: Padding(
          padding: EdgeInsets.only(top: mq.height * 0.018),
          child: Row(
            children: [
              _topBackCircleButton(
                mq: mq,
                onTap: () => Navigator.pop(context),
              ),
              const Spacer(),
              Image.asset(
                'assets/images/moon_launch_logo.png',
                width: mq.width * 0.32,
                fit: BoxFit.contain,
              ),
            ],
          ),
        ),
      ),
      body: AppBackground(
        child: SafeArea(
          child: SingleChildScrollView(
            padding: EdgeInsets.only(
              left: mq.width * 0.05,
              right: mq.width * 0.05,
              top: mq.height * 0.02,
              bottom: mq.height * 0.06,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                /// 🔹 COIN HEADER
                Row(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    SizedBox(
                      width: mq.width * 0.15,
                      height: mq.width * 0.15,
                      child: Image.asset('assets/images/bit_coin.png'),
                    ),
                    SizedBox(width: mq.width * 0.03),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'MemeCoin1',
                          style: TextStyle(
                            fontFamily: 'BernardMTCondensed',
                            fontSize: mq.width * 0.047,
                            color: Colors.white,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        SizedBox(height: mq.height * 0.003),
                        Row(
                          children: [
                            Text(
                              '12.234',
                              style: TextStyle(
                                fontFamily: 'Benne',
                                fontWeight: FontWeight.bold,
                                fontSize: mq.width * 0.045,
                                color: const Color(0xFFC9C9C9),
                              ),
                            ),
                            Text(
                              '     Averge (\$7,765)',
                              style: TextStyle(
                                fontFamily: 'Benne',
                                fontSize: mq.width * 0.035,
                                color: const Color(0xFFC9C9C9),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                    const Spacer(),
                    Image.asset('assets/images/heart.png', height: 45),
                    SizedBox(width: mq.width * 0.03),
                    Image.asset('assets/images/share.png', height: 45),
                  ],
                ),

                SizedBox(height: mq.height * 0.02),

                /// 🔹 PRICE
                RichText(
                  text: TextSpan(
                    style: const TextStyle(
                      fontFamily: 'BernardMTCondensed',
                      color: Colors.white,
                    ),
                    children: [
                      TextSpan(
                        text: '\$7,765,431 ',
                        style: TextStyle(
                          fontSize: mq.width * 0.10,
                          fontWeight: FontWeight.w900,
                        ),
                      ),
                      WidgetSpan(
                        alignment: PlaceholderAlignment.baseline,
                        baseline: TextBaseline.alphabetic,
                        child: Text(
                          'usd',
                          style: TextStyle(
                            fontSize: mq.width * 0.05,
                            color: Colors.white.withOpacity(0.85),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),

                SizedBox(height: mq.height * 0.008),

                Row(
                  children: [
                    Icon(Icons.arrow_drop_up,
                        color: Colors.green, size: mq.width * 0.07),
                    Text(
                      '0.72%',
                      style: TextStyle(
                        fontFamily: 'BernardMTCondensed',
                        fontSize: mq.width * 0.04,
                        color: Colors.green,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),

                SizedBox(height: mq.height * 0.015),

                /// 📈 CHART
                SizedBox(
                  height: mq.height * 0.23,
                  width: mq.width * 0.92,
                  child: const WalletChart(),
                ),

                SizedBox(height: mq.height * 0.008),

                /// 🔥 RANGE SELECTOR (BELOW GRAPH)
                Row(
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

                SizedBox(height: mq.height * 0.008),

                /// 🔘 ACTION BUTTONS
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    _filledGradientActionButton(
                      mq: mq,
                      icon: Icons.add,
                      label: 'Buy',
                      onTap: _token == null
                          ? null
                          : () => Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (_) => BuyScreen(
                                    tokenAddress: _token!.tokenAddress,
                                    tokenName: _token!.displayName,
                                    tokenSymbol: _token!.symbol,
                                    tokenLogo: _token!.logo,
                                  ),
                                ),
                              ),
                    ),
                    SizedBox(width: mq.width * 0.05),
                    _filledGradientActionButton(
                      mq: mq,
                      icon: Icons.arrow_upward,
                      label: 'Send',
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const SellScreen()),
                      ),
                    ),
                    SizedBox(width: mq.width * 0.05),
                    _filledGradientActionButton(
                      mq: mq,
                      icon: Icons.swap_vert,
                      label: 'Swap',
                      onTap: () {
                        if (_token == null) return;
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => SwapScreen(
                              token: WalletTokenModel(
                                tokenAddress: _token!.tokenAddress,
                                name: _token!.name,
                                symbol: _token!.symbol,
                                logo: _token!.logo,
                                decimals: int.tryParse(_token!.decimals ?? '18') ?? 18,
                                balance: '0',
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                  ],
                ),

                SizedBox(height: mq.height * 0.02),

                /// 🔻 ABOUT COIN
                Row(
                  children: [
                    Text(
                      'About Coin',
                      style: TextStyle(
                        fontFamily: 'BernardMTCondensed',
                        fontSize: mq.width * 0.07,
                        color: Colors.white,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    SizedBox(width: 15),
                    Image.asset('assets/images/flower.png', height: 26),
                    Image.asset('assets/images/divider.png', height: 24),
                    SizedBox(width: 5),
                    Image.asset('assets/images/3_people.png', height: 28),
                    SizedBox(width: 5),
                    Image.asset('assets/images/divider.png', height: 24),
                    Image.asset('assets/images/tick.png', height: 34),
                  ],
                ),

                SizedBox(height: mq.height * 0.012),

                Text(
                  'Lorem ipsum dolor sit amet, consectetur adipiscing elit. '
                  'Donec a pharetra augue. Nunc eu mauris arcu. Phasellus diam nibh, '
                  'rutrum id eros in, viverra commodo elit.',
                  style: TextStyle(
                    fontFamily: 'Benne',
                    fontSize: mq.width * 0.037,
                    color: Colors.white.withOpacity(0.75),
                    height: 1.45,
                  ),
                ),

                SizedBox(height: mq.height * 0.025),

                /// 🌐 SOCIAL ICONS
                Row(
                  children: [
                    _gradientIconContainer(
                        mq: mq,
                        imagePath: 'assets/images/www.png',
                        onTap: () {}),
                    SizedBox(width: mq.width * 0.03),
                    _gradientIconContainer(
                        mq: mq,
                        imagePath: 'assets/images/twitter.png',
                        onTap: () {}),
                    SizedBox(width: mq.width * 0.03),
                    _gradientIconContainer(
                        mq: mq,
                        imagePath: 'assets/images/telegram.png',
                        onTap: () {}),
                  ],
                ),

                SizedBox(height: mq.height * 0.10),
              ],
            ),
          ),
        ),
      ),
    );
  }

  /// ================= HELPERS =================

  Widget _topBackCircleButton({required Size mq, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(999),
      child: Container(
        width: mq.width * 0.11,
        height: mq.width * 0.11,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: Colors.white.withOpacity(0.10),
        ),
        child: Center(
          child: Icon(
            Icons.arrow_back_ios_new_rounded,
            color: Colors.white,
            size: mq.width * 0.045,
          ),
        ),
      ),
    );
  }

  Widget _filledGradientActionButton({
    required Size mq,
    required IconData icon,
    required String label,
    VoidCallback? onTap,
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

  Widget _gradientIconContainer({
    required Size mq,
    required String imagePath,
    required VoidCallback onTap,
  }) {
    final double size = mq.width * 0.15;
    final double ring = (size * 0.06).clamp(3.0, 6.0);

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(999),
      child: SizedBox(
        width: size,
        height: size,
        child: Padding(
          padding: EdgeInsets.all(ring),
          child: ClipOval(
            child: Image.asset(imagePath, fit: BoxFit.contain),
          ),
        ),
      ),
    );
  }
}
