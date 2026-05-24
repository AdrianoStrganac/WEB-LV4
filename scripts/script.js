let sviPodaci = [];
let playlist = [];
let spremljena = localStorage.getItem("mojaPlaylista");

if (spremljena) {
    playlist = JSON.parse(spremljena);
}


Papa.parse("/public/glazba.csv", {
    download: true,
    header: true,
    skipEmptyLines: true, // 🔥 ignorira prazne redove
    complete: function(results) {
        console.log("Učitani podaci:", results.data); // DEBUG

        sviPodaci = results.data;

        prikaziTablicu(sviPodaci); // odmah prikaži sve
        popuniDropdownove();
        prikaziPlaylistu();
    },
    error: function(err) {
        console.error("Greška pri učitavanju CSV:", err);
    }
});

// generiranje tablice
function prikaziTablicu(podaci) {
    let tbody = document.querySelector("#tabla tbody");
    tbody.innerHTML = "";

    podaci.forEach(pjesma => {
        if (!pjesma.ID) return; // zaštita od praznih redova

        let tr = document.createElement("tr");

        tr.innerHTML = `
            <td>${pjesma.ID}</td>
            <td>${pjesma.Naslov}</td>
            <td>${pjesma.Izvođač}</td>
            <td>${pjesma.Žanr}</td>
            <td>${pjesma.BPM}</td>
            <td>${pjesma.Godina}</td>
            <td><button onclick="dodaj('${pjesma.Naslov}')">Dodaj</button></td>
        `;

        tbody.appendChild(tr);
    });
}

// filtriranje (TOČNO po zadatku)
function filtriraj() {
    let zanr = document.getElementById("zanr").value;
    let rasp = document.getElementById("raspolozenje").value;

    let bpmMin = document.getElementById("bpmMin").value;
    let bpmMax = document.getElementById("bpmMax").value;

    let godMin = document.getElementById("godinaMin").value;
    let godMax = document.getElementById("godinaMax").value;

    let filtrirano = sviPodaci.filter(p => {
        if (!p.ID) return false;

        return (!zanr || p.Žanr === zanr) &&
               (!rasp || p.Raspoloženje === rasp) &&
               (!bpmMin || parseInt(p.BPM) >= parseInt(bpmMin)) &&
               (!bpmMax || parseInt(p.BPM) <= parseInt(bpmMax)) &&
               (!godMin || parseInt(p.Godina) >= parseInt(godMin)) &&
               (!godMax || parseInt(p.Godina) <= parseInt(godMax));
    });

    prikaziTablicu(filtrirano);
}

// reset filtera
function reset() {
    document.getElementById("zanr").value = "";
    document.getElementById("raspolozenje").value = "";
    document.getElementById("bpmMin").value = "";
    document.getElementById("bpmMax").value = "";
    document.getElementById("godinaMin").value = "";
    document.getElementById("godinaMax").value = "";

    prikaziTablicu(sviPodaci);
}

// dropdownovi (žanr + raspoloženje)
function popuniDropdownove() {
    let zanrSelect = document.getElementById("zanr");
    let raspSelect = document.getElementById("raspolozenje");

    let zanrovi = [...new Set(sviPodaci.map(p => p.Žanr).filter(Boolean))];
    let raspolozenja = [...new Set(sviPodaci.map(p => p.Raspoloženje).filter(Boolean))];

    zanrSelect.innerHTML = `<option value="">Svi</option>`;
    raspSelect.innerHTML = `<option value="">Sva</option>`;

    zanrovi.forEach(z => {
        zanrSelect.innerHTML += `<option value="${z}">${z}</option>`;
    });

    raspolozenja.forEach(r => {
        raspSelect.innerHTML += `<option value="${r}">${r}</option>`;
    });
}

// dodavanje u playlistu
function dodaj(naziv) {
    if (playlist.includes(naziv)) {
        alert("Pjesma je već u playlisti!");
        return;
    }

    playlist.push(naziv);
    prikaziPlaylistu();
}

// prikaz playliste
function prikaziPlaylistu() {
    let ul = document.getElementById("playlist");
    ul.innerHTML = "";

    playlist.forEach((p, i) => {
        ul.innerHTML += `<li>${p} <button onclick="obrisi(${i})">X</button></li>`;
    });
}

// brisanje iz playliste
function obrisi(i) {
    playlist.splice(i, 1);
    prikaziPlaylistu();
}

// spremanje
function spremi() {
    localStorage.setItem("mojaPlaylista", JSON.stringify(playlist));

    document.getElementById("msg").innerText =
        "Playlista uspješno spremljena!";
}

function novaPlaylista() {
    playlist = [];
    localStorage.removeItem("mojaPlaylista");
    prikaziPlaylistu();

    document.getElementById("msg").innerText =
        "Započeta nova playlista!";
}