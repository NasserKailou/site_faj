<?php
require_once '../includes/config.php';

// Si déjà connecté
if (isAdmin()) {
    redirect(SITE_URL . '/admin/dashboard');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND actif = 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['mot_de_passe'])) {
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nom'] = $admin['nom'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_login_time'] = time();
                
                // Mise à jour dernière connexion
                $pdo->prepare("UPDATE admins SET derniere_connexion = NOW() WHERE id = ?")->execute([$admin['id']]);
                
                redirect(SITE_URL . '/admin/dashboard');
            } else {
                $error = 'Email ou mot de passe incorrect.';
            }
        } catch (Exception $e) {
            $error = 'Erreur de connexion. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - FAJ Niger</title>
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/logo-faj.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1B2A4A 0%, #243564 50%, #1B2A4A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: rgba(232, 135, 10, 0.07);
            border-radius: 50%;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
            pointer-events: none;
        }
        .login-box {
            background: white;
            border-radius: 20px;
            padding: 50px 45px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .login-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin: 0 auto 16px;
            display: block;
        }
        .login-title {
            font-size: 22px;
            font-weight: 700;
            color: #1B2A4A;
            margin-bottom: 4px;
        }
        .login-subtitle {
            font-size: 13px;
            color: #6c757d;
        }
        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(232,135,10,0.1);
            color: #E8870A;
            border: 1px solid rgba(232,135,10,0.3);
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1B2A4A;
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            color: #212529;
            transition: border-color 0.3s;
            outline: none;
        }
        .form-control:focus {
            border-color: #E8870A;
            box-shadow: 0 0 0 4px rgba(232,135,10,0.1);
        }
        .input-with-icon {
            position: relative;
        }
        .input-with-icon i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 16px;
        }
        .input-with-icon .form-control {
            padding-left: 44px;
        }
        .toggle-pwd {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #adb5bd;
            font-size: 16px;
        }
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #E8870A, #c97008);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(232,135,10,0.4);
        }
        .error-msg {
            background: rgba(220,53,69,0.1);
            border: 1px solid rgba(220,53,69,0.3);
            color: #721c24;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link:hover { color: #E8870A; }
        .back-link i { margin-right: 6px; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-header">
            <span class="admin-badge"><i class="fas fa-shield-alt"></i> Espace Administration</span>
            <img src="<?= SITE_URL ?>/assets/images/logo-faj.png" alt="FAJ" class="login-logo">
            <h1 class="login-title">FAJ Niger</h1>
            <p class="login-subtitle">Connectez-vous à votre espace administrateur</p>
        </div>
        
        <?php if ($error): ?>
        <div class="error-msg">
            <i class="fas fa-exclamation-circle"></i>
            <?= sanitize($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope" style="color:#E8870A; margin-right:6px;"></i> Adresse Email</label>
                <div class="input-with-icon">
                    <i class="fas fa-at"></i>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="admin@faj.ne" required autocomplete="username"
                           value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock" style="color:#E8870A; margin-right:6px;"></i> Mot de Passe</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="toggle-pwd" onclick="togglePwd()">
                        <i class="fas fa-eye" id="pwdIcon"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Se Connecter
            </button>
        </form>
        
        <a href="<?= SITE_URL ?>/" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au site public
        </a>
    </div>
    
    <script>
    function togglePwd() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('pwdIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            pwd.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
    </script>
</body>
</html>
