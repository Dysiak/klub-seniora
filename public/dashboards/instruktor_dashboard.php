<?php
/**
 * Dashboard dla Instruktora
 */
require_once __DIR__ . '/../../src/models/Instruktor.php';

// Pobranie danych instruktora
$instruktor = new Instruktor(['id' => $_SESSION['user_id']]);
$mojeZajecia = $instruktor->getMojeZajecia();
$zajeciaZakonczone = $instruktor->getMojeZajecia('odbyte');

// Statystyki
$liczbaZajec = count($mojeZajecia);
$liczbaPlanowanych = count(array_filter($mojeZajecia, fn($z) => $z['status'] === 'planowane'));
$liczbaUczestnikow = array_sum(array_column($mojeZajecia, 'liczba_uczestnikow'));
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Instruktora - Klub Seniora</title>
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
                <h2><i class="bi bi-person-badge"></i> Panel Instruktora</h2>
                <p class="text-muted">Witaj, <?= htmlspecialchars($_SESSION['user_login']) ?>!</p>
            </div>
        </div>

        <!-- Statystyki -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h3><?= $liczbaZajec ?></h3>
                    <p><i class="bi bi-calendar-event"></i> Wszystkie zajęcia</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <h3><?= $liczbaPlanowanych ?></h3>
                    <p><i class="bi bi-clock-history"></i> Planowane</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <h3><?= count($zajeciaZakonczone) ?></h3>
                    <p><i class="bi bi-check-circle"></i> Ukończone</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <h3><?= $liczbaUczestnikow ?></h3>
                    <p><i class="bi bi-people"></i> Łączna frekwencja</p>
                </div>
            </div>
        </div>

        <!-- Moje Zajęcia -->
        <div class="card dashboard-card">
            <div class="card-header">
                <i class="bi bi-calendar-week"></i> Moje Zajęcia
            </div>
            <div class="card-body">
                <?php if (empty($mojeZajecia)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Nie masz jeszcze przypisanych żadnych zajęć.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nazwa</th>
                                    <th>Data</th>
                                    <th>Godziny</th>
                                    <th>Sala</th>
                                    <th>Uczestnicy</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mojeZajecia as $z): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($z['nazwa']) ?></strong></td>
                                        <td><i class="bi bi-calendar3"></i> <?= date('d.m.Y', strtotime($z['data'])) ?></td>
                                        <td><i class="bi bi-clock"></i> <?= $z['godzina_od'] ?> - <?= $z['godzina_do'] ?></td>
                                        <td><i class="bi bi-door-open"></i> <?= htmlspecialchars($z['sala_nazwa'] ?? 'Brak') ?></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?= $z['liczba_uczestnikow'] ?>/<?= $z['limit_miejsc'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($z['status'] === 'planowane'): ?>
                                                <span class="badge bg-warning text-dark">Planowane</span>
                                            <?php elseif ($z['status'] === 'odbyte'): ?>
                                                <span class="badge bg-success">Odbyte</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($z['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-uczestnicy" data-zajecia-id="<?= $z['id'] ?>">
                                                <i class="bi bi-people"></i> Uczestnicy
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

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <!-- Modal: Lista Uczestników -->
    <div class="modal fade" id="modalUczestnicy" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-people"></i> Lista Uczestników</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-uczestnicy-body">
                    <div class="text-center">
                        <div class="spinner-border" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script>
        // Obsługa przycisków "Uczestnicy"
        document.querySelectorAll('.btn-uczestnicy').forEach(btn => {
            btn.addEventListener('click', async function() {
                const zajeciaId = this.dataset.zajeciaId;
                const modal = new bootstrap.Modal(document.getElementById('modalUczestnicy'));
                const body = document.getElementById('modal-uczestnicy-body');
                
                modal.show();
                body.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
                
                try {
                    const response = await fetch(`api/instruktor/lista_uczestnikow.php?id_zajec=${zajeciaId}`);
                    const result = await response.json();
                    
                    if (result.sukces) {
                        if (result.uczestnicy.length === 0) {
                            body.innerHTML = '<div class="alert alert-info">Brak zapisanych uczestników.</div>';
                        } else {
                            let html = '<div class="table-responsive"><table class="table"><thead><tr><th>Imię</th><th>Nazwisko</th><th>Email</th><th>Telefon</th><th>Status</th></tr></thead><tbody>';
                            result.uczestnicy.forEach(u => {
                                html += `<tr>
                                    <td>${u.imie}</td>
                                    <td>${u.nazwisko}</td>
                                    <td><a href="mailto:${u.email}">${u.email}</a></td>
                                    <td>${u.telefon || '-'}</td>
                                    <td><span class="badge bg-success">${u.status}</span></td>
                                </tr>`;
                            });
                            html += '</tbody></table></div>';
                            body.innerHTML = html;
                        }
                    } else {
                        body.innerHTML = `<div class="alert alert-danger">${result.komunikat}</div>`;
                    }
                } catch (error) {
                    body.innerHTML = '<div class="alert alert-danger">Błąd podczas ładowania danych.</div>';
                }
            });
        });
    </script>
</body>
</html>
