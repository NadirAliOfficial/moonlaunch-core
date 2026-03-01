import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:moon_launch/Back-end/Sessions/session_controller.dart';
import 'package:moon_launch/Front-end/widgets/app_background.dart';
import 'package:moon_launch/Front-end/widgets/image_selector.dart';
import 'package:moon_launch/Back-end/Services/edit_profile_service.dart';

class EditProfileScreen extends StatefulWidget {
  const EditProfileScreen({super.key});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  File? _profileImage;
  final ImagePicker _picker = ImagePicker();

  final TextEditingController _nameCtrl = TextEditingController();
  final TextEditingController _emailCtrl = TextEditingController();
  final TextEditingController _phoneCtrl = TextEditingController();

  bool _isLoading = false;

  String _sessionImageBase64 = "";

  @override
  void initState() {
    super.initState();
    _loadSessionData();
  }

  Future<void> _loadSessionData() async {
    await SessionController.instance.init();
    if (!mounted) return;

    setState(() {
      _nameCtrl.text =
          SessionController.instance.name.isNotEmpty ? SessionController.instance.name : "";
      _emailCtrl.text =
          SessionController.instance.email.isNotEmpty ? SessionController.instance.email : "";
      _phoneCtrl.text =
          SessionController.instance.phone.isNotEmpty ? SessionController.instance.phone : "";

      _sessionImageBase64 = SessionController.instance.profileImageBase64;
    });
  }

