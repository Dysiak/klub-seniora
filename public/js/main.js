// Główny plik JavaScript dla aplikacji klubu seniora

// Funkcja pomocnicza do wykonywania zapytań AJAX
async function fetchAPI(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('Błąd API:', error);
        return { sukces: false, komunikat: 'Błąd połączenia z serwerem' };
    }
}

// Funkcja do wyświetlania komunikatów
function pokazKomunikat(komunikat, typ = 'info') {
    const container = document.getElementById('komunikaty');
    if (!container) return;
    
    const div = document.createElement('div');
    div.className = `message message-${typ}`;
    div.textContent = komunikat;
    
    container.appendChild(div);
    
    // Automatyczne usunięcie po 5 sekundach
    setTimeout(() => {
        div.remove();
    }, 5000);
}

// Funkcja do formatowania daty
function formatujDate(dataString) {
    const data = new Date(dataString);
    const dzien = String(data.getDate()).padStart(2, '0');
    const miesiac = String(data.getMonth() + 1).padStart(2, '0');
    const rok = data.getFullYear();
    return `${dzien}.${miesiac}.${rok}`;
}

// Funkcja do formatowania godziny
function formatujGodzine(godzinaString) {
    return godzinaString.substring(0, 5); // HH:MM
}

// Funkcja walidacji formularza
function walidujFormularz(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const wymagane = form.querySelectorAll('[required]');
    let valid = true;
    
    wymagane.forEach(pole => {
        if (!pole.value.trim()) {
            pole.style.borderColor = 'red';
            valid = false;
        } else {
            pole.style.borderColor = '#bdc3c7';
        }
    });
    
    return valid;
}

// Funkcja do walidacji email
function walidujEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Funkcja do walidacji telefonu (9-15 cyfr)
function walidujTelefon(telefon) {
    const regex = /^[0-9]{9,15}$/;
    return regex.test(telefon.replace(/\s/g, ''));
}

// Obsługa zapisywania się na zajęcia (Senior)
async function zapiszNaZajecia(idZajec) {
    if (!confirm('Czy na pewno chcesz zapisać się na te zajęcia?')) {
        return;
    }
    
    const wynik = await fetchAPI('/api/rezerwacje.php', 'POST', {
        akcja: 'zapisz',
        id_zajec: idZajec
    });
    
    if (wynik.sukces) {
        pokazKomunikat('Zapisano pomyślnie na zajęcia!', 'success');
        // Odświeżenie listy lub aktualizacja interfejsu
        setTimeout(() => location.reload(), 1500);
    } else {
        pokazKomunikat(wynik.komunikat, 'error');
    }
}

// Obsługa anulowania rezerwacji (Senior)
async function anulujRezerwacje(idRezerwacji) {
    if (!confirm('Czy na pewno chcesz anulować rezerwację?')) {
        return;
    }
    
    const wynik = await fetchAPI('/api/rezerwacje.php', 'POST', {
        akcja: 'anuluj',
        id_rezerwacji: idRezerwacji
    });
    
    if (wynik.sukces) {
        pokazKomunikat('Rezerwacja została anulowana', 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        pokazKomunikat(wynik.komunikat, 'error');
    }
}

// Obsługa tworzenia zajęć (Koordynator)
async function utworzZajecia(daneFormularza) {
    const wynik = await fetchAPI('/api/zajecia.php', 'POST', {
        akcja: 'utworz',
        ...daneFormularza
    });
    
    if (wynik.sukces) {
        pokazKomunikat('Zajęcia zostały utworzone!', 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        pokazKomunikat(wynik.komunikat, 'error');
    }
}

// Obsługa sprawdzania dostępności sali
async function sprawdzDostepnoscSali(idSali, data, godzinaOd, godzinaDo) {
    const wynik = await fetchAPI('/api/sale.php', 'POST', {
        akcja: 'sprawdz_dostepnosc',
        id_sali: idSali,
        data: data,
        godzina_od: godzinaOd,
        godzina_do: godzinaDo
    });
    
    return wynik.dostepna;
}

// Inicjalizacja po załadowaniu DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('System klubu seniora załadowany');
    
    // Dodanie obsługi dla przycisków "Zapisz się"
    const przyciski = document.querySelectorAll('.btn-zapisz');
    przyciski.forEach(przycisk => {
        przycisk.addEventListener('click', function(e) {
            e.preventDefault();
            const idZajec = this.dataset.idZajec;
            zapiszNaZajecia(idZajec);
        });
    });
    
    // Dodanie obsługi dla przycisków "Anuluj"
    const przyciskiAnuluj = document.querySelectorAll('.btn-anuluj');
    przyciskiAnuluj.forEach(przycisk => {
        przycisk.addEventListener('click', function(e) {
            e.preventDefault();
            const idRezerwacji = this.dataset.idRezerwacji;
            anulujRezerwacje(idRezerwacji);
        });
    });
    
    // Walidacja formularzy w czasie rzeczywistym
    const formularze = document.querySelectorAll('form');
    formularze.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!walidujFormularz(this.id)) {
                e.preventDefault();
                pokazKomunikat('Wypełnij wszystkie wymagane pola', 'error');
            }
        });
    });
});

// Eksport funkcji dla użycia w innych skryptach
window.KlubSeniora = {
    zapiszNaZajecia,
    anulujRezerwacje,
    utworzZajecia,
    sprawdzDostepnoscSali,
    pokazKomunikat,
    formatujDate,
    formatujGodzine,
    walidujEmail,
    walidujTelefon
};
