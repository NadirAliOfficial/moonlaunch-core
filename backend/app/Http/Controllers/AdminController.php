<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Mail\EmailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminOtpMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
   public function showLoginForm()
    {
        return view('business.auth');
    }
    public function index()
    {
        return view('business.dashboard');
    }

    /**
     * Handle login submission (Step 1: Validate credentials & send OTP)                              
     */
    public function submitLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Find admin user in roles table
        $admin = DB::table('roles')
            ->where('email', $request->email)
            ->first();

        // Validate credentials: Direct comparison (No Hashing)
        if (!$admin || $request->password !== $admin->password) {
            return back()->with('error', 'Invalid email or password.');
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in database with expiry (10 minutes)                                                  
        DB::table('roles')
            ->where('roles_id', $admin->roles_id)
            ->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

        // Store admin ID in session for OTP verification                                                   
        session([
            'admin_id_pending' => $admin->roles_id,
            'admin_email' => $admin->email,
        ]);

        // Send OTP email using Mail class
        try {
            Mail::to($admin->email)->send(new AdminOtpMail($otp, $admin->name ?? 'Admin'));
            
            return redirect()->route('admin.login')
                ->with('otp_sent', true)
                ->with('success', 'OTP has been sent to your email address.');
        } catch (\Exception $e) {
            // Log the error message and details
            Log::error('Failed to send OTP email', [
                'exception' => $e->getMessage(),
                'admin_email' => $admin->email,
                'otp' => $otp
            ]);
            
            return back()->with('error', 'Failed to send OTP. Please try again.');
        }
    }

    /**
     * Handle OTP verification (Step 2: Verify OTP & login)
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        // Get pending admin ID from session
        $adminId = session('admin_id_pending');
        
        if (!$adminId) {
            return redirect()->route('admin.login')
                ->with('error', 'Session expired. Please login again.');
        }

        // Find admin and verify OTP                               
        $admin = DB::table('roles')
            ->where('roles_id', $adminId)
            ->first();

        if (!$admin) {
            return back()->with('otp_error', 'Invalid session. Please login again.');
        }

        // Check if OTP is expired
        if (Carbon::parse($admin->otp_expires_at)->isPast()) {
            return back()->with('otp_error', 'OTP has expired. Please request a new one.');
        }

        // Verify OTP
        if ($request->otp !== $admin->otp) {
            return back()->with('otp_error', 'Invalid OTP. Please try again.');
        }

        // Clear OTP from database
        DB::table('roles')
            ->where('roles_id', $admin->roles_id)
            ->update([
                'otp' => null,
                'otp_expires_at' => null,
                'last_login_at' => Carbon::now(),
            ]);

        // Log the admin in
        session([
            'admin_id' => $admin->roles_id ,
            'admin_email' => $admin->email,
            'admin_role' => $admin->role_type,
            'admin_name' => $admin->name ?? 'Admin',
        ]);

        // Clear pending session data
        session()->forget(['admin_id_pending']);

        // Log audit trail
        $this->logAudit($admin->roles_id, 'admin_login', 'Admin logged in successfully');

        return redirect()->route('admin.dashboard')
            ->with('success', 'Welcome back, ' . ($admin->name ?? 'Admin') . '!');
    }

    /**
     * Resend OTP                     
     */
    public function resendOTP()
    {
        $adminId = session('admin_id_pending');
        
        if (!$adminId) {
            return redirect()->route('admin.login')
                ->with('error', 'Session expired. Please login again.');
        }

        $admin = DB::table('roles')
            ->where('roles_id', $adminId)
            ->first();

        if (!$admin) {
            return redirect()->route('admin.login')
                ->with('error', 'Invalid session. Please login again.');
        }

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update OTP in database
        DB::table('roles')
            ->where('roles_id', $admin->id)
            ->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
                'updated_at' => Carbon::now(),
            ]);

        // Resend OTP email using Mail class                               
        try {
            Mail::to($admin->email)->send(new AdminOtpMail($otp, $admin->name ?? 'Admin'));
            
            return redirect()->route('admin.login')
                ->with('otp_sent', true)
                ->with('success', 'A new OTP has been sent to your email.');
        } catch (\Exception $e) {
            return back()->with('otp_error', 'Failed to resend OTP. Please try again.');
        }
    }

    //  FORGOT PASSWORD                                                                              
    public function forget_sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        // Check if email exists in roles table
        $admin = DB::table('roles')->where('email', $email)->first();

        if (!$admin) {
            return back()->with('forgot_error', 'Email not found in our records.');
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP in database with expiry time (10 minutes)
        DB::table('roles')
            ->where('email', $email)
            ->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10)
            ]);

        // Send OTP email                                         
        try {
            Mail::to($email)->send(new AdminOtpMail($otp));
        } catch (\Exception $e) {
            return back()->with('forgot_error', 'Failed to send OTP. Please try again.');
        }

        // Store email in session for verification
        session(['forgot_password_email' => $email, 'forgot_otp_sent' => true]);

        return back()->with('forgot_success', 'OTP has been sent to your email.');
    }

    /**
     * Verify OTP for password reset                                     
     */
    public function forget_verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('forgot_password_email');

        if (!$email) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Please try again.');
        }

        // Get admin record                                        
        $admin = DB::table('roles')->where('email', $email)->first();

        if (!$admin) {
            return back()->with('forgot_otp_error', 'Email not found.');
        }

        // Check if OTP matches
        if ($admin->otp != $request->otp) {
            return back()->with('forgot_otp_error', 'Invalid OTP. Please try again.');
        }

        // Check if OTP has expired                                     
        if (now()->gt($admin->otp_expires_at)) {
            return back()->with('forgot_otp_error', 'OTP has expired. Please request a new one.');
        }

        // OTP is valid, move to reset password step
        session(['reset_password' => true, 'forgot_otp_sent' => false]);

        return back();
    }

    /**
     * Resend OTP for password reset                                       
     */
    public function forget_resendOtp(Request $request)
    {
        $email = session('forgot_password_email');

        if (!$email) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Please try again.');
        }

        // Generate new 6-digit OTP
        $otp = rand(100000, 999999);

        // Update OTP in database with new expiry time
        DB::table('roles')
            ->where('email', $email)
            ->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10)
            ]);

        // Send OTP email
        try {
            Mail::to($email)->send(new AdminOtpMail($otp));
        } catch (\Exception $e) {
            return back()->with('forgot_otp_error', 'Failed to resend OTP. Please try again.');
        }

        return back()->with('forgot_success', 'OTP has been resent to your email.');
    }

    /**
     * Reset password
     */
    public function forget_resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        $email = session('forgot_password_email');

        if (!$email || !session('reset_password')) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Please try again.');
        }

        // Update password (not hashing as per your requirement)
        DB::table('roles')
            ->where('email', $email)
            ->update([
                'password' => $request->password,
                'otp' => null,
                'otp_expires_at' => null
            ]);

        // Clear all forgot password sessions
        session()->forget(['forgot_password_email', 'forgot_otp_sent', 'reset_password']);

        return redirect()->route('admin.login')->with('password_reset_success', 'Password reset successfully. You can now login with your new password.');
    }
    //  FORGOT PASSWORD 

    /**
     * Logout admin
     */
    public function logout()
    {
        $adminId = session('admin_id');
        
        if ($adminId) {
            $this->logAudit($adminId, 'admin_logout', 'Admin logged out');
        }

        session()->flush();
        
        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out successfully.');
    }
    //  Get User 
        public function get_user()
    {
        // Get all users from users table
        $users = DB::table('users')
            ->select('id', 'name', 'email', 'profile_pick', 'created_at','status')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('business.user', compact('users'));
    }

    /**
     * Search users by name or email
     */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $users = DB::table('users')
            ->select('id', 'name', 'email', 'profile_pick', 'created_at')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('business.user', compact('users', 'search'));
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {
        try {
            // Find user
            $user = DB::table('users')->where('id', $id)->first();

            if (!$user) {
                return back()->with('error', 'User not found.');
            }

            // Delete user
            DB::table('users')->where('id', $id)->delete();

            // Log audit trail
            $this->logAudit(
                session('admin_id'),
                'user_deleted',
                "Deleted user: {$user->name} (ID: {$id}, Email: {$user->email})"
            );

            return back()->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user. Please try again.');
        }
    }
    public function blockUser($id)
    {
        try {
            $user = DB::table('users')->where('id', $id)->first();

            if (!$user) {
                return back()->with('error', 'User not found.');
            }

            // Update user status to Blocked
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'status' => 'Blocked',
                    'updated_at' => now()
                ]);

            return back()->with('success', 'User "' . $user->name . '" has been blocked successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to block user. Please try again.');
        }
    }

    /**
     * Unblock a user
     */
    public function unblockUser($id)
    {
        try {
            $user = DB::table('users')->where('id', $id)->first();

            if (!$user) {
                return back()->with('error', 'User not found.');
            }

            // Update user status to Active
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'status' => 'Active',
                    'updated_at' => now()
                ]);

            return back()->with('success', 'User "' . $user->name . '" has been unblocked successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to unblock user. Please try again.');
        }
    }

    /**
     * Reset user password
     */
    public function resetUserPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        try {
            $user = DB::table('users')->where('id', $id)->first();

            if (!$user) {
                return back()->with('error', 'User not found.');
            }

            // Update password with hashing
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'password' => Hash::make($request->password),
                    'updated_at' => now()
                ]);

            return back()->with('success', 'Password for "' . $user->name . '" has been reset successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reset password. Please try again.');
        }
    }
    public function audit_log()
    {
        // Get audit logs with admin details
        $logs = DB::table('audit_logs')
            ->join('roles', 'audit_logs.admin_id', '=', 'roles.roles_id')
            ->select(
                'audit_logs.*',
                'roles.name as admin_name',
                'roles.email as admin_email',
                'roles.role_type as admin_role'
            )
            ->orderBy('audit_logs.created_at', 'desc')
            ->paginate(50); // 50 logs per page

        return view('business.audit-log', compact('logs'));
    }

    /**
     * Search audit logs
     */
    public function audit_search(Request $request)
    {
        $search = $request->input('search');
        $action = $request->input('action');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $query = DB::table('audit_logs')
            ->join('roles', 'audit_logs.admin_id', '=', 'roles.roles_id')
            ->select(
                'audit_logs.*',
                'roles.name as admin_name',
                'roles.email as admin_email',
                'roles.role_type as admin_role'
            );

        // Search by description or admin name
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('audit_logs.description', 'like', "%{$search}%")
                ->orWhere('roles.name', 'like', "%{$search}%")
                ->orWhere('roles.email', 'like', "%{$search}%");
            });
        }

        // Filter by action
        if ($action) {
            $query->where('audit_logs.action', $action);
        }

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('audit_logs.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('audit_logs.created_at', '<=', $dateTo);
        }

        $logs = $query->orderBy('audit_logs.created_at', 'desc')->paginate(50);

        return view('business.audit-log', compact('logs', 'search', 'action', 'dateFrom', 'dateTo'));
    }

    /**
     * Export audit logs to CSV
     */
    public function export()
    {
        $logs = DB::table('audit_logs')
            ->join('roles', 'audit_logs.admin_id', '=', 'roles.roles_id')
            ->select(
                'audit_logs.id',
                'roles.name as admin_name',
                'roles.email as admin_email',
                'roles.role_type as admin_role',
                'audit_logs.action',
                'audit_logs.description',
                'audit_logs.ip_address',
                'audit_logs.created_at'
            )
            ->orderBy('audit_logs.created_at', 'desc')
            ->get();

        $filename = 'audit_logs_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['ID', 'Admin Name', 'Admin Email', 'Role', 'Action', 'Description', 'IP Address', 'Date & Time']);
            
            // Data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->admin_name,
                    $log->admin_email,
                    $log->admin_role,
                    $log->action,
                    $log->description,
                    $log->ip_address,
                    $log->created_at,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Log admin actions to audit trail
     */
    public function get_roles()
    {
        $roles = DB::table('roles')
            ->orderBy('roles_id', 'desc')
            ->paginate(10);

        $activeCount = DB::table('roles')->where('status', 'Active')->count();
        $inactiveCount = DB::table('roles')->where('status', 'Inactive')->count();

        return view('business.roles', compact('roles', 'activeCount', 'inactiveCount'));
    }

    /**
    * Search roles.
    */
    public function role_search(Request $request)
    {
        $search = $request->input('search');

        $roles = DB::table('roles')
            ->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('role_type', 'LIKE', "%{$search}%");
            })
            ->orderBy('roles_id', 'desc')
            ->paginate(10);

        $activeCount = DB::table('roles')->where('status', 'Active')->count();
        $inactiveCount = DB::table('roles')->where('status', 'Inactive')->count();

        return view('admin.roles', compact('roles', 'search', 'activeCount', 'inactiveCount'));
    }

    /**
    * Store a newly created role in database.
    */
    public function role_store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:roles,email',
            'role_type' => 'required|in:Owner,Admin,Ops,Finance,Support',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:Active,Inactive',
        ]);

        try {
            // Insert role
            DB::table('roles')->insert([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role_type' => $validated['role_type'],
                'password' => $validated['password'],
                'status' => $validated['status'],
                'otp' => null,
                'otp_expires_at' => null,
                'last_login_at' => null,
            ]);

            return redirect()->route('admin.roles')
                ->with('success', 'Role created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create role. Please try again.');
        }
    }

    /**
    * Update the specified role in database.
    */
    public function role_update(Request $request, $id)
    {
        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('roles', 'email')->ignore($id, 'roles_id')
            ],
            'role_type' => 'required|in:Owner,Admin,Ops,Finance,Support',
            'status' => 'required|in:Active,Inactive',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            // Prepare update data
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role_type' => $validated['role_type'],
                'status' => $validated['status'],
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            // Update role
            DB::table('roles')
                ->where('roles_id', $id)
                ->update($updateData);

            return redirect()->route('admin.roles')
                ->with('success', 'Role updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update role. Please try again.');
        }
    }

    /**
    * Remove the specified role from database.
    */
    public function role_destroy($id)
    {
        try {
            // Check if role exists
            $role = DB::table('roles')->where('roles_id', $id)->first();

            if (!$role) {
                return redirect()->route('admin.roles')
                    ->with('error', 'Role not found.');
            }

            // Delete role
            DB::table('roles')->where('roles_id', $id)->delete();

            return redirect()->route('admin.roles')
                ->with('success', 'Role deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete role. Please try again.');
        }
    }
    private function logAudit($adminId, $action, $description)
    {
        DB::table('audit_logs')->insert([
            'admin_id' => $adminId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => Carbon::now(),
        ]);
    }
}
