<?php
/**
 * Strona główna - publiczna lista dostępnych zajęć
 */
session_start();
require_once __DIR__ . '/../src/models/Zajecia.php';

$zajecia = Zajecia::pobierzDostepne();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klub Seniora - Dostępne Zajęcia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Witaj w Klubie Seniora
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        Odkryj różnorodne zajęcia dostosowane do Twoich potrzeb. 
                        Joga, taniec, gimnastyka i wiele więcej!
                    </p>
                </div>
                <div class="col-lg-6 d-none d-lg-block text-center">
                    <i class="bi bi-people" style="font-size: 200px; color: rgba(255,255,255,0.3);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Zajęcia Section -->
    <div class="container my-5">
        <div class="row mb-4">
            <div class="col">
                <h2 class="text-center mb-4">
                    <i class="bi bi-calendar-event"></i> Nadchodzące Zajęcia
                </h2>
            </div>
        </div>

        <div class="row g-4" id="zajecia-list">
            <?php if (empty($zajecia)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> Brak dostępnych zajęć w najbliższym czasie.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($zajecia as $z): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card zajecia-card h-100 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-star-fill"></i> <?= htmlspecialchars($z['nazwa']) ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text text-muted"><?= htmlspecialchars($z['opis']) ?></p>
                                
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-calendar3 text-primary"></i>
                                        <strong>Data:</strong> <?= date('d.m.Y', strtotime($z['data'])) ?>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-clock text-primary"></i>
                                        <strong>Godziny:</strong> <?= $z['godzina_od'] ?> - <?= $z['godzina_do'] ?>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-person-badge text-primary"></i>
                                        <strong>Instruktor:</strong> <?= htmlspecialchars($z['instruktor_imie'] . ' ' . $z['instruktor_nazwisko']) ?>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-door-open text-primary"></i>
                                        <strong>Sala:</strong> <?= htmlspecialchars($z['sala_nazwa']) ?>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-people text-primary"></i>
                                        <strong>Wolne miejsca:</strong> 
                                        <span class="badge bg-success"><?= $z['wolne_miejsca'] ?> / <?= $z['limit_miejsc'] ?></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer bg-light">
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_rola'] === 'senior'): ?>
                                    <button class="btn btn-primary w-100 btn-zapisz" data-zajecia-id="<?= $z['id'] ?>">
                                        <i class="bi bi-check-circle"></i> Zapisz się
                                    </button>
                                <?php elseif (!isset($_SESSION['user_id'])): ?>
                                    <a href="login.php" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-box-arrow-in-right"></i> Zaloguj się aby zapisać
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>
                                        Tylko dla seniorów
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Toast notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="notification-toast" class="toast" role="alert">
            <div class="toast-header">
                <strong class="me-auto">Powiadomienie</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
