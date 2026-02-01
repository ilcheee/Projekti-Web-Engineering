document.addEventListener("DOMContentLoaded", () => {
  const SETTINGS_KEY = "cempra_settings_v1";
  const DAILY_KEY = "cempra_daily_searches_v1";
  const HISTORY_KEY = "cempra_search_history_v1";

  const dbFavsRaw = Array.isArray(window.CEMPRA_DB_FAVS) ? window.CEMPRA_DB_FAVS : [];

  const elFavCity = document.getElementById("dashFavCity");
  const elFavDesc = document.getElementById("dashFavDesc");
  const elFavTemp = document.getElementById("dashFavTemp");

  const elDashFavCount = document.getElementById("dashFavCount");
  const elDashSearchesToday = document.getElementById("dashSearchesToday");
  const elDashUnit = document.getElementById("dashUnit");

  const elStatFavCount = document.getElementById("statFavCount");
  const elStatSearchesToday = document.getElementById("statSearchesToday");
  const elStatUnitText = document.getElementById("statUnitText");

  const elHistory = document.getElementById("dashHistory");

  const elOpen = document.getElementById("openSettings");
  const elPanel = document.getElementById("settingsPanel");
  const elUnitC = document.getElementById("unitC");
  const elUnitF = document.getElementById("unitF");

  const dashCard = document.querySelector(".weather-card");

  function readJSON(key, fallback) {
    try {
      return JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback));
    } catch {
      return fallback;
    }
  }

  function writeJSON(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
  }

  function todayKey() {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}`;
  }

  function getSettings() {
    return readJSON(SETTINGS_KEY, { unit: "C" });
  }

  function setUnit(unit) {
    const s = getSettings();
    s.unit = unit;
    writeJSON(SETTINGS_KEY, s);
    render();
  }

  function getSearchesToday() {
    const obj = readJSON(DAILY_KEY, { day: todayKey(), count: 0 });
    const t = todayKey();
    if (obj.day !== t) {
      obj.day = t;
      obj.count = 0;
      writeJSON(DAILY_KEY, obj);
    }
    return obj.count || 0;
  }

  function openMeteoDesc(code) {
    const map = {
      0:"Kthjellët",1:"Pjesërisht me vranësira",2:"Me vranësira",3:"I mbuluar me re",
      45:"Mjegull",48:"Mjegull e dendur",
      51:"Pikim i lehtë",53:"Pikim mesatar",55:"Pikim i fortë",
      61:"Shi i lehtë",63:"Shi mesatar",65:"Shi i fortë",
      71:"Borë e lehtë",73:"Borë mesatare",75:"Borë e fortë",77:"Kristale bore",
      80:"Rrebeshe të lehta",81:"Rrebeshe mesatare",82:"Rrebeshe të forta",
      85:"Rrebeshe bore të lehta",86:"Rrebeshe bore të forta",
      95:"Stuhi",96:"Stuhi me breshër (lehtë)",99:"Stuhi me breshër (fortë)"
    };
    return map[code] || "Moti";
  }

  function themeKeyFromCode(code) {
    const storm = [95, 96, 99];
    const rain  = [51,53,55,56,57,61,63,65,66,67,80,81,82];
    const snow  = [71,73,75,77,85,86];
    const cloudy= [1,2,3,45,48];

    if (storm.includes(code)) return "storm";
    if (rain.includes(code)) return "rain";
    if (snow.includes(code)) return "snow";
    if (cloudy.includes(code)) return "cloudy";
    if (code === 0) return "sunny";
    return "cloudy";
  }

  function applyDashTheme(code) {
    if (!dashCard) return;
    dashCard.classList.add("dash-theme");
    dashCard.classList.remove("dash-sunny","dash-cloudy","dash-rain","dash-snow","dash-storm");
    dashCard.classList.add(`dash-${themeKeyFromCode(code)}`);
  }

  async function fetchCurrent(lat, lon) {
    const url =
      `https://api.open-meteo.com/v1/forecast?latitude=${encodeURIComponent(lat)}&longitude=${encodeURIComponent(lon)}` +
      `&current=temperature_2m,weather_code&timezone=auto`;

    const res = await fetch(url);
    if (!res.ok) throw new Error("Weather fetch error");
    const data = await res.json();

    return {
      tempC: Math.round(data.current.temperature_2m),
      code: data.current.weather_code
    };
  }

  function cToF(c) {
    return Math.round((c * 9) / 5 + 32);
  }

  function renderHistory() {
    if (!elHistory) return;

    const history = readJSON(HISTORY_KEY, []);
    if (!history || history.length === 0) {
      elHistory.innerHTML = "<li>Nuk ka kërkime ende.</li>";
      return;
    }

    const last5 = history.slice(0, 5);
    elHistory.innerHTML = last5.map(h => `<li>${escapeHtml(h.q)} · ${escapeHtml(h.when)}</li>`).join("");
  }

  function escapeHtml(str) {
    return String(str)
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function sortFavs(favs) {
    return [...favs].sort((a, b) => {
      const aT = a.addedAt || (a.created_at ? Date.parse(a.created_at) : 0) || 0;
      const bT = b.addedAt || (b.created_at ? Date.parse(b.created_at) : 0) || 0;
      return bT - aT;
    });
  }

  function renderLastFavoriteWeather(favs, unit) {
    if (!elFavTemp) return;

    if (!favs || favs.length === 0) {
      if (elFavCity) elFavCity.textContent = "Qyteti i fundit i preferuar: —";
      if (elFavDesc) elFavDesc.textContent = "Shto një qytet te “Të preferuarat” nga Ballina.";
      elFavTemp.textContent = "—";
      applyDashTheme(2);
      return;
    }

    const f = favs[0];
    const title = f.city_name || `${f.name || ""}${f.country ? ", " + f.country : ""}`.trim();

    if (elFavCity) elFavCity.textContent = `Qyteti i fundit i preferuar: ${title}`;
    if (elFavDesc) elFavDesc.textContent = "Duke ngarkuar motin...";
    elFavTemp.textContent = "…";

    const lat = (typeof f.lat === "number") ? f.lat : Number(f.lat);
    const lon = (typeof f.lon === "number") ? f.lon : Number(f.lon);

    if (!Number.isFinite(lat) || !Number.isFinite(lon)) {
      if (elFavDesc) elFavDesc.textContent = "Ky qytet s’ka koordinata (lat/lon) të ruajtura.";
      elFavTemp.textContent = "—";
      applyDashTheme(2);
      return;
    }

    fetchCurrent(lat, lon)
      .then(({ tempC, code }) => {
        const shown = unit === "F" ? cToF(tempC) : tempC;
        elFavTemp.textContent = `${shown}°`;
        if (elFavDesc) elFavDesc.textContent = `Tani · ${openMeteoDesc(code)}`;
        applyDashTheme(code);
      })
      .catch(() => {
        if (elFavDesc) elFavDesc.textContent = "S’u ngarkua moti për këtë qytet.";
        elFavTemp.textContent = "—";
        applyDashTheme(2);
      });
  }

  function render() {
    const settings = getSettings();
    const unit = settings.unit === "F" ? "F" : "C";

    const favs = sortFavs(dbFavsRaw);
    const favCount = Number.isFinite(window.CEMPRA_FAV_COUNT) ? window.CEMPRA_FAV_COUNT : favs.length;

    const searchesToday = getSearchesToday();

    if (elStatFavCount) elStatFavCount.textContent = String(favCount);
    if (elStatSearchesToday) elStatSearchesToday.textContent = String(searchesToday);
    if (elStatUnitText) elStatUnitText.textContent = unit === "F" ? "Fahrenheit" : "Celsius";

    // top card counters
    if (elDashFavCount) elDashFavCount.textContent = `Qytete të preferuara: ${favCount}`;
    if (elDashSearchesToday) elDashSearchesToday.textContent = `Kërkime sot: ${searchesToday}`;
    if (elDashUnit) elDashUnit.textContent = `Njësia: °${unit}`;

    if (elUnitC) elUnitC.classList.toggle("active-unit", unit === "C");
    if (elUnitF) elUnitF.classList.toggle("active-unit", unit === "F");

    renderHistory();
    renderLastFavoriteWeather(favs, unit);
  }

  if (elOpen && elPanel) {
    elOpen.addEventListener("click", () => {
      const isHidden = elPanel.getAttribute("aria-hidden") === "true";
      elPanel.setAttribute("aria-hidden", String(!isHidden));
      elOpen.textContent = isHidden ? "Mbyll cilësimet" : "Hap cilësimet";
    });
  }

  if (elUnitC) elUnitC.addEventListener("click", () => setUnit("C"));
  if (elUnitF) elUnitF.addEventListener("click", () => setUnit("F"));

  render();
});
