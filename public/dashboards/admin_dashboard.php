<?php
/**
 * Dashboard dla Administratora
 */
require_once __DIR__ . '/../../src/models/Administrator.php';
require_once __DIR__ . '/../../src/models/User.php';

// Pobranie wszystkich użytkowników
$admin = new Administrator(['id' => $_SESSION['user_id']]);
$uzytkownicy = User::pobierzWszystkich();

// Statystyki
$liczbaSeniorow = count(array_filter($uzytkownicy, fn($u) => $u['rola'] === 'senior'));
$liczbaInstruktorow = count(array_filter($uzytkownicy, fn($u) => $u['rola'] === 'instruktor'));
$liczbaKoordynatorow = count(array_filter($uzytkownicy, fn($u) => $u['rola'] === 'koordynator'));
$liczbaAdminow = count(array_filter($uzytkownicy, fn($u) => $u['rola'] === 'administrator'));
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora - Klub Seniora</title>
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
                <h2><i class="bi bi-shield-check"></i> Panel Administratora</h2>
                <p class="text-muted">Witaj, <?= htmlspecialchars($_SESSION['user_login']) ?>!</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNowyUzytkownik">
                    <i class="bi bi-person-plus"></i> Dodaj Użytkownika
                </button>
            </div>
        </div>

        <!-- Statystyki -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h3><?= count($uzytkownicy) ?></h3>
                    <p><i class="bi bi-people"></i> Wszyscy użytkownicy</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <h3><?= $liczbaSeniorow ?></h3>
                    <p><i class="bi bi-person"></i> Seniorzy</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <h3><?= $liczbaInstruktorow ?></h3>
                    <p><i class="bi bi-person-badge"></i> Instruktorzy</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <h3><?= $liczbaKoordynatorow ?></h3>
                    <p><i class="bi bi-kanban"></i> Koordynatorzy</p>
                </div>
            </div>
        </div>

        <!-- Lista Użytkowników -->
        <div class="card dashboard-card">
            <div class="card-header">
                <i class="bi bi-people-fill"></i> Zarządzanie Użytkownikami
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Login</th>
                                <th>Imię i Nazwisko</th>
                                <th>Email</th>
                                <th>Rola</th>
                                <th>Status</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($uzytkownicy as $u): ?>
                                <tr>
                                    <td><?= $u['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($u['login']) ?></strong></td>
                                    <td><?= htmlspecialchars($u['imie'] . ' ' . $u['nazwisko']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = [
                                            'senior' => 'bg-info',
                                            'instruktor' => 'bg-success',
                                            'koordynator' => 'bg-warning text-dark',
                                            'administrator' => 'bg-danger'
                                        ][$u['rola']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($u['rola']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($u['aktywny']): ?>
                                            <span class="badge bg-success">Aktywny</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nieaktywny</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                                            <button class="btn btn-sm btn-warning btn-edytuj-user" 
                                                    data-user='<?= json_encode($u, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-<?= $u['aktywny'] ? 'secondary' : 'success' ?> btn-toggle-status" 
                                                    data-user-id="<?= $u['id'] ?>"
                                                    data-current-status="<?= $u['aktywny'] ?>">
                                                <i class="bi bi-<?= $u['aktywny'] ? 'lock' : 'unlock' ?>"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-usun-user" data-user-id="<?= $u['id'] ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-primary">To Ty</span>
                                        <?php endif; ?>
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

    <!-- Modal: Nowy Użytkownik -->
    <div class="modal fade" id="modalNowyUzytkownik" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Dodaj Nowego Użytkownika</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-nowy-uzytkownik">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Login *</label>
                                <input type="text" class="form-control" name="login" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hasło *</label>
                                <input type="password" class="form-control" name="haslo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rola *</label>
                                <select class="form-select" name="rola" required>
                                    <option value="senior">Senior</option>
                                    <option value="instruktor">Instruktor</option>
                                    <option value="koordynator">Koordynator</option>
                                    <option value="administrator">Administrator</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Imię *</label>
                                <input type="text" class="form-control" name="imie" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nazwisko *</label>
                                <input type="text" class="form-control" name="nazwisko" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="tel" class="form-control" name="telefon" pattern="[0-9]{9}" title="Numer telefonu musi zawierać dokładnie 9 cyfr" placeholder="123456789">
                                <div class="form-text">Format: 9 cyfr (np. 123456789)</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data urodzenia</label>
                                <input type="date" class="form-control" name="data_urodzenia">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Dodaj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edytuj Użytkownika -->
    <div class="modal fade" id="modalEdytujUzytkownika" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Edytuj Użytkownika</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-edytuj-uzytkownika">
                    <input type="hidden" name="id" id="edit-user-id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" id="edit-email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rola *</label>
                                <select class="form-select" name="rola" id="edit-rola" required>
                                    <option value="senior">Senior</option>
                                    <option value="instruktor">Instruktor</option>
                                    <option value="koordynator">Koordynator</option>
                                    <option value="administrator">Administrator</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Imię *</label>
                                <input type="text" class="form-control" name="imie" id="edit-imie" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nazwisko *</label>
                                <input type="text" class="form-control" name="nazwisko" id="edit-nazwisko" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="tel" class="form-control" name="telefon" id="edit-telefon" pattern="[0-9]{9}" title="Numer telefonu musi zawierać dokładnie 9 cyfr" placeholder="123456789">
                                <div class="form-text">Format: 9 cyfr (np. 123456789)</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nowe hasło</label>
                                <input type="password" class="form-control" name="haslo" placeholder="Zostaw puste, aby nie zmieniać">
                            </div>
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
        // Obsługa dodawania użytkownika
        document.getElementById('form-nowy-uzytkownik').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/admin/utworz_uzytkownika.php', {
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
                showToast('Błąd podczas tworzenia użytkownika', 'error');
            }
        });

        // Obsługa edycji użytkownika
        document.querySelectorAll('.btn-edytuj-user').forEach(btn => {
            btn.addEventListener('click', function() {
                const user = JSON.parse(this.dataset.user);
                const modal = new bootstrap.Modal(document.getElementById('modalEdytujUzytkownika'));
                
                document.getElementById('edit-user-id').value = user.id;
                document.getElementById('edit-email').value = user.email;
                document.getElementById('edit-rola').value = user.rola;
                document.getElementById('edit-imie').value = user.imie;
                document.getElementById('edit-nazwisko').value = user.nazwisko;
                document.getElementById('edit-telefon').value = user.telefon || '';
                
                modal.show();
            });
        });

        // Obsługa zapisu edycji
        document.getElementById('form-edytuj-uzytkownika').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/admin/edytuj_uzytkownika.php', {
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
                showToast('Błąd podczas aktualizacji użytkownika', 'error');
            }
        });

        // Obsługa blokowania/odblokowywania
        document.querySelectorAll('.btn-toggle-status').forEach(btn => {
            btn.addEventListener('click', async function() {
                const userId = this.dataset.userId;
                const currentStatus = this.dataset.currentStatus === '1';
                const action = currentStatus ? 'zablokować' : 'odblokować';
                
                if (!confirm(`Czy na pewno chcesz ${action} tego użytkownika?`)) return;
                
                try {
                    const response = await fetch('api/admin/toggle_status.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({id: userId})
                    });
                    const result = await response.json();
                    
                    if (result.sukces) {
                        showToast(result.komunikat, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(result.komunikat, 'error');
                    }
                } catch (error) {
                    showToast('Błąd podczas zmiany statusu', 'error');
                }
            });
        });

        // Obsługa usuwania użytkownika
        document.querySelectorAll('.btn-usun-user').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!confirm('Czy na pewno chcesz usunąć tego użytkownika? Tej operacji nie można cofnąć!')) return;
                
                const userId = this.dataset.userId;
                
                try {
                    const response = await fetch('api/admin/usun_uzytkownika.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({id: userId})
                    });
                    const result = await response.json();
                    
                    if (result.sukces) {
                        showToast(result.komunikat, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(result.komunikat, 'error');
                    }
                } catch (error) {
                    showToast('Błąd podczas usuwania użytkownika', 'error');
                }
            });
        });
    </script>
</body>
</html>
