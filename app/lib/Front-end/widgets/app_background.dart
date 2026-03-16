import 'package:flutter/material.dart';

class AppBackground extends StatelessWidget {
  final Widget child;

  const AppBackground({super.key, required this.child});

  @override
  Widget build(BuildContext context) {
    return Stack(
      fit: StackFit.expand,
      children: [
        // Image as background - fit completely to screen with no rounded corners
        ClipRRect(
          borderRadius: BorderRadius.zero,
          child: Container(color: const Color(0xFF14121f)),
        ),
        child, // Place the child widget (content) on top of the background
      ],
    );
  }
}
