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

// ── Feature flags — set to true to re-enable ──────────────────────────────
const bool _showCoinChart = false;
const bool _showAboutCoin = false;
// ──────────────────────────────────────────────────────────────────────────

class _CoinDetailScreenState extends State<CoinDetailScreen> {
  final LinearGradient _circleGradient = const LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [Color(0xFFA21117), Color(0xFF251216)],
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
      backgroundColor: const Color(0xFF14121f),
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
              _topBackCircleButton(mq: mq, onTap: () => Navigator.pop(context)),
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
                    _letterAvatar(
                      size: mq.width * 0.15,
                      name: _token?.displayName ?? '',
                    ),
                    SizedBox(width: mq.width * 0.03),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          _token?.displayName ?? '--',
                          style: TextStyle(
                            fontFamily: 'BernardMTCondensed',
                            fontSize: mq.width * 0.047,
                            color: Colors.white,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        SizedBox(height: mq.height * 0.003),
                        Text(
                          _token?.symbol ?? '',
                          style: TextStyle(
                            fontFamily: 'Benne',
                            fontSize: mq.width * 0.035,
                            color: const Color(0xFFC9C9C9),
                          ),
                        ),
                      ],
                    ),
                    const Spacer(),
                  ],
                ),

                SizedBox(height: mq.height * 0.02),

                /// 🔹 PRICE
                if (_token != null)
                  RichText(
                    text: TextSpan(
                      style: const TextStyle(
                        fontFamily: 'BernardMTCondensed',
                        color: Colors.white,
                      ),
                      children: [
                        TextSpan(
                          text: '${_truncatePrice(_token!.displayPrice)} ',
                          style: TextStyle(
                            fontSize: mq.width * 0.065,
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

                SizedBox(height: mq.height * 0.015),

                /// 📈 CHART
                if (_showCoinChart) SizedBox(
                  height: mq.height * 0.23,
                  width: mq.width * 0.92,
                  child: const WalletChart(),
                ),

                if (_showCoinChart) SizedBox(height: mq.height * 0.008),

                /// 🔥 RANGE SELECTOR (BELOW GRAPH)
                if (_showCoinChart) Row(
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

                if (_showCoinChart) SizedBox(height: mq.height * 0.008),

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
                      onTap: _token == null
                          ? null
                          : () => Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => SellScreen(
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
                                decimals:
                                    int.tryParse(_token!.decimals ?? '18') ??
                                    18,
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
                if (_showAboutCoin) Row(
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
                    const SizedBox(width: 15),
                    Image.asset('assets/images/flower.png', height: 26),
                    Image.asset('assets/images/divider.png', height: 24),
                    const SizedBox(width: 5),
                    Image.asset('assets/images/3_people.png', height: 28),
                    const SizedBox(width: 5),
                    Image.asset('assets/images/divider.png', height: 24),
                    Image.asset('assets/images/tick.png', height: 34),
                  ],
                ),

                if (_showAboutCoin) SizedBox(height: mq.height * 0.012),

                if (_showAboutCoin) Text(
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

                if (_showAboutCoin) SizedBox(height: mq.height * 0.025),

                /// 🌐 SOCIAL ICONS
                if (_showAboutCoin) Row(
                  children: [
                    _gradientIconContainer(
                      mq: mq,
                      imagePath: 'assets/images/www.png',
                      onTap: () {},
                    ),
                    SizedBox(width: mq.width * 0.03),
                    _gradientIconContainer(
                      mq: mq,
                      imagePath: 'assets/images/twitter.png',
                      onTap: () {},
                    ),
                    SizedBox(width: mq.width * 0.03),
                    _gradientIconContainer(
                      mq: mq,
                      imagePath: 'assets/images/telegram.png',
                      onTap: () {},
                    ),
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

  String _truncatePrice(String price) {
    if (!price.startsWith('\$')) return price;
    final num = price.substring(1);
    if (num.length <= 10) return price;
    return '\$${num.substring(0, 10)}…';
  }

  Widget _letterAvatar({required double size, required String name}) {
    final letter = name.isNotEmpty ? name[0].toUpperCase() : '?';
    return Container(
      width: size,
      height: size,
      decoration: const BoxDecoration(color: Colors.white, shape: BoxShape.circle),
      alignment: Alignment.center,
      child: Text(
        letter,
        style: TextStyle(fontSize: size * 0.45, fontWeight: FontWeight.bold, color: Colors.black),
      ),
    );
  }

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
            child: Center(child: Icon(icon, color: Colors.white, size: 34)),
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
          child: ClipOval(child: Image.asset(imagePath, fit: BoxFit.contain)),
        ),
      ),
    );
  }
}
