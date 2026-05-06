<?php
/**
 * Dashboard dla Koordynatora
 */
require_once __DIR__ . '/../../src/models/Koordynator.php';
require_once __DIR__ . '/../../src/models/Zajecia.php';
require_once __DIR__ . '/../../src/models/User.php';
require_once __DIR__ . '/../../src/models/Sala.php';

// Pobranie danych
$koordynator = new Koordynator(['id' => $_SESSION['user_id']]);
$wszystkieZajecia = Zajecia::pobierzWszystkie();
$instruktorzy = User::pobierzPoRoli('instruktor');
$sale = Sala::pobierzWszystkie();

// Statystyki
$liczbaZajec = count($wszystkieZajecia);
$liczbaPlanowanych = count(array_filter($wszystkieZajecia, fn($z) => $z['status'] === 'planowane'));
$liczbaOdbytych = count(array_filter($wszystkieZajecia, fn($z) => $z['status'] === 'odbyte'));
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Koordynatora - Klub Seniora</title>
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
                <h2><i class="bi bi-kanban"></i> Panel Koordynatora</h2>
                <p class="text-muted">Witaj, <?= htmlspecialchars($_SESSION['user_login']) ?>!</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNoweZajecia">
                    <i class="bi bi-plus-circle"></i> Nowe Zajęcia
                </button>
            </div>
        </div>

        <!-- Statystyki -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <h3><?= $liczbaZajec ?></h3>
                    <p><i class="bi bi-calendar-event"></i> Wszystkie zajęcia</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card warning">
                    <h3><?= $liczbaPlanowanych ?></h3>
                    <p><i class="bi bi-clock-history"></i> Planowane</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card success">
                    <h3><?= $liczbaOdbytych ?></h3>
                    <p><i class="bi bi-check-circle"></i> Odbyte</p>
                </div>
            </div>
        </div>

        <!-- Lista Zajęć -->
        <div class="card dashboard-card">
            <div class="card-header">
                <i class="bi bi-list-ul"></i> Wszystkie Zajęcia
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nazwa</th>
                                <th>Data</th>
                                <th>Godziny</th>
                                <th>Instruktor</th>
                                <th>Sala</th>
                                <th>Miejsca</th>
                                <th>Status</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wszystkieZajecia as $z): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($z['nazwa']) ?></strong></td>
                                    <td><i class="bi bi-calendar3"></i> <?= date('d.m.Y', strtotime($z['data'])) ?></td>
                                    <td><i class="bi bi-clock"></i> <?= $z['godzina_od'] ?> - <?= $z['godzina_do'] ?></td>
                                    <td><?= htmlspecialchars($z['instruktor_imie'] . ' ' . $z['instruktor_nazwisko']) ?></td>
                                    <td><i class="bi bi-door-open"></i> <?= htmlspecialchars($z['sala_nazwa'] ?? 'Brak') ?></td>
                                    <td><?= $z['wolne_miejsca'] ?>/<?= $z['limit_miejsc'] ?></td>
                                    <td>
                                        <?php if ($z['status'] === 'planowane'): ?>
                                            <span class="badge bg-warning text-dark">Planowane</span>
                                        <?php elseif ($z['status'] === 'odbyte'): ?>
                                            <span class="badge bg-success">Odbyte</span>
                                        <?php elseif ($z['status'] === 'odwolane'): ?>
                                            <span class="badge bg-danger">Odwołane</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($z['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning btn-edytuj" 
                                                data-zajecia='<?= json_encode($z, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-usun" data-zajecia-id="<?= $z['id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <!-- Modal: Nowe Zajęcia -->
    <div class="modal fade" id="modalNoweZajecia" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Utwórz Nowe Zajęcia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-nowe-zajecia">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nazwa zajęć *</label>
                                <input type="text" class="form-control" name="nazwa" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data *</label>
                                <input type="date" class="form-control" name="data" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Godzina od *</label>
                                <input type="time" class="form-control" name="godzina_od" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Godzina do *</label>
                                <input type="time" class="form-control" name="godzina_do" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Instruktor *</label>
                                <select class="form-select" name="id_instruktora" required>
                                    <option value="">Wybierz instruktora</option>
                                    <?php foreach ($instruktorzy as $ins): ?>
                                        <option value="<?= $ins['id'] ?>">
                                            <?= htmlspecialchars($ins['imie'] . ' ' . $ins['nazwisko']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sala</label>
                                <select class="form-select" name="id_sali">
                                    <option value="">Brak (zajęcia zewnętrzne)</option>
                                    <?php foreach ($sale as $sala): ?>
                                        <option value="<?= $sala['id'] ?>">
                                            <?= htmlspecialchars($sala['nazwa']) ?> (<?= $sala['pojemnosc'] ?> miejsc)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Opis</label>
                            <textarea class="form-control" name="opis" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Limit miejsc *</label>
                            <input type="number" class="form-control" name="limit_miejsc" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Utwórz
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edytuj Zajęcia -->
    <div class="modal fade" id="modalEdytujZajecia" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Edytuj Zajęcia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-edytuj-zajecia">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nazwa zajęć *</label>
                                <input type="text" class="form-control" name="nazwa" id="edit-nazwa" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data *</label>
                                <input type="date" class="form-control" name="data" id="edit-data" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Godzina od *</label>
                                <input type="time" class="form-control" name="godzina_od" id="edit-godzina-od" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Godzina do *</label>
                                <input type="time" class="form-control" name="godzina_do" id="edit-godzina-do" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Instruktor *</label>
                                <select class="form-select" name="id_instruktora" id="edit-instruktor" required>
                                    <option value="">Wybierz instruktora</option>
                                    <?php foreach ($instruktorzy as $ins): ?>
                                        <option value="<?= $ins['id'] ?>">
                                            <?= htmlspecialchars($ins['imie'] . ' ' . $ins['nazwisko']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sala</label>
                                <select class="form-select" name="id_sali" id="edit-sala">
                                    <option value="">Brak (zajęcia zewnętrzne)</option>
                                    <?php foreach ($sale as $sala): ?>
                                        <option value="<?= $sala['id'] ?>">
                                            <?= htmlspecialchars($sala['nazwa']) ?> (<?= $sala['pojemnosc'] ?> miejsc)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Opis</label>
                            <textarea class="form-control" name="opis" id="edit-opis" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Limit miejsc *</label>
                            <input type="number" class="form-control" name="limit_miejsc" id="edit-limit" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit-status">
                                <option value="planowane">Planowane</option>
                                <option value="odbyte">Odbyte</option>
                                <option value="odwolane">Odwołane</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Zapisz zmiany
                        </button>
                    </div>
                </form>
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
        // Obsługa tworzenia zajęć
        document.getElementById('form-nowe-zajecia').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/koordynator/utworz_zajecia.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.sukces) {
                    showToast(result.komunikat, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.komunikat, 'error');
                }
            } catch (error) {
                showToast('Błąd podczas tworzenia zajęć', 'error');
            }
        });

        // Obsługa edycji
        document.querySelectorAll('.btn-edytuj').forEach(btn => {
            btn.addEventListener('click', function() {
                const zajecia = JSON.parse(this.dataset.zajecia);
                const modal = new bootstrap.Modal(document.getElementById('modalEdytujZajecia'));
                
                document.getElementById('edit-id').value = zajecia.id;
                document.getElementById('edit-nazwa').value = zajecia.nazwa;
                document.getElementById('edit-data').value = zajecia.data;
                document.getElementById('edit-godzina-od').value = zajecia.godzina_od;
                document.getElementById('edit-godzina-do').value = zajecia.godzina_do;
                document.getElementById('edit-instruktor').value = zajecia.id_instruktora;
                document.getElementById('edit-sala').value = zajecia.id_sali || '';
                document.getElementById('edit-opis').value = zajecia.opis || '';
                document.getElementById('edit-limit').value = zajecia.limit_miejsc;
                document.getElementById('edit-status').value = zajecia.status;
                
                modal.show();
            });
        });

        // Obsługa zapisu edycji
        document.getElementById('form-edytuj-zajecia').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/koordynator/edytuj_zajecia.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.sukces) {
                    showToast(result.komunikat, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.komunikat, 'error');
                }
            } catch (error) {
                showToast('Błąd podczas aktualizacji zajęć', 'error');
            }
        });

        // Obsługa usuwania
        document.querySelectorAll('.btn-usun').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!confirm('Czy na pewno chcesz usunąć te zajęcia?')) return;
                
                const zajeciaId = this.dataset.zajeciaId;
                
                try {
                    const response = await fetch('api/koordynator/usun_zajecia.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({id_zajec: zajeciaId})
                    });
                    const result = await response.json();
                    
                    if (result.sukces) {
                        showToast(result.komunikat, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(result.komunikat, 'error');
                    }
                } catch (error) {
                    showToast('Błąd podczas usuwania zajęć', 'error');
                }
            });
        });
    </script>
</body>
</html>
