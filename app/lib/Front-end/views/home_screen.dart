import 'package:flutter/material.dart';
import 'package:moon_launch/Back-end/Controllers/session_controller.dart';
import 'package:moon_launch/Back-end/Services/token_service.dart';
import 'package:moon_launch/Back-end/Services/wallet_service.dart';
import 'package:moon_launch/Front-end/Extra Widgets/receive_screen.dart';
import 'package:moon_launch/Front-end/views/coin_detail_screen.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

// ── Feature flags — set to true to re-enable ──────────────────────────────
const bool _showSearch = false;
const bool _showHighlights = false;
const bool _showRewards = false;
// ──────────────────────────────────────────────────────────────────────────

class _HomeScreenState extends State<HomeScreen> {
  final LinearGradient _mainGradient = const LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [Color(0xFFA21117), Color(0xFF251216)],
  );

  List<TokenModel> _tokens = [];
  bool _loading = true;
  String? _error;

  String _bnbBalance = '--';
  String _usdValue = '--';

  @override
  void initState() {
    super.initState();
    _loadTokens();
    _loadBnb();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      precacheImage(const AssetImage('assets/images/reward_coin.png'), context);
    });
  }

  Future<void> _loadBnb() async {
    final address = SessionController.instance.walletAddress;
    if (address == null || address.isEmpty) return;
    try {
      final wallet = await WalletService.getBalance(address);
      if (mounted) {
        setState(() {
          _bnbBalance = wallet.displayBnb;
          _usdValue = wallet.displayUsd;
        });
      }
    } catch (_) {}
  }

  Future<void> _loadTokens() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final tokens = await TokenService.getNewLaunches(limit: 20);
      setState(() {
        _tokens = tokens;
        _loading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _loading = false;
      });
    }
  }

  Widget _letterAvatar({required double size, required String name}) {
    final letter = name.isNotEmpty ? name[0].toUpperCase() : '?';
    return Container(
      width: size,
      height: size,
      decoration: const BoxDecoration(
        color: Colors.white,
        shape: BoxShape.circle,
      ),
      alignment: Alignment.center,
      child: Text(
        letter,
        style: TextStyle(
          fontSize: size * 0.45,
          fontWeight: FontWeight.bold,
          color: Colors.black,
        ),
      ),
    );
  }

  Widget _tokenLogo(TokenModel token, {required double size}) {
    final fallback = _letterAvatar(size: size, name: token.displayName);
    if (token.logo != null && token.logo!.isNotEmpty) {
      return SizedBox(
        width: size,
        height: size,
        child: ClipRRect(
          borderRadius: BorderRadius.circular(size),
          child: Image.network(
            token.logo!,
            fit: BoxFit.cover,
            errorBuilder: (_, _, _) => fallback,
          ),
        ),
      );
    }
    return fallback;
  }

  Widget _buildHeader(BuildContext context, Size mq) {
    return Padding(
      padding: EdgeInsets.only(top: mq.height * 0.01),
      child: Column(
        children: [
          // Search bar
          if (_showSearch)
            Padding(
              padding: EdgeInsets.symmetric(horizontal: mq.width * 0.06),
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
                    Icon(
                      Icons.search,
                      color: Colors.white.withOpacity(0.85),
                      size: 20,
                    ),
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
                          contentPadding: const EdgeInsets.symmetric(
                            vertical: 0,
                          ),
                        ),
                      ),
                    ),
                    SizedBox(width: mq.width * 0.04),
                  ],
                ),
              ),
            ),

          SizedBox(height: mq.height * 0.03),

          // Total wallet value label
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

          // USD value — big
          RichText(
            text: TextSpan(
              style: const TextStyle(
                fontFamily: 'BernardMTCondensed',
                fontWeight: FontWeight.w400,
                color: Colors.white,
              ),
              children: [
                TextSpan(
                  text: _usdValue,
                  style: TextStyle(fontSize: mq.width * 0.100),
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
                        fontSize: mq.width * 0.042,
                        color: Colors.white,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),

          // BNB balance — smaller below
          RichText(
            text: TextSpan(
              style: const TextStyle(
                fontFamily: 'BernardMTCondensed',
                fontWeight: FontWeight.w400,
                color: Colors.white,
              ),
              children: [
                TextSpan(
                  text: _bnbBalance,
                  style: TextStyle(fontSize: mq.width * 0.058),
                ),
                WidgetSpan(
                  alignment: PlaceholderAlignment.baseline,
                  baseline: TextBaseline.alphabetic,
                  child: Padding(
                    padding: const EdgeInsets.only(left: 3),
                    child: Text(
                      'BNB',
                      style: TextStyle(
                        fontFamily: 'BernardMTCondensed',
                        fontWeight: FontWeight.w400,
                        fontSize: mq.width * 0.038,
                        color: Colors.white,
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
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const ReceiveScreen()),
            ),
          ),

          SizedBox(height: mq.height * 0.035),

          // Highlights / Rewards titles
          if (_showHighlights || _showRewards)
            Padding(
              padding: EdgeInsets.symmetric(horizontal: mq.width * 0.08),
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

          // Highlight cards
          if (_showHighlights || _showRewards)
            Padding(
              padding: EdgeInsets.symmetric(horizontal: mq.width * 0.06),
              child: Row(
                children: [
                  Expanded(
                    child: _highlightPill(
                      mq: mq,
                      gradient: _mainGradient,
                      leading: _tokens.isNotEmpty
                          ? _tokenLogo(_tokens.first, size: 58)
                          : _letterAvatar(size: 58, name: '?'),
                      title: _tokens.isNotEmpty
                          ? _tokens.first.displayName
                          : '--',
                      subtitle: _tokens.isNotEmpty
                          ? (_tokens.first.symbol ?? 'New')
                          : 'Loading...',
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

          if (_showHighlights || _showRewards)
            SizedBox(height: mq.height * 0.025),

          // New Launches header
          Padding(
            padding: EdgeInsets.symmetric(horizontal: mq.width * 0.07),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'New Launches',
                  style: TextStyle(
                    fontFamily: 'Benne',
                    fontWeight: FontWeight.w400,
                    fontSize: mq.width * 0.05,
                    color: Colors.white,
                  ),
                ),
                InkWell(
                  onTap: _loadTokens,
                  child: Text(
                    'Refresh',
                    style: TextStyle(
                      fontFamily: 'Benne',
                      fontWeight: FontWeight.w400,
                      fontSize: mq.width * 0.04,
                      color: const Color(0xFFFFE600),
                    ),
                  ),
                ),
              ],
            ),
          ),

          SizedBox(height: mq.height * 0.012),
        ],
      ),
    );
  }

  Widget _buildTokenList(BuildContext context, Size mq) {
    if (_loading) {
      return const Center(
        child: CircularProgressIndicator(color: Color(0xFFFFE600)),
      );
    } else if (_error != null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              'Failed to load launches',
              style: TextStyle(
                fontFamily: 'Benne',
                color: Colors.white70,
                fontSize: mq.width * 0.04,
              ),
            ),
            const SizedBox(height: 8),
            GestureDetector(
              onTap: _loadTokens,
              child: Text(
                'Tap to retry',
                style: TextStyle(
                  fontFamily: 'Benne',
                  color: const Color(0xFFFFE600),
                  fontSize: mq.width * 0.038,
                ),
              ),
            ),
          ],
        ),
      );
    } else if (_tokens.isEmpty) {
      return Center(
        child: Text(
          'No launches yet',
          style: TextStyle(
            fontFamily: 'Benne',
            color: Colors.white54,
            fontSize: mq.width * 0.04,
          ),
        ),
      );
    } else {
      return ListView.builder(
        padding: EdgeInsets.only(
          left: mq.width * 0.04,
          right: mq.width * 0.04,
          bottom: mq.height * 0.02,
        ),
        physics: const AlwaysScrollableScrollPhysics(),
        itemCount: _tokens.length,
        itemBuilder: (context, index) {
          final token = _tokens[index];
          return InkWell(
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) =>
                      CoinDetailScreen(tokenAddress: token.tokenAddress),
                ),
              );
            },
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 14),
              decoration: const BoxDecoration(
                border: Border(
                  top: BorderSide(width: 1.0, color: Color(0xFFCDCDCD)),
                ),
              ),
              child: Row(
                children: [
                  _tokenLogo(token, size: 46),
                  SizedBox(width: mq.width * 0.02),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          token.displayName,
                          style: const TextStyle(
                            fontFamily: 'BernardMTCondensed',
                            fontWeight: FontWeight.w400,
                            fontSize: 16,
                            color: Colors.white,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          token.symbol ?? '',
                          style: const TextStyle(
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
                      Text(
                        token.displayPrice,
                        style: const TextStyle(
                          fontFamily: 'BernardMTCondensed',
                          fontWeight: FontWeight.w400,
                          fontSize: 16,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        token.priceCurrency ?? 'USD',
                        style: const TextStyle(
                          fontFamily: 'Benne',
                          fontSize: 11,
                          color: Color(0xFFC9C9C9),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          );
        },
      );
    }
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
      body: AppBackground(
        child: SafeArea(
          child: LayoutBuilder(
            builder: (context, constraints) {
              return Column(
                mainAxisSize: MainAxisSize.max,
                children: [
                  _buildHeader(context, mq),
                  Expanded(
                    child: RefreshIndicator(
                      onRefresh: _loadTokens,
                      color: const Color(0xFFFFE600),
                      child: _buildTokenList(context, mq),
                    ),
                  ),
                ],
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
        border: Border.all(color: Color(0xFFca4e5b), width: 1.5),
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
        border: Border.all(color: Color(0xFFca4e5b), width: 1.5),
      ),
      child: Row(
        children: [
          SizedBox(width: 42, height: 39, child: Center(child: leading)),
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
