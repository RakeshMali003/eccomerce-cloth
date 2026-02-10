<?php
session_start();
require_once __DIR__ . '/loader.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ... (previous code)

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ? AND status = 'active' LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['role'] = 'admin';
        header("Location: dashboard.php");
        exit();
    } else {
        // Fallback: Check for Worker/Staff in users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role IN ('worker', 'manager', 'staff') AND status = 'active' LIMIT 1");
        $stmt->execute([$email]);
        $worker = $stmt->fetch();

        if ($worker && password_verify($password, $worker['password'])) {
            $_SESSION['admin_id'] = $worker['user_id']; // Use user_id as admin_id for session compatibility
            $_SESSION['admin_name'] = $worker['name'];
            $_SESSION['role'] = 'worker';
            $_SESSION['worker_id'] = $worker['user_id'];

            // Get specific permissions if any
            $permStmt = $pdo->prepare("SELECT permissions FROM workers WHERE user_id = ?");
            $permStmt->execute([$worker['user_id']]);
            $wData = $permStmt->fetch();
            $_SESSION['permissions'] = $wData ? json_decode($wData['permissions'], true) : [];

            header("Location: dashboard.php");
            exit();
        }

        $error = "Access Denied: Invalid Credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Joshi Electricals Admin | Secure Gateway</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            overflow: hidden;
        }

        /* Ambient Background Glows */
        .glow {
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 111, 30, 0.15);
            filter: blur(100px);
            border-radius: 50%;
            z-index: -1;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-glass:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #ea580c;
            box-shadow: 0 0 20px rgba(234, 88, 12, 0.2);
            outline: none;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">

    <div class="glow top-[-10%] right-[-10%]"></div>
    <div class="glow bottom-[-10%] left-[-10%] animate-pulse"></div>

    <div class="w-full max-w-md relative z-10 animate-float">

        <div class="text-center mb-10">
            <h1 class="text-white text-5xl font-extrabold tracking-tighter uppercase italic">
                Joshi Electricals<span class="text-orange-600">.</span>
            </h1>
            <div class="flex items-center justify-center gap-2 mt-3">
                <div class="h-[1px] w-8 bg-orange-600/50"></div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.5em]">Central Command</p>
                <div class="h-[1px] w-8 bg-orange-600/50"></div>
            </div>
        </div>

        <div class="glass-panel rounded-[3rem] p-10 shadow-2xl">
            <div class="mb-8">
                <h2 class="text-white text-2xl font-bold">Admin Login</h2>
                <p class="text-slate-400 text-xs mt-1">Authorized Personnel Only</p>
            </div>

            <?php if ($error): ?>
                <div
                    class="mb-6 p-4 bg-red-500/10 text-red-400 rounded-2xl text-[10px] font-black uppercase text-center border border-red-500/20">
                    <i class="fas fa-shield-virus mr-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-widest">Master
                        Identifier</label>
                    <div class="relative">
                        <input type="email" name="email" required placeholder="admin@joshielectricals.com"
                            class="input-glass w-full rounded-2xl px-6 py-4 text-sm font-medium">
                        <i class="fas fa-fingerprint absolute right-6 top-1/2 -translate-y-1/2 text-slate-600"></i>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-widest">Access
                        Protocol</label>
                    <div class="relative">
                        <input type="password" name="password" id="adminPassword" required placeholder="••••••••"
                            class="input-glass w-full rounded-2xl px-6 py-4 text-sm font-medium">

                        <button type="button" onclick="togglePasswordVisibility()"
                            class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-600 hover:text-orange-500 transition-colors">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-orange-600 text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-[0.25em] shadow-lg shadow-orange-900/40 hover:bg-orange-500 hover:scale-[1.02] transition-all active:scale-95">
                        Initialize Session
                    </button>
                </div>
            </form>

            <div class="mt-10 pt-6 border-t border-white/5 text-center">
                <p class="text-[9px] text-slate-500 uppercase font-bold tracking-widest">
                    Encryption: AES-256 Bit Secure
                </p>
            </div>
        </div>

        <p class="text-center text-slate-600 text-[10px] mt-8 font-medium">
            &copy; 2025 Joshi Electricals Textiles. System Version 4.0.2
        </p>
    </div>



    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('adminPassword');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>

</html>