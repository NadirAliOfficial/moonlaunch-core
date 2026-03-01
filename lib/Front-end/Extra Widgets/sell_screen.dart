import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/Extra%20Widgets/qr_scan_screen.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';

class SellScreen extends StatefulWidget {
  const SellScreen({super.key});

  static const LinearGradient btnGradient = LinearGradient(
    colors: [Color(0xFFFFE600), Color(0xFFDB2519)],
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
  );

  @override
  State<SellScreen> createState() => _SellScreenState();
}

class _SellScreenState extends State<SellScreen> {
  final TextEditingController _addressController = TextEditingController();

  @override
  void dispose() {
    _addressController.dispose();
    super.dispose();
  }

  Future<void> _openQrScanner() async {
    final String? result = await Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => const QrScanScreen()),
    );

    if (result != null && result.trim().isNotEmpty) {
      setState(() => _addressController.text = result.trim());
    }
  }

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
                  "Send",
                  style: TextStyle(
                    fontFamily: "BernardMTCondensed",
                    fontSize: mq.width * 0.085,
                    color: Colors.white,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                SizedBox(height: mq.height * 0.03),

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

                // Address field + Paste + Scan icon
                _pill(
                  mq,
                  child: Row(
                    children: [
                      const SizedBox(width: 18),

                      // Address TextField (readOnly feel, but editable bhi rakh sakti ho)
                      Expanded(
                        child: TextField(
                          controller: _addressController,
                          style: TextStyle(
                            fontFamily: "Benne",
                            color: Colors.white.withOpacity(.9),
                            fontSize: 14,
                          ),
                          cursorColor: Colors.white,
                          decoration: InputDecoration(
                            hintText: "Wallet Address",
                            hintStyle: TextStyle(
                              fontFamily: "Benne",
                              color: Colors.white.withOpacity(.45),
                              fontSize: 14,
                            ),
                            border: InputBorder.none,
                          ),
                        ),
                      ),

                      // Paste (abhi UI only)
                      ShaderMask(
                        shaderCallback: (b) => SellScreen.btnGradient.createShader(b),
                        child: const Text(
                          "Paste",
                          style: TextStyle(
                            fontFamily: "BernardMTCondensed",
                            color: Colors.white,
                            fontSize: 18,
                          ),
                        ),
                      ),

                      const SizedBox(width: 10),

                      // QR Scanner Icon (CLICK)
                      InkWell(
                        onTap: _openQrScanner,
                        borderRadius: BorderRadius.circular(999),
                        child: Padding(
                          padding: const EdgeInsets.all(6),
                          child: ShaderMask(
                            shaderCallback: (b) => SellScreen.btnGradient.createShader(b),
                            child: const Icon(
                              Icons.qr_code_scanner,
                              color: Colors.white,
                              size: 22,
                            ),
                          ),
                        ),
                      ),

                      const SizedBox(width: 18),
                    ],
                  ),
                ),

                const SizedBox(height: 12),

                // Destination Network (filled slightly yellow tint)
                Container(
                  height: mq.height * 0.065,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(40),
                    border: Border.all(color: Colors.white.withOpacity(.35)),
                    color: const Color(0xFFFFE600).withOpacity(0.10),
                  ),
                  child: Row(
                    children: [
                      const SizedBox(width: 18),
                      Text(
                        "Destination Network",
                        style: TextStyle(
                          fontFamily: "Benne",
                          color: Colors.white.withOpacity(.7),
                          fontSize: 14,
                        ),
                      ),
                      const Spacer(),
                      // ShaderMask(
                      //   shaderCallback: (b) => SellScreen.btnGradient.createShader(b),
                      //   child: const Icon(Icons.keyboard_arrow_down,
                      //       color: Colors.white, size: 28),
                      // ),
                      const SizedBox(width: 18),
                    ],
                  ),
                ),

                const SizedBox(height: 12),

                // Amount field + Max (as-is)
                _pill(
                  mq,
                  child: Row(
                    children: [
                      const SizedBox(width: 18),
                      Expanded(
                        child: Text(
                          "Amount(Meme Coin)",
                          style: TextStyle(
                            fontFamily: "Benne",
                            color: Colors.white.withOpacity(.8),
                            fontSize: 14,
                          ),
                        ),
                      ),
                      ShaderMask(
                        shaderCallback: (b) => SellScreen.btnGradient.createShader(b),
                        child: const Text(
                          "Max",
                          style: TextStyle(
                            fontFamily: "BernardMTCondensed",
                            color: Colors.white,
                            fontSize: 18,
                          ),
                        ),
                      ),
                      const SizedBox(width: 18),
                    ],
                  ),
                ),

                const Spacer(),

                _bottomGradientButton(text: "Next", onTap: () {}),

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
          gradient: SellScreen.btnGradient,
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
