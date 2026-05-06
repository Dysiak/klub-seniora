/**
 * Główny plik JavaScript dla aplikacji Klub Seniora
 */

// ===== Utility Functions =====
function showToast(message, type = 'info') {
    const toast = document.getElementById('notification-toast');
    const toastBody = toast.querySelector('.toast-body');
    const toastHeader = toast.querySelector('.toast-header');
    
    toastBody.textContent = message;
    
    // Ustaw kolor w zależności od typu
    toastHeader.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
    if (type === 'success') {
        toastHeader.classList.add('bg-success', 'text-white');
    } else if (type === 'error') {
        toastHeader.classList.add('bg-danger', 'text-white');
    } else if (type === 'warning') {
        toastHeader.classList.add('bg-warning');
    } else {
        toastHeader.classList.add('bg-info', 'text-white');
    }
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

function showSpinner() {
    const spinner = document.createElement('div');
    spinner.id = 'spinner-overlay';
    spinner.className = 'spinner-overlay';
    spinner.innerHTML = '<div class="spinner-border text-light" role="status"><span class="visually-hidden">Ładowanie...</span></div>';
    document.body.appendChild(spinner);
}

function hideSpinner() {
    const spinner = document.getElementById('spinner-overlay');
    if (spinner) {
        spinner.remove();
    }
}

// ===== Event Listeners =====
document.addEventListener('DOMContentLoaded', function() {
    
    // Obsługa przycisków "Zapisz się" (Senior)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-zapisz') || e.target.closest('.btn-zapisz')) {
            const btn = e.target.classList.contains('btn-zapisz') ? e.target : e.target.closest('.btn-zapisz');
            const zajeciaId = btn.dataset.zajeciaId;
            zapiszNaZajecia(zajeciaId);
        }
        
        // Obsługa przycisków "Anuluj rezerwację" (Senior)
        if (e.target.classList.contains('btn-anuluj') || e.target.closest('.btn-anuluj')) {
            const btn = e.target.classList.contains('btn-anuluj') ? e.target : e.target.closest('.btn-anuluj');
            const rezerwacjaId = btn.dataset.rezerwacjaId;
            if (confirm('Czy na pewno chcesz anulować tę rezerwację?')) {
                anulujRezerwacje(rezerwacjaId);
            }
        }
    });
    
    // Animacja kart przy scrollu (jeśli są obecne)
    const cards = document.querySelectorAll('.zajecia-card');
    if (cards.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, { threshold: 0.1 });
        
        cards.forEach(card => observer.observe(card));
    }
});

// ===== Senior Functions =====
async function zapiszNaZajecia(zajeciaId) {
    showSpinner();
    
    try {
        const response = await fetch('api/senior/zapisz.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_zajec: zajeciaId })
        });
        
        const data = await response.json();
        
        if (data.sukces) {
            showToast(data.komunikat, 'success');
            // Odśwież listę zajęć lub przekieruj
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.komunikat, 'error');
        }
    } catch (error) {
        showToast('Wystąpił błąd podczas zapisu', 'error');
        console.error('Error:', error);
    } finally {
        hideSpinner();
    }
}

async function anulujRezerwacje(rezerwacjaId) {
    showSpinner();
    
    try {
        const response = await fetch('api/senior/anuluj.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_rezerwacji: rezerwacjaId })
        });
        
        const data = await response.json();
        
        if (data.sukces) {
            showToast(data.komunikat, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.komunikat, 'error');
        }
    } catch (error) {
        showToast('Wystąpił błąd podczas anulowania', 'error');
        console.error('Error:', error);
    } finally {
        hideSpinner();
    }
}

// ===== Koordynator Functions =====
async function utworzZajecia() {
    const form = document.getElementById('form-nowe-zajecia');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    showSpinner();
    
    try {
        const response = await fetch('api/koordynator/utworz_zajecia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.sukces) {
            showToast(result.komunikat, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalNoweZajecia')).hide();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(result.komunikat, 'error');
        }
    } catch (error) {
        showToast('Wystąpił błąd podczas tworzenia zajęć', 'error');
        console.error('Error:', error);
    } finally {
        hideSpinner();
    }
}

async function edytujZajecia() {
    const form = document.getElementById('form-edytuj-zajecia');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    showSpinner();
    
    try {
        const response = await fetch('api/koordynator/edytuj_zajecia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.sukces) {
            showToast(result.komunikat, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalEdytujZajecia')).hide();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(result.komunikat, 'error');
        }
    } catch (error) {
        showToast('Wystąpił błąd podczas edycji zajęć', 'error');
        console.error('Error:', error);
    } finally {
        hideSpinner();
    }
}

async function usunZajecia(zajeciaId) {
    if (!confirm('Czy na pewno chcesz usunąć te zajęcia?')) {
        return;
    }
    
    showSpinner();
    
    try {
        const response = await fetch('api/koordynator/usun_zajecia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_zajec: zajeciaId })
        });
        
        const result = await response.json();
        
        if (result.sukces) {
            showToast(result.komunikat, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(result.komunikat, 'error');
        }
    } catch (error) {
        showToast('Wystąpił błąd podczas usuwania zajęć', 'error');
        console.error('Error:', error);
    } finally {
        hideSpinner();
    }
}

// ===== Instruktor Functions =====
async function pobierzListeUczestnikow(zajeciaId) {
    showSpinner();
    
    try {
        const response = await fetch(`api/instruktor/lista_uczestnikow.php?id_zajec=${zajeciaId}`);
        const data = await response.json();
        
        if (data.sukces) {
            // Wyświetl listę w modalu
            wyswietlListeUczestnikow(data.uczestnicy);
        } else {
            showToast(data.komunikat, 'error');
        }
    } catch (error) {
        showToast('Wystąpił błąd podczas pobierania listy', 'error');
        console.error('Error:', error);
    } finally {
        hideSpinner();
    }
}

function wyswietlListeUczestnikow(uczestnicy) {
    const modal = document.getElementById('modalListaUczestnikow');
    const tbody = modal.querySelector('tbody');
    
    tbody.innerHTML = '';
    
    uczestnicy.forEach((u, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${u.imie} ${u.nazwisko}</td>
            <td>${u.email}</td>
            <td>${u.telefon}</td>
            <td><span class="badge bg-success">${u.status}</span></td>
        `;
        tbody.appendChild(tr);
    });
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

// ===== Animation on scroll =====
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
        }
    });
}, observerOptions);

// Obserwuj wszystkie karty zajęć
document.querySelectorAll('.zajecia-card').forEach(card => {
    observer.observe(card);
});
