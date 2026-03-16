import 'package:flutter/material.dart';
import 'package:moon_launch/Back-end/Controllers/session_controller.dart';
import 'package:moon_launch/Back-end/Services/wallet_service.dart';
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

// ── Feature flags — set to true to re-enable ──────────────────────────────
const bool _showWalletChart = false;
// ──────────────────────────────────────────────────────────────────────────

class _WalletScreenState extends State<WalletScreen> {
  final LinearGradient _circleGradient = const LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [Color(0xFFFFE600), Color(0xFFDB2519)],
  );

  int _selectedRangeIndex = 0;
  final List<String> _ranges = ['Live', '1D', '1M', '3M', '1Y', 'All'];

  WalletBalanceModel? _wallet;
  bool _loading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadBalance();
  }

  Future<void> _loadBalance() async {
    final address = SessionController.instance.walletAddress;
    if (address == null || address.isEmpty) {
      setState(() {
        _error = 'No wallet address found';
        _loading = false;
      });
      return;
    }
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final wallet = await WalletService.getBalance(address);
      setState(() {
        _wallet = wallet;
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
          child: RefreshIndicator(
            onRefresh: _loadBalance,
            color: const Color(0xFFFFE600),
            child: Column(
              children: [
                // BNB balance
                Padding(
                  padding:
                      EdgeInsets.symmetric(horizontal: mq.width * 0.05),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      if (_loading)
                        Padding(
                          padding: EdgeInsets.only(top: mq.height * 0.01),
                          child: const SizedBox(
                            height: 28,
                            width: 28,
                            child: CircularProgressIndicator(
                              color: Color(0xFFFFE600),
                              strokeWidth: 2.5,
                            ),
                          ),
                        )
                      else if (_error != null)
                        GestureDetector(
                          onTap: _loadBalance,
                          child: Text(
                            'Tap to retry',
                            style: TextStyle(
                              fontFamily: 'Benne',
                              fontSize: mq.width * 0.038,
                              color: const Color(0xFFDB2519),
                            ),
                          ),
                        )
                      else ...[
                        // USD value — big
                        RichText(
                          text: TextSpan(
                            style: const TextStyle(
                              fontFamily: 'BernardMTCondensed',
                              color: Colors.white,
                            ),
                            children: [
                              TextSpan(
                                text: _wallet?.displayUsd ?? '--',
                                style: TextStyle(fontSize: mq.width * 0.090),
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
                                      fontSize: mq.width * 0.038,
                                      color: Colors.white,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                        // BNB balance — smaller below
                        Text(
                          '${_wallet?.displayBnb ?? '--'} BNB',
                          style: TextStyle(
                            fontFamily: 'BernardMTCondensed',
                            fontSize: mq.width * 0.052,
                            color: Colors.white,
                          ),
                        ),
                      ],
                      if (!_loading && _error == null)
                        Text(
                          SessionController.instance.walletAddress != null
                              ? '${SessionController.instance.walletAddress!.substring(0, 6)}...${SessionController.instance.walletAddress!.substring(SessionController.instance.walletAddress!.length - 4)}'
                              : '',
                          style: TextStyle(
                            fontFamily: 'Benne',
                            fontSize: mq.width * 0.032,
                            color: Colors.white54,
                          ),
                        ),
                    ],
                  ),
                ),

                if (_showWalletChart) const SizedBox(height: 10),

                // Chart
                if (_showWalletChart) SizedBox(
                  height: mq.height * 0.23,
                  width: mq.width * 0.92,
                  child: const WalletChart(),
                ),

                if (_showWalletChart) const SizedBox(height: 8),

                // Range selector
                if (_showWalletChart) Padding(
                  padding: EdgeInsets.symmetric(horizontal: mq.width * 0.05),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: List.generate(_ranges.length, (index) {
                      final bool isActive = _selectedRangeIndex == index;
                      return GestureDetector(
                        onTap: () =>
                            setState(() => _selectedRangeIndex = index),
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

                if (_showWalletChart) const SizedBox(height: 16),

                // Action buttons
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    _actionButton(
                      mq: mq,
                      icon: Icons.add,
                      label: 'Buy',
                      onTap: () => ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text(
                            'Tap a token from the feed to buy',
                            style: TextStyle(fontFamily: 'Benne'),
                          ),
                          backgroundColor: Color(0xFF1A1A1A),
                          duration: Duration(seconds: 2),
                        ),
                      ),
                    ),
                    SizedBox(width: mq.width * 0.05),
                    _actionButton(
                      mq: mq,
                      icon: Icons.arrow_upward,
                      label: 'Send',
                      onTap: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                              builder: (_) => const SellScreen())),
                    ),
                    SizedBox(width: mq.width * 0.05),
                    _actionButton(
                      mq: mq,
                      icon: Icons.arrow_downward,
                      label: 'Receive',
                      onTap: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                              builder: (_) => const ReceiveScreen())),
                    ),
                    SizedBox(width: mq.width * 0.05),
                    _actionButton(
                      mq: mq,
                      icon: Icons.swap_vert,
                      label: 'Swap',
                      onTap: () => ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text(
                            'Tap a token from Your Coins below to swap',
                            style: TextStyle(fontFamily: 'Benne'),
                          ),
                          backgroundColor: Color(0xFF1A1A1A),
                          duration: Duration(seconds: 2),
                        ),
                      ),
                    ),
                  ],
                ),

                SizedBox(height: mq.height * 0.02),

                // Your Coins header
                Padding(
                  padding:
                      EdgeInsets.symmetric(horizontal: mq.width * 0.05),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Your Coins',
                        style: TextStyle(
                          fontFamily: 'Benne',
                          fontSize: 18,
                          color: Colors.white,
                        ),
                      ),
                      if (!_loading && _wallet != null)
                        Text(
                          '${_wallet!.tokens.length} token${_wallet!.tokens.length == 1 ? '' : 's'}',
                          style: const TextStyle(
                            fontFamily: 'Benne',
                            fontSize: 13,
                            color: Colors.white54,
                          ),
                        ),
                    ],
                  ),
                ),

                SizedBox(height: mq.height * 0.012),

                // Token list
                Expanded(
                  child: _loading
                      ? const Center(
                          child: CircularProgressIndicator(
                            color: Color(0xFFFFE600),
                          ),
                        )
                      : _error != null
                          ? Center(
                              child: Text(
                                'Could not load tokens',
                                style: TextStyle(
                                  fontFamily: 'Benne',
                                  color: Colors.white54,
                                  fontSize: mq.width * 0.038,
                                ),
                              ),
                            )
                          : _wallet == null || _wallet!.tokens.isEmpty
                              ? Center(
                                  child: Column(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Text(
                                        'No tokens yet',
                                        style: TextStyle(
                                          fontFamily: 'Benne',
                                          color: Colors.white54,
                                          fontSize: mq.width * 0.04,
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Text(
                                        'Pull down to refresh after buying',
                                        style: TextStyle(
                                          fontFamily: 'Benne',
                                          color: Colors.white30,
                                          fontSize: mq.width * 0.033,
                                        ),
                                      ),
                                    ],
                                  ),
                                )
                              : ListView.separated(
                                  padding: EdgeInsets.symmetric(
                                      horizontal: mq.width * 0.05),
                                  itemCount: _wallet!.tokens.length,
                                  separatorBuilder: (_, __) => Divider(
                                    color: Colors.white.withOpacity(0.18),
                                    height: 18,
                                  ),
                                  itemBuilder: (context, index) {
                                    final token =
                                        _wallet!.tokens[index];
                                    return InkWell(
                                      onTap: () => Navigator.push(
                                        context,
                                        MaterialPageRoute(
                                          builder: (_) => SwapScreen(token: token),
                                        ),
                                      ),
                                      borderRadius: BorderRadius.circular(12),
                                      child: Row(
                                      children: [
                                        _tokenLogo(token, size: 40),
                                        const SizedBox(width: 12),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                token.displayName,
                                                style: const TextStyle(
                                                  fontFamily:
                                                      'BernardMTCondensed',
                                                  fontSize: 16,
                                                  color: Colors.white,
                                                ),
                                              ),
                                              const SizedBox(height: 2),
                                              Text(
                                                token.symbol ?? '',
                                                style: const TextStyle(
                                                  fontFamily: 'Benne',
                                                  fontSize: 12,
                                                  color: Color(0xFFC9C9C9),
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        Text(
                                          token.balance,
                                          style: const TextStyle(
                                            fontFamily: 'BernardMTCondensed',
                                            fontSize: 16,
                                            color: Colors.white,
                                          ),
                                        ),
                                      ],
                                    ),
                                    );
                                  },
                                ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _tokenLogo(WalletTokenModel token, {required double size}) {
    if (token.logo != null && token.logo!.isNotEmpty) {
      return SizedBox(
        width: size,
        height: size,
        child: ClipRRect(
          borderRadius: BorderRadius.circular(size),
          child: Image.network(
            token.logo!,
            fit: BoxFit.cover,
            errorBuilder: (_, __, ___) => _defaultIcon(size),
          ),
        ),
      );
    }
    return _defaultIcon(size);
  }

  Widget _defaultIcon(double size) {
    return Image.asset(
      'assets/images/bit_coin.png',
      width: size,
      height: size,
    );
  }

  Widget _actionButton({
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
