import 'package:flutter/material.dart';
import 'package:moon_launch/Front-end/views/home_screen.dart';
import 'package:moon_launch/Front-end/views/profile_screen.dart';
import 'package:moon_launch/Front-end/views/reward_screen.dart';
import 'package:moon_launch/Front-end/views/wallet_screen.dart';
import 'package:moon_launch/Front-end/widgets/custom_bottom_navbar.dart';
import 'notifiers.dart';

List<Widget> pages = [
  HomeScreen(),
  WalletScreen(),
  RewardScreen(),
  ProfileScreen(),
];

class WidgetTree extends StatefulWidget {
  const WidgetTree({super.key});

  @override
  State<WidgetTree> createState() => _WidgetTreeState();
}

class _WidgetTreeState extends State<WidgetTree> {
  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    precacheImage(const AssetImage('assets/images/reward_coin.png'), context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBody: true,
      resizeToAvoidBottomInset: false,

      body: SafeArea(
        bottom: true,
        child: ValueListenableBuilder(
          valueListenable: selectedPageNotifier,
          builder: (context, selectedPage, child) {
            return IndexedStack(
              index: selectedPage,
              children: pages,
            );
          },
        ),
      ),

      bottomNavigationBar: const CustomBottomNavBar(),
    );
  }
}
