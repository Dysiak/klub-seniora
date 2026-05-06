<?php
/**
 * Dashboard dla Seniora
 */
require_once __DIR__ . '/../../src/models/Senior.php';
require_once __DIR__ . '/../../src/models/Zajecia.php';

// Pobranie danych seniora
$senior = new Senior(['id' => $_SESSION['user_id']]);
$mojeRezerwacje = $senior->getMojeRezerwacje('aktywna');
$dostepneZajecia = Zajecia::pobierzDostepne();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Seniora - Klub Seniora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container my-4">
        <!-- Nagłówek -->
        <div class="row mb-4">
            <div class="col">
                <h2><i class="bi bi-speedometer2"></i> Panel Seniora</h2>
                <p class="text-muted">Witaj, <?= htmlspecialchars($_SESSION['user_login']) ?>!</p>
            </div>
        </div>

        <!-- Statystyki -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <h3><?= count($mojeRezerwacje) ?></h3>
                    <p><i class="bi bi-calendar-check"></i> Aktywne rezerwacje</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card success">
                    <h3><?= count($dostepneZajecia) ?></h3>
                    <p><i class="bi bi-calendar-plus"></i> Dostępne zajęcia</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card info">
                    <h3><?= count($senior->getMojeRezerwacje('zakonczona')) ?></h3>
                    <p><i class="bi bi-check-circle"></i> Ukończone zajęcia</p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="rezerwacje-tab" data-bs-toggle="tab" data-bs-target="#rezerwacje" type="button">
                    <i class="bi bi-calendar-check"></i> Moje Rezerwacje
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dostepne-tab" data-bs-toggle="tab" data-bs-target="#dostepne" type="button">
                    <i class="bi bi-calendar-plus"></i> Dostępne Zajęcia
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Moje Rezerwacje -->
            <div class="tab-pane fade show active" id="rezerwacje">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <i class="bi bi-list-check"></i> Moje Aktywne Rezerwacje
                    </div>
                    <div class="card-body">
                        <?php if (empty($mojeRezerwacje)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Nie masz jeszcze żadnych rezerwacji.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nazwa zajęć</th>
                                            <th>Data</th>
                                            <th>Godziny</th>
                                            <th>Sala</th>
                                            <th>Status</th>
                                            <th>Akcje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mojeRezerwacje as $r): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($r['nazwa']) ?></strong></td>
                                                <td><i class="bi bi-calendar3"></i> <?= date('d.m.Y', strtotime($r['data'])) ?></td>
                                                <td><i class="bi bi-clock"></i> <?= $r['godzina_od'] ?> - <?= $r['godzina_do'] ?></td>
                                                <td><i class="bi bi-door-open"></i> <?= htmlspecialchars($r['sala_nazwa']) ?></td>
                                                <td><span class="badge bg-success"><?= htmlspecialchars($r['status']) ?></span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger btn-anuluj" data-rezerwacja-id="<?= $r['id'] ?>">
                                                        <i class="bi bi-x-circle"></i> Anuluj
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Dostępne Zajęcia -->
            <div class="tab-pane fade" id="dostepne">
                <div class="row g-4">
                    <?php foreach ($dostepneZajecia as $z): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card zajecia-card h-100 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><?= htmlspecialchars($z['nazwa']) ?></h6>
                                </div>
                                <div class="card-body">
                                    <p class="small text-muted"><?= htmlspecialchars(substr($z['opis'], 0, 80)) ?>...</p>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-calendar3"></i> <?= date('d.m.Y', strtotime($z['data'])) ?></li>
                                        <li><i class="bi bi-clock"></i> <?= $z['godzina_od'] ?> - <?= $z['godzina_do'] ?></li>
                                        <li><i class="bi bi-people"></i> Wolne: <?= $z['wolne_miejsca'] ?>/<?= $z['limit_miejsc'] ?></li>
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-sm btn-primary w-100 btn-zapisz" data-zajecia-id="<?= $z['id'] ?>">
                                        <i class="bi bi-check-circle"></i> Zapisz się
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

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
