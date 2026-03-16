import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'notifiers.dart';

class CustomBottomNavBar extends StatelessWidget {
  const CustomBottomNavBar({super.key});

  @override
  Widget build(BuildContext context) {
    final Size mqSize = MediaQuery.of(context).size;

    return SafeArea(
      bottom: true,
      child: Padding(
        padding: const EdgeInsets.only(bottom: 6),
        child: SizedBox(
          width: mqSize.width,
          height: 80,
          child: Stack(
            clipBehavior: Clip.none,
            children: [
              /// Background with border
              CustomPaint(
                size: Size(mqSize.width, 80),
                painter: BNBCustomPainter(),
              ),

              // ✅ Center Button (Launch) — hidden until Phase 2 (_showLaunchButton = false)
              if (false) Positioned(
                top: -20,
                left: mqSize.width / 2 - 35,
                child: Container(
                  height: 70,
                  width: 70,
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFFA21117), Color(0xFF251216)],
                    ),
                    borderRadius: BorderRadius.circular(50),
                    border: Border.all(color: Color(0xFFca4e5b), width: 1.5),
                  ),
                  // _showLaunchCoin = false — hidden until Phase 2
                  child: InkWell(
                    borderRadius: BorderRadius.circular(50),
                    onTap: () {
                      // disabled until Phase 2
                    },
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Image.asset(
                          "assets/images/si_rocket-fill.png",
                          height: 22,
                          width: 22,
                        ),
                        const SizedBox(height: 2),
                        const Text(
                          "Launch",
                          style: TextStyle(
                            color: Colors.white70,
                            fontSize: 11,
                            fontFamily: "BernardMTCondensed",
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              /// Bottom Icons
              ValueListenableBuilder<int>(
                valueListenable: selectedPageNotifier,
                builder: (context, selectedIndex, _) {
                  return Positioned(
                    bottom: 10,
                    left: 0,
                    right: 0,
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                      children: [
                        _navIcon(
                          index: 0,
                          selectedIndex: selectedIndex,
                          iconPath: 'assets/images/home.png',
                          label: "Home",
                        ),
                        _navIcon(
                          index: 1,
                          selectedIndex: selectedIndex,
                          iconPath: 'assets/images/Walleticon.svg',
                          label: "Wallet",
                        ),
                        SizedBox(width: mqSize.width * 0.20),
                        _navIcon(
                          index: 2,
                          selectedIndex: selectedIndex,
                          iconPath: 'assets/images/Rewardicon.svg',
                          label: "Reward",
                        ),
                        _navIcon(
                          index: 3,
                          selectedIndex: selectedIndex,
                          iconPath: 'assets/images/profileicon.svg',
                          label: "Profile",
                        ),
                      ],
                    ),
                  );
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _navIcon({
    required int index,
    required int selectedIndex,
    required String iconPath,
    required String label,
  }) {
    final bool isSelected = index == selectedIndex;
    final Color iconColor = isSelected ? Colors.white : Colors.white54;

    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        IconButton(
          onPressed: () {
            selectedPageNotifier.value = index;
          },
          icon: _buildIcon(iconPath, iconColor),
        ),
        Text(
          label,
          style: TextStyle(
            fontSize: 10,
            fontFamily: "BernardMTCondensed",
            color: isSelected ? Colors.white : Colors.white54,
          ),
        ),
      ],
    );
  }

  /// SVG -> SvgPicture
  /// PNG/JPG -> Image.asset
  Widget _buildIcon(String iconPath, Color color) {
    final String lower = iconPath.toLowerCase();

    if (lower.endsWith('.svg')) {
      return SvgPicture.asset(
        iconPath,
        width: 28,
        height: 28,
        colorFilter: ColorFilter.mode(color, BlendMode.srcIn),
      );
    }

    return Image.asset(
      iconPath,
      width: 28,
      height: 28,
      color: color,
      colorBlendMode: BlendMode.srcIn,
      fit: BoxFit.contain,
    );
  }
}

class BNBCustomPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    /// Background Gradient
    Paint paint = Paint()
      ..shader = const LinearGradient(
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
        colors: [Color(0xFFA21117), Color(0xFF251216)],
      ).createShader(Rect.fromLTWH(0, 0, size.width, size.height))
      ..style = PaintingStyle.fill;

    /// Border Paint
    Paint borderPaint = Paint()
      ..color = const Color(0xFFCA4E5B)
      ..strokeWidth = 1.5
      ..style = PaintingStyle.stroke;

    Path path = Path();
    path.moveTo(0, 20);

    /// Left curve
    path.quadraticBezierTo(size.width * 0.18, 0, size.width * 0.30, 0);
    path.quadraticBezierTo(size.width * 0.35, 0, size.width * 0.38, 35);

    /// Center dip
    path.cubicTo(
      size.width * 0.38,
      35,
      size.width * 0.42,
      70,
      size.width * 0.50,
      68,
    );

    path.cubicTo(
      size.width * 0.58,
      70,
      size.width * 0.62,
      35,
      size.width * 0.64,
      20,
    );

    /// Right curve
    path.quadraticBezierTo(size.width * 0.67, 0, size.width * 0.72, 0);
    path.quadraticBezierTo(size.width * 0.82, 0, size.width, 20);

    path.lineTo(size.width, size.height);
    path.lineTo(0, size.height);
    path.close();

    /// Draw background
    canvas.drawPath(path, paint);

    /// Draw border
    canvas.drawPath(path, borderPaint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