  Future<void> _pickImage(ImageSource source) async {
    final XFile? pickedFile = await _picker.pickImage(
      source: source,
      imageQuality: 70,
    );

    if (pickedFile != null) {
      setState(() {
        _profileImage = File(pickedFile.path);
      });
    }
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _emailCtrl.dispose();
    _phoneCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final Size mqSize = MediaQuery.of(context).size;

    return Scaffold(
      resizeToAvoidBottomInset: true,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        toolbarHeight: 65,
        automaticallyImplyLeading: false,
        backgroundColor: Colors.transparent,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        title: Padding(
          padding: const EdgeInsets.only(top: 25.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              InkWell(
                onTap: () => Navigator.pop(context),
                child: Container(
                  height: 38,
                  width: 38,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(50),
                    color: const Color(0xFFDB2519).withOpacity(0.2),
                  ),
                  child: const Padding(
                    padding: EdgeInsets.only(right: 3),
                    child: Icon(
                      Icons.arrow_back_ios_new,
                      color: Colors.white,
                      size: 24,
                    ),
                  ),
                ),
              ),
              Image.asset(
                'assets/images/moon_launch_logo.png',
                width: 104,
                height: 31,
              ),
            ],
          ),
        ),
      ),
      body: AppBackground(
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 20),
            child: SingleChildScrollView(
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.start,
                    children: [
                      Text(
                        'Edit Profile',
                        style: TextStyle(
                          fontFamily: 'BernardMTCondensed',
                          fontWeight: FontWeight.w400,
                          fontSize: mqSize.width * 0.07,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: mqSize.height * 0.07),

                  Stack(
                    children: [
                      Container(
                        width: mqSize.height * 0.15,
                        height: mqSize.height * 0.15,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(100),
                        ),
                        child: Padding(
                          padding: const EdgeInsets.all(2),
                          child: Container(
                            decoration: BoxDecoration(
                              color: Colors.black.withOpacity(0.7),
                              borderRadius: BorderRadius.circular(98),
                            ),
                            child: ClipOval(
                              child: _buildProfileImage(),
                            ),
                          ),
                        ),
                      ),
                      Positioned(
                        bottom: 0,
                        right: 10,
                        child: InkWell(
                          onTap: () {
                            ImageSelector.show(
                              context: context,
                              onImageSelected: (source) => _pickImage(source),
                            );
                          },
                          child: Container(
                            height: 30,
                            width: 30,
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                colors: [
                                  Color(0xFFFFE600),
                                  Color(0xFFDB2519),
                                ],
                              ),
                              borderRadius: BorderRadius.circular(50),
                            ),
                            child: const Icon(
                              Icons.camera_alt,
                              color: Colors.white,
                              size: 18,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),

                  SizedBox(height: mqSize.height * 0.05),

                  Padding(
                    padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.01),
                    child: TextField(
                      controller: _nameCtrl,
                      decoration: InputDecoration(
                        hint: const Text(
                          "Username",
                          style: TextStyle(fontFamily: 'Benne', fontSize: 15),
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                          borderSide: const BorderSide(color: Color(0xFFDB2519)),
                        ),
                      ),
                    ),
                  ),
                  SizedBox(height: mqSize.height * 0.01),

                  Padding(
                    padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.01),
                    child: TextField(
                      controller: _emailCtrl,
                      decoration: InputDecoration(
                        hint: const Text(
                          "Email Address",
                          style: TextStyle(fontFamily: 'Benne', fontSize: 15),
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                          borderSide: const BorderSide(color: Color(0xFFDB2519)),
                        ),
                      ),
                    ),
                  ),
                  SizedBox(height: mqSize.height * 0.01),

                  Padding(
                    padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.01),
                    child: TextField(
                      controller: _phoneCtrl,
                      decoration: InputDecoration(
                        hint: const Text(
                          "Phone Number (optional)",
                          style: TextStyle(fontFamily: 'Benne', fontSize: 15),
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(mqSize.width * 0.5),
                          borderSide: const BorderSide(color: Color(0xFFDB2519)),
                        ),
                      ),
                    ),
                  ),

                  SizedBox(height: mqSize.height * 0.05),

                  InkWell(
                    onTap: _isLoading ? null : _onUpdateProfile,
                    child: Padding(
                      padding: EdgeInsets.symmetric(horizontal: mqSize.width * 0.01),
                      child: Container(
                        height: mqSize.height * 0.07,
                        width: double.infinity,
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [Color(0xFFFFE600), Color(0xFFDB2519)],
                            begin: Alignment.centerLeft,
                            end: Alignment.centerRight,
                          ),
                          borderRadius: BorderRadius.circular(40),
                        ),
                        child: Center(
                          child: _isLoading
                              ? const SizedBox(
                                  width: 22,
                                  height: 22,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2.5,
                                    color: Colors.white,
                                  ),
                                )
                              : Text(
                                  'Update',
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
          ),
        ),
      ),
    );
  }

  Widget _buildProfileImage() {
    // If user picked new image
    if (_profileImage != null) {
      return Image.file(
        _profileImage!,
        width: double.infinity,
        height: double.infinity,
        fit: BoxFit.cover,
      );
    }

    // If session has base64 image
    if (_sessionImageBase64.trim().isNotEmpty) {
      try {
        final bytes = base64Decode(_sessionImageBase64);
        return Image.memory(
          bytes,
          width: double.infinity,
          height: double.infinity,
          fit: BoxFit.cover,
        );
      } catch (_) {
        // fall back to asset
      }
    }

    // default asset
    return const Image(
      image: AssetImage('assets/images/s2.png'),
      fit: BoxFit.cover,
    );
  }

  Future<void> _onUpdateProfile() async {
    final name = _nameCtrl.text.trim();
    final email = _emailCtrl.text.trim();
    final phone = _phoneCtrl.text.trim();

    if (name.isEmpty) {
      _showSnack("Please enter username");
      return;
    }

    await SessionController.instance.init();
    final userIdStr = SessionController.instance.id;
    final userId = int.tryParse(userIdStr);

    if (userId == null) {
      _showSnack("User ID not found. Please login again.");
      return;
    }

    setState(() => _isLoading = true);

    try {
      // base64 image: if new picked then encode, else use existing session image
      String imageBase64 = _sessionImageBase64;

      if (_profileImage != null) {
        final bytes = await _profileImage!.readAsBytes();
        imageBase64 = base64Encode(bytes);
      }

      // If still empty, send empty (backend may allow)
      final res = await EditProfileService.editProfile(
        userId: userId,
        name: name,
        imageBase64: imageBase64,
      );

      // Save locally so profile screen shows updated info instantly
      await SessionController.instance.updateProfileLocal(
        name: name,
        email: email,
        phone: phone,
        imageBase64: imageBase64,
      );

      if (!mounted) return;
      setState(() {
        _isLoading = false;
        _sessionImageBase64 = imageBase64;
        _profileImage = null; // optional: reset after save
      });

      _showSnack("Profile updated");
      Navigator.pop(context, res); // return response if you want
    } catch (e) {
      if (!mounted) return;
      setState(() => _isLoading = false);
      _showSnack(e.toString().replaceAll("Exception:", "").trim());
    }
  }

  void _showSnack(String msg) {
    ScaffoldMessenger.of(context).clearSnackBars();
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        behavior: SnackBarBehavior.floating,
        backgroundColor: Colors.black,
        content: Text(
          msg,
          style: const TextStyle(
            color: Colors.white,
            fontFamily: "Benne",
          ),
        ),
      ),
    );
  }
}
