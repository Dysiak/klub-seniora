<?php
/**
 * Strona logowania
 */
session_start();

// Jeśli już zalogowany, przekieruj do dashboardu
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../src/models/User.php';
    
    $login = $_POST['login'] ?? '';
    $haslo = $_POST['haslo'] ?? '';
    
    if (empty($login) || empty($haslo)) {
        $error = 'Wypełnij wszystkie pola';
    } else {
        $wynik = User::loguj($login, $haslo);
        
        if ($wynik['sukces']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $wynik['komunikat'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Klub Seniora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card login-card">
                        <div class="login-header">
                            <h2 class="text-center mb-0">
                                <i class="bi bi-house-heart text-primary"></i>
                            </h2>
                            <h3 class="text-center mt-2">Klub Seniora</h3>
                            <p class="text-muted text-center mb-0">Zaloguj się do systemu</p>
                        </div>
                        <div class="login-body bg-light">
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="login.php">
                                <div class="mb-3">
                                    <label for="login" class="form-label">
                                        <i class="bi bi-person"></i> Login
                                    </label>
                                    <input type="text" class="form-control" id="login" name="login" 
                                           placeholder="Wprowadź login" required autofocus>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="haslo" class="form-label">
                                        <i class="bi bi-lock"></i> Hasło
                                    </label>
                                    <input type="password" class="form-control" id="haslo" name="haslo" 
                                           placeholder="Wprowadź hasło" required>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-in-right"></i> Zaloguj się
                                    </button>
                                </div>
                            </form>
                            
                            <hr class="my-4">
                            
                            <div class="text-center">
                                <a href="index.php" class="text-decoration-none">
                                    <i class="bi bi-arrow-left"></i> Powrót do strony głównej
                                </a>
                            </div>
                            
                            <!-- Dane testowe -->
                            <div class="mt-4">
                                <div class="card bg-white border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted mb-3">
                                            <i class="bi bi-info-circle"></i> Dane testowe (hasło dla wszystkich: <code>haslo123</code>)
                                        </h6>
                                        <div class="row g-2 small">
                                            <div class="col-6">
                                                <strong>Senior:</strong> senior1
                                            </div>
                                            <div class="col-6">
                                                <strong>Instruktor:</strong> instruktor1
                                            </div>
                                            <div class="col-6">
                                                <strong>Koordynator:</strong> koordynator1
                                            </div>
                                            <div class="col-6">
                                                <strong>Admin:</strong> admin
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
