import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';
import 'package:url_launcher/url_launcher.dart';

class PrivacyPolicyScreen extends StatelessWidget {
  const PrivacyPolicyScreen({super.key});

  static const Color _yellow = Color(0xFFFFE600);
  static const Color _red = Color(0xFFDB2519);

  Future<void> _openUrl(String url) async {
    final uri = Uri.parse(url);
    if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
      throw 'Could not launch $url';
    }
  }

  @override
  Widget build(BuildContext context) {
    final Size mqSize = MediaQuery.of(context).size;

    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        backgroundColor: Colors.transparent,
        toolbarHeight: 70,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        title: Padding(
          padding: const EdgeInsets.only(top: 30.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              InkWell(
                onTap: () => Navigator.pop(context),
                borderRadius: BorderRadius.circular(50),
                child: Container(
                  height: 40,
                  width: 40,
                  decoration: BoxDecoration(
                    color: _red.withOpacity(0.2),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.arrow_back_ios_new,
                    color: Colors.white,
                    size: 18,
                  ),
                ),
              ),
              Image.asset(
                'assets/images/moon_launch_logo.png',
                width: mqSize.width * 0.3,
              ),
            ],
          ),
        ),
      ),
      body: AppBackground(
        child: SafeArea(
          child: Padding(
            padding: EdgeInsets.symmetric(
              horizontal: mqSize.width * 0.05,
              vertical: mqSize.height * 0.02,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                /// 🔹 TITLE
                Text(
                  'Legal & Privacy Policy',
                  style: TextStyle(
                    fontFamily: 'BernardMTCondensed',
                    fontSize: mqSize.width * 0.08,
                    color: Colors.white,
                  ),
                ),

                SizedBox(height: mqSize.height * 0.03),

                /// 🔹 BUTTONS LIST
                _policyTile(
                  context: context,
                  icon: Icons.description_outlined,
                  title: 'Terms of Use',
                  onTap: () => _openUrl(
                    'https://moonlaunchapp.com/terms-of-use/',
                  ),
                ),

                _policyTile(
                  context: context,
                  icon: Icons.lock_outline,
                  title: 'Privacy Policy',
                  onTap: () => _openUrl(
                    'https://moonlaunchapp.com/privacy-policy/',
                  ),
                ),

                _policyTile(
                  context: context,
                  icon: Icons.verified_user_outlined,
                  title: 'Acceptable Use Policy',
                  onTap: () => _openUrl(
                    'https://moonlaunchapp.com/acceptable-use-policy/',
                  ),
                ),

                _policyTile(
                  context: context,
                  // ✅ Better icon for "Do Not Share"
                  icon: Icons.privacy_tip_outlined,
                  title: 'Terms of Do Not Share',
                  onTap: () => _openUrl(
                    'https://moonlaunchapp.com/terms-of-do-not-share-or-sell/',
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  /// 🔹 SINGLE POLICY TILE
  Widget _policyTile({
    required BuildContext context,
    required IconData icon,
    required String title,
    required VoidCallback onTap,
  }) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.05),
            borderRadius: BorderRadius.circular(14),
          ),
          child: Row(
            children: [
              _gradientIcon(icon),
              const SizedBox(width: 14),
              Expanded(
                child: Text(
                  title,
                  style: const TextStyle(
                    fontFamily: 'Benne',
                    fontSize: 16,
                    color: Colors.white,
                  ),
                ),
              ),
              const Icon(
                Icons.arrow_forward_ios_rounded,
                size: 16,
                color: Colors.white70,
              ),
            ],
          ),
        ),
      ),
    );
  }

  /// ✅ Gradient icon using app theme colors
  Widget _gradientIcon(IconData icon) {
    return ShaderMask(
      shaderCallback: (bounds) {
        return const LinearGradient(
          colors: [_yellow, _red],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ).createShader(Rect.fromLTWH(0, 0, bounds.width, bounds.height));
      },
      child: Icon(icon, color: Colors.white, size: 26),
    );
  }
}
