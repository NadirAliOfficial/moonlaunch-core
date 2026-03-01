import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:moon_launch/Back-end/Sessions/session_controller.dart';
import 'package:moon_launch/Front-end/auth_screens/login_screen.dart';
import 'package:moon_launch/Front-end/views/edit_profile_screen.dart';
import 'package:moon_launch/Front-end/views/export_key.dart';
import 'package:moon_launch/Front-end/views/privacy_policy_screen.dart';
import 'package:moon_launch/Front-end/views/push_notification_screen.dart';
import 'package:moon_launch/Front-end/widgets/confirm_dialog.dart';
import 'package:moon_launch/Front-end/widgets/notifiers.dart';
import 'package:moon_launch/Front-end/widgets/profile_background.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  bool startNotification = true;
  double _selectedRating = 0;

  String _name = "User";
  String _email = "-";
  String _imgBase64 = "";

  @override
  void initState() {
    super.initState();
    _loadSessionUser();
  }

  Future<void> _loadSessionUser() async {
    await SessionController.instance.init();
    if (!mounted) return;

    setState(() {
      _name = SessionController.instance.name.isNotEmpty
          ? SessionController.instance.name
          : "User";

      _email = SessionController.instance.email.isNotEmpty
          ? SessionController.instance.email
          : "-";

      _imgBase64 = SessionController.instance.profileImageBase64;
    });
  }

  @override
  Widget build(BuildContext context) {
    final Size mqSize = MediaQuery.of(context).size;

    final double bottomSafe = MediaQuery.of(context).padding.bottom;
    final double bottomSpace = kBottomNavigationBarHeight + bottomSafe;

    return Scaffold(
      extendBodyBehindAppBar: true,
      extendBody: true,
      resizeToAvoidBottomInset: true,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        elevation: 0,
        backgroundColor: Colors.transparent,
        surfaceTintColor: Colors.transparent,
        titleSpacing: mqSize.width * 0.045,
        actions: [
          Padding(
            padding: EdgeInsets.only(
              right: mqSize.width * 0.045,
              top: mqSize.height * 0.02,
            ),
            child: Align(
              alignment: Alignment.center,
              child: Image.asset(
                'assets/images/moon_launch_logo.png',
                width: 104,
                height: 31,
                fit: BoxFit.contain,
              ),
            ),
          ),
        ],
      ),
      body: ProfileBackground(
        child: SafeArea(
          child: LayoutBuilder(
            builder: (context, constraints) {
              return SingleChildScrollView(
                physics: const ClampingScrollPhysics(),
                padding: EdgeInsets.only(
                  left: 10,
                  right: 10,
                  top: 10,
                  bottom: bottomSpace,
                ),
                child: ConstrainedBox(
                  constraints: BoxConstraints(
                    minHeight: constraints.maxHeight - bottomSpace,
                  ),
                  child: Column(
                    children: [
                      Column(
                        children: [
                          Container(
                            width: mqSize.height * 0.13,
                            height: mqSize.height * 0.13,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(100),
                            ),
                            child: Padding(
                              padding: const EdgeInsets.all(2),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(98),
                                child: _buildProfileImage(),
                              ),
                            ),
                          ),
                          const SizedBox(height: 10),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Text(
                                _name,
                                style: TextStyle(
                                  fontFamily: 'BernardMTCondensed',
                                  fontWeight: FontWeight.w400,
                                  fontSize: mqSize.width * 0.045,
                                  color: const Color(0xFFC9C9C9),
                                ),
                              ),
                              const SizedBox(width: 5),
                              Image.asset(
                                "assets/images/tick.png",
                                height: 34,
                                width: 34,
                              ),
                            ],
                          ),
                          const SizedBox(height: 6),
                          Text(
                            _email,
                            style: TextStyle(
                              fontFamily: 'Benne',
                              fontWeight: FontWeight.w400,
                              fontSize: mqSize.width * 0.045,
                              color: const Color(0xFFC9C9C9),
                            ),
                          ),
                        ],
                      ),

                      SizedBox(height: mqSize.height * 0.02),

                      Column(
                        children: [
                          Container(
                            height: mqSize.height * 0.18,
                            width: mqSize.height * 0.4,
                            decoration: BoxDecoration(
                              color: Colors.transparent,
                              border: Border.all(
                                color: const Color(0xffDB2519),
                                width: 1,
                              ),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Padding(
                              padding: const EdgeInsets.only(
                                top: 12.0,
                                left: 15,
                                right: 15,
                              ),
                              child: Column(
                                children: [
                                  const Align(
                                    alignment: Alignment.centerLeft,
                                    child: Text(
                                      "Wallet Address",
                                      style: TextStyle(
                                        fontWeight: FontWeight.w900,
                                        color: Colors.white,
                                        fontSize: 16.5,
                                        fontFamily: "Benne",
                                      ),
                                    ),
                                  ),
                                  const SizedBox(height: 3),
                                  const Align(
                                    alignment: Alignment.centerLeft,
                                    child: Text(
                                      "0x21c086a2fd88a055f829989sdgjfgsakl35kgh546gvb3j5b345vvj",
                                      style: TextStyle(
                                        fontFamily: "Benne",
                                        fontSize: 12.5,
                                        fontWeight: FontWeight.w800,
                                        color: Colors.white,
                                      ),
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Align(
                                    alignment: Alignment.centerLeft,
                                    child: Container(
                                      height: 35,
                                      width: mqSize.height * 0.12,
                                      decoration: BoxDecoration(
                                        gradient: const LinearGradient(
                                          colors: [
                                            Color(0xffFFE600),
                                            Color(0xffDB2519),
                                          ],
                                          begin: Alignment.centerLeft,
                                          end: Alignment.centerRight,
                                        ),
                                        borderRadius: BorderRadius.circular(9),
                                      ),
                                      child: const Align(
                                        alignment: Alignment.center,
                                        child: Text(
                                          "Copy Address",
                                          style: TextStyle(
                                            fontWeight: FontWeight.w800,
                                            fontSize: 11,
                                            fontFamily: "BernardMTCondensed",
                                            color: Colors.white,
                                          ),
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),

                          SizedBox(height: mqSize.height * 0.04),

                          _menuItem(
                            mqSize: mqSize,
                            title: 'Notifications',
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (_) => PushNotificationScreen(),
                                ),
                              );
                            },
                          ),
                          SizedBox(height: mqSize.height * 0.04),

                          _menuItem(
                            mqSize: mqSize,
                            title: 'Edit Profile',
                            onTap: () async {
                              await Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (_) => const EditProfileScreen(),
                                ),
                              );
                              await _loadSessionUser();
                            },
                          ),
                          SizedBox(height: mqSize.height * 0.04),

                          _menuItem(
                            mqSize: mqSize,
                            title: 'Help Center',
                            onTap: () {},
                          ),
                          SizedBox(height: mqSize.height * 0.04),

                          _menuItem(
                            mqSize: mqSize,
                            title: 'Export Keys',
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (_) => ExportKey()),
                              );
                            },
                          ),
                          SizedBox(height: mqSize.height * 0.04),

                          _menuItem(
                            mqSize: mqSize,
                            title: 'Legal and Privacy Policy',
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (_) => PrivacyPolicyScreen(),
                                ),
                              );
                            },
                          ),
                          SizedBox(height: mqSize.height * 0.04),

                          InkWell(
                            onTap: () => _showRatingDialog(context),
                            child: Container(
                              padding:
                                  const EdgeInsets.symmetric(horizontal: 15),
                              child: Row(
                                children: [
                                  Text(
                                    'Rate MoonLaunch',
                                    style: TextStyle(
                                      fontFamily: 'Benne',
                                      fontWeight: FontWeight.w400,
                                      fontSize: mqSize.width * 0.045,
                                      color: Colors.white,
                                    ),
                                  ),
                                  const Spacer(),
                                  const Image(
                                    image: AssetImage(
                                        "assets/images/solar_star-outline.png"),
                                    height: 24,
                                    width: 24,
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ],
                      ),

                      SizedBox(height: mqSize.height * 0.04),

                      InkWell(
                        onTap: () {
                          AppDialogs.showConfirmDialog(
                            context: context,
                            title: 'Delete Account',
                            message:
                                'This action cannot be undone. Are you sure?',
                            confirmText: 'Delete',
                            onConfirm: () async {
                              await _logOut();
                            },
                          );
                        },
                        child: Padding(
                          padding: EdgeInsets.symmetric(
                              horizontal: mqSize.width * 0.05),
                          child: Container(
                            height: mqSize.height * 0.06,
                            width: double.infinity,
                            decoration: BoxDecoration(
                              border: Border.all(
                                  width: 2, color: const Color(0xFFDB2519)),
                              borderRadius: BorderRadius.circular(40),
                            ),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(
                                  Icons.delete,
                                  color: const Color(0xFFDB2519),
                                  size: mqSize.width * 0.07,
                                ),
                                const SizedBox(width: 10),
                                Text(
                                  'Delete Account',
                                  style: TextStyle(
                                    fontFamily: 'BernardMTCondensed',
                                    fontWeight: FontWeight.w400,
                                    color: const Color(0xFFDB2519),
                                    fontSize: mqSize.width * 0.05,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),

                      SizedBox(height: mqSize.height * 0.02),

                      InkWell(
                        onTap: () {
                          AppDialogs.showConfirmDialog(
                            context: context,
                            title: 'Logout',
                            message: 'Are you sure you want to logout?',
                            onConfirm: () async {
                              await _logOut();
                            },
                          );
                        },
                        child: Padding(
                          padding: EdgeInsets.symmetric(
                              horizontal: mqSize.width * 0.05),
                          child: Container(
                            height: mqSize.height * 0.07,
                            width: double.infinity,
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                colors: [
                                  Color(0xFFFFE600),
                                  Color(0xFFDB2519)
                                ],
                                begin: Alignment.centerLeft,
                                end: Alignment.centerRight,
                              ),
                              borderRadius: BorderRadius.circular(40),
                            ),
                            child: Center(
                              child: Text(
                                'Logout',
                                style: TextStyle(
                                  fontFamily: 'BernardMTCondensed',
                                  fontWeight: FontWeight.w400,
                                  color: Colors.white,
                                  fontSize: mqSize.width * 0.06,
                                ),
                              ),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        ),
      ),
    );
  }

  Widget _buildProfileImage() {
    if (_imgBase64.trim().isNotEmpty) {
      try {
        final bytes = base64Decode(_imgBase64);
        return Image.memory(bytes, fit: BoxFit.cover);
      } catch (_) {}
    }
    return const Image(
      image: AssetImage('assets/images/s2.png'),
      fit: BoxFit.cover,
    );
  }

  Widget _menuItem({
    required Size mqSize,
    required String title,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 15),
        child: Row(
          children: [
            Text(
              title,
              style: TextStyle(
                fontFamily: 'Benne',
                fontWeight: FontWeight.w400,
                fontSize: mqSize.width * 0.045,
                color: Colors.white,
              ),
            ),
            const Spacer(),
            Icon(
              Icons.arrow_forward_ios,
              size: mqSize.width * 0.045,
              color: Colors.white,
            ),
          ],
        ),
      ),
    );
  }

  void _showRatingDialog(BuildContext context) {
    final Size mqSize = MediaQuery.of(context).size;

    showDialog(
      context: context,
      barrierDismissible: true,
      builder: (ctx) {
        return StatefulBuilder(
          builder: (ctx, setStateDialog) {
            return Dialog(
              backgroundColor: Colors.transparent,
              insetPadding:
                  const EdgeInsets.symmetric(horizontal: 18, vertical: 24),
              child: Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 18, vertical: 18),
                decoration: BoxDecoration(
                  color: const Color(0xFF0C0C0C),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFDB2519), width: 1),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      "Rate MoonLaunch",
                      style: TextStyle(
                        fontFamily: "BernardMTCondensed",
                        fontSize: mqSize.width * 0.06,
                        color: Colors.white,
                        fontWeight: FontWeight.w400,
                      ),
                    ),
                    const SizedBox(height: 10),
                    Text(
                      "How was your experience?",
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontFamily: "Benne",
                        fontSize: mqSize.width * 0.04,
                        color: const Color(0xFFC9C9C9),
                        fontWeight: FontWeight.w400,
                      ),
                    ),
                    const SizedBox(height: 14),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: List.generate(5, (index) {
                        final starValue = index + 1;
                        final isSelected = starValue <= _selectedRating;
                        return InkWell(
                          onTap: () {
                            setStateDialog(() {
                              _selectedRating = starValue.toDouble();
                            });
                          },
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 4),
                            child: Icon(
                              isSelected ? Icons.star : Icons.star_border,
                              size: 34,
                              color: isSelected
                                  ? const Color(0xFFFFE600)
                                  : const Color(0xFFC9C9C9),
                            ),
                          ),
                        );
                      }),
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: InkWell(
                            onTap: () => Navigator.pop(ctx),
                            borderRadius: BorderRadius.circular(12),
                            child: Container(
                              height: 44,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                    color: const Color(0xFFDB2519),
                                    width: 1.5),
                              ),
                              child: const Center(
                                child: Text(
                                  "Cancel",
                                  style: TextStyle(
                                    fontFamily: "BernardMTCondensed",
                                    color: Color(0xFFDB2519),
                                    fontSize: 18,
                                    fontWeight: FontWeight.w400,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: InkWell(
                            onTap: () {
                              Navigator.pop(ctx);
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                  backgroundColor: const Color(0xFF0C0C0C),
                                  content: Text(
                                    _selectedRating == 0
                                        ? "Please select a rating"
                                        : "Thanks! You rated $_selectedRating ⭐",
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontFamily: "Benne",
                                    ),
                                  ),
                                ),
                              );
                            },
                            borderRadius: BorderRadius.circular(12),
                            child: Container(
                              height: 44,
                              decoration: BoxDecoration(
                                gradient: const LinearGradient(
                                  colors: [
                                    Color(0xFFFFE600),
                                    Color(0xFFDB2519)
                                  ],
                                  begin: Alignment.centerLeft,
                                  end: Alignment.centerRight,
                                ),
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: const Center(
                                child: Text(
                                  "Submit",
                                  style: TextStyle(
                                    fontFamily: "BernardMTCondensed",
                                    color: Colors.white,
                                    fontSize: 18,
                                    fontWeight: FontWeight.w400,
                                  ),
                                ),
                              ),
                            ),
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
      },
    );
  }

  /// ✅ FINAL LOGOUT FIX
  Future<void> _logOut() async {
    selectedPageNotifier.value = 0;

    await SessionController.instance.logout();

    if (!mounted) return;

    Navigator.pushAndRemoveUntil(
      context,
      MaterialPageRoute(builder: (_) => const LoginScreen()),
      (route) => false,
    );
  }
}
