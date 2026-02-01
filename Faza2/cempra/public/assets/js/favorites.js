document.addEventListener("DOMContentLoaded", () => {
  const FAVORITES_KEY = "cempra_favorites_v1";
  const grid = document.getElementById("favoritesGrid");
  const empty = document.getElementById("favoritesEmpty");

  function openMeteoDesc(code) {
    const map = {
      0: "Kthjellët",
      1: "Pjesërisht me vranësira",
      2: "Me vranësira",
      3: "I mbuluar me re",
      45: "Mjegull",
      48: "Mjegull e dendur",
      51: "Pikim i lehtë",
      53: "Pikim mesatar",
      55: "Pikim i fortë",
      61: "Shi i lehtë",
      63: "Shi mesatar",
      65: "Shi i fortë",
      71: "Borë e lehtë",
      73: "Borë mesatare",
      75: "Borë e fortë",
      77: "Kristale bore",
      80: "Rrebeshe të lehta",
      81: "Rrebeshe mesatare",
      82: "Rrebeshe të forta",
      85: "Rrebeshe bore të lehta",
      86: "Rrebeshe bore të forta",
      95: "Stuhi",
      96: "Stuhi me breshër (lehtë)",
      99: "Stuhi me breshër (fortë)",
    };
    return map[code] || "Moti";
  }

  function themeKeyFromCode(code) {
    const storm = [95, 96, 99];
    const rain = [51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82];
    const snow = [71, 73, 75, 77, 85, 86];
    const cloudy = [1, 2, 3, 45, 48];

    if (storm.includes(code)) return "storm";
    if (rain.includes(code)) return "rain";
    if (snow.includes(code)) return "snow";
    if (cloudy.includes(code)) return "cloudy";
    if (code === 0) return "sunny";
    return "cloudy";
  }

  function applyTheme(card, code) {
    card.classList.remove("fav-sunny", "fav-cloudy", "fav-rain", "fav-snow", "fav-storm");
    card.classList.add("fav-theme", `fav-${themeKeyFromCode(code)}`);
  }

  // ---------------- STORAGE ----------------
  function readFavorites() {
    try {
      return JSON.parse(localStorage.getItem(FAVORITES_KEY) || "[]");
    } catch {
      return [];
    }
  }

  function writeFavorites(list) {
    localStorage.setItem(FAVORITES_KEY, JSON.stringify(list));
  }

  function normalizeKey(place) {
    const a = (place.name || "").trim().toLowerCase();
    const b = (place.country || "").trim().toLowerCase();
    return `${a}|${b}`;
  }

  // ---------------- WEATHER FETCH ----------------
  async function fetchCurrent(lat, lon) {
    const url =
      `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}` +
      `&current=temperature_2m,weather_code&timezone=auto`;

    const res = await fetch(url);
    if (!res.ok) throw new Error("Weather fetch error");
    const data = await res.json();

    return {
      temp: Math.round(data.current.temperature_2m),
      code: data.current.weather_code,
    };
  }

  // ---------------- MAP ----------------
  let map = null;
  let markersLayer = null;

  function initMapIfNeeded() {
    const el = document.getElementById("favoritesMap");
    if (!el) return; // nuk e ke div në HTML
    if (typeof L === "undefined") return; // Leaflet s’është ngarku
    if (map) return; // veç e inicializum

    map = L.map("favoritesMap", { scrollWheelZoom: false }).setView([42.66, 21.16], 6);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 19,
      attribution: "&copy; OpenStreetMap contributors",
    }).addTo(map);

    markersLayer = L.layerGroup().addTo(map);
  }

  function renderMapFromFavorites(list) {
    initMapIfNeeded();
    if (!map || !markersLayer) return;

    markersLayer.clearLayers();

    const points = [];
    list.forEach((fav) => {
      if (typeof fav.lat !== "number" || typeof fav.lon !== "number") return;

      const title = `${fav.name}${fav.country ? ", " + fav.country : ""}`;
      const m = L.marker([fav.lat, fav.lon]).bindPopup(title);
      markersLayer.addLayer(m);
      points.push([fav.lat, fav.lon]);
    });

    if (points.length === 1) {
      map.setView(points[0], 9);
    } else if (points.length > 1) {
      map.fitBounds(points, { padding: [20, 20] });
    }
  }

  // ---------------- UI RENDER ----------------
  function getTop3Newest(list) {
    return (list || [])
      .sort((a, b) => (b.addedAt || 0) - (a.addedAt || 0))
      .slice(0, 3);
  }

  function renderCards(list) {
    if (!grid) return;

    grid.innerHTML = "";

    if (!list || list.length === 0) {
      if (empty) empty.style.display = "block";
      return;
    }
    if (empty) empty.style.display = "none";

    list.forEach((fav) => {
      const card = document.createElement("div");
      card.className = "card fav-theme fav-cloudy"; // default

      card.innerHTML = `
        <div class="card-title">${fav.name}${fav.country ? ", " + fav.country : ""}</div>
        <div class="card-temp" data-temp>--°</div>
        <div class="card-sub" data-sub>Duke ngarkuar...</div>
        <div style="display:flex; gap:10px; margin-top:10px;">
          <button class="btn-ghost" type="button" data-remove>Fshij</button>
        </div>
      `;

      if (typeof fav.weatherCode === "number") {
        applyTheme(card, fav.weatherCode);
      }

      card.querySelector("[data-remove]").addEventListener("click", () => {
        const current = readFavorites();
        const key = normalizeKey(fav);
        const filtered = current.filter((x) => normalizeKey(x) !== key);
        writeFavorites(filtered);
        refreshUI(); // rifresko cards + hartë
      });

      const tempEl = card.querySelector("[data-temp]");
      const subEl = card.querySelector("[data-sub]");

      fetchCurrent(fav.lat, fav.lon)
        .then(({ temp, code }) => {
          tempEl.textContent = `${temp}°`;
          subEl.textContent = openMeteoDesc(code);
          applyTheme(card, code);
        })
        .catch(() => {
          subEl.textContent = "S’u ngarkua moti.";
        });

      grid.appendChild(card);
    });
  }

  function refreshUI() {
    const list = getTop3Newest(readFavorites());
    renderCards(list);
    renderMapFromFavorites(list);
  }

  refreshUI();
});
