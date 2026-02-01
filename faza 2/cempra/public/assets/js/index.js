console.log("index.js loaded ✅");

document.addEventListener("DOMContentLoaded", () => {
  const FAVORITES_KEY = "cempra_favorites_v1";

  const elCard = document.getElementById("weatherCard");
  const elToggleForecast = document.getElementById("toggleForecast");
  const elForecastPanel = document.getElementById("forecastPanel");
  const elForecastGrid = document.getElementById("forecastGrid");

  const elWxAnim = document.getElementById("wxAnim");
  const elInput = document.getElementById("cityInput");
  const elBtn = document.getElementById("searchBtn");

  const elCity = document.getElementById("wCity");
  const elDesc = document.getElementById("wDesc");
  const elTemp = document.getElementById("wTemp");
  const elWind = document.getElementById("wWind");
  const elHum = document.getElementById("wHum");
  const elFeels = document.getElementById("wFeels");

  const elAddFav = document.getElementById("addFavoriteBtn");

  if (!elCard || !elInput || !elBtn || !elCity || !elDesc || !elTemp) {
    console.error("Missing HTML elements (check id's).");
    return;
  }

  let lastPlace = null; // { name, country, lat, lon }
  let forecastLoaded = false;

  function todayKey() {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}`;
  }

  function incDailySearch() {
    const key = "cempra_daily_searches_v1";
    const day = todayKey();

    let obj;
    try { obj = JSON.parse(localStorage.getItem(key) || "{}"); }
    catch { obj = {}; }

    if (obj.day !== day) obj = { day, count: 0 };
    obj.count = (obj.count || 0) + 1;
    localStorage.setItem(key, JSON.stringify(obj));
  }

  function pushHistory(q) {
    const key = "cempra_search_history_v1";

    let list;
    try { list = JSON.parse(localStorage.getItem(key) || "[]"); }
    catch { list = []; }

    const when = new Date().toLocaleString();
    list.unshift({ q, when });

    localStorage.setItem(key, JSON.stringify(list.slice(0, 10)));
  }

  function openMeteoDesc(weatherCode) {
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
    return map[weatherCode] || "Moti";
  }

  function setAnimByOpenMeteo(weatherCode, isDay) {
    if (!elWxAnim) return;

    elWxAnim.classList.remove("sunny", "cloudy", "rainy", "snowy");

    const rain = [51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82];
    const snow = [71, 73, 75, 77, 85, 86];
    const cloudy = [1, 2, 3, 45, 48];

    if (rain.includes(weatherCode)) return elWxAnim.classList.add("rainy");
    if (snow.includes(weatherCode)) return elWxAnim.classList.add("snowy");
    if (cloudy.includes(weatherCode)) return elWxAnim.classList.add("cloudy");
    if (weatherCode === 0) return elWxAnim.classList.add("sunny");

    elWxAnim.classList.add(isDay ? "sunny" : "cloudy");
  }

  function setThemeByOpenMeteo(weatherCode, isDay) {
    document.body.classList.remove(
      "theme-clear-day",
      "theme-clear-night",
      "theme-cloudy",
      "theme-rain",
      "theme-storm",
      "theme-snow",
      "theme-fog"
    );

    if (weatherCode === 0) return document.body.classList.add(isDay ? "theme-clear-day" : "theme-clear-night");
    if ([45, 48].includes(weatherCode)) return document.body.classList.add("theme-fog");
    if ([51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82].includes(weatherCode)) return document.body.classList.add("theme-rain");
    if ([71, 73, 75, 77, 85, 86].includes(weatherCode)) return document.body.classList.add("theme-snow");
    if ([95, 96, 99].includes(weatherCode)) return document.body.classList.add("theme-storm");

    document.body.classList.add("theme-cloudy");
  }

  function formatDayLabel(dateStr) {
    const d = new Date(dateStr + "T00:00:00");
    return d.toLocaleDateString(undefined, { weekday: "short", day: "2-digit", month: "short" });
  }

  function renderForecast3(days) {
    if (!elForecastGrid) return;
    elForecastGrid.innerHTML = "";

    days.forEach((day) => {
      const div = document.createElement("div");
      div.className = "forecast-item";
      div.innerHTML = `
        <div class="forecast-day">${formatDayLabel(day.date)}</div>
        <div class="forecast-temp">${Math.round(day.max)}° / ${Math.round(day.min)}°</div>
        <div class="forecast-sub">${openMeteoDesc(day.code)}</div>
      `;
      elForecastGrid.appendChild(div);
    });
  }

  async function loadForecast3() {
    if (!lastPlace) return;

    const url =
      `https://api.open-meteo.com/v1/forecast?latitude=${lastPlace.lat}&longitude=${lastPlace.lon}` +
      `&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto`;

    const res = await fetch(url);
    if (!res.ok) throw new Error("Forecast error");
    const data = await res.json();

    const times = data.daily.time;
    const codes = data.daily.weather_code;
    const tmax = data.daily.temperature_2m_max;
    const tmin = data.daily.temperature_2m_min;

    const days = [];
    for (let i = 0; i < 3 && i < times.length; i++) {
      days.push({ date: times[i], code: codes[i], max: tmax[i], min: tmin[i] });
    }

    renderForecast3(days);
    forecastLoaded = true;
  }

  function resetForecastUI() {
    forecastLoaded = false;
    if (elForecastPanel) elForecastPanel.setAttribute("aria-hidden", "true");
    if (elForecastGrid) elForecastGrid.innerHTML = "";
    if (elToggleForecast) elToggleForecast.textContent = "Shiko parashikimin 3-ditor";
    elCard.classList.remove("expanded");
  }

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
    return `${(place.name || "").trim().toLowerCase()}|${(place.country || "").trim().toLowerCase()}`;
  }

  function addFavoriteLocal(place) {
    let favorites = readFavorites();

    const key = normalizeKey(place);
    favorites = favorites.filter((x) => normalizeKey(x) !== key);

    favorites.unshift({
      name: place.name,
      country: place.country || "",
      lat: place.lat,
      lon: place.lon,
      addedAt: Date.now(),
    });

    favorites = favorites.slice(0, 3);
    writeFavorites(favorites);
  }

  async function searchCityWeather() {
    const q = elInput.value.trim();
    if (!q) return;

    try {
      resetForecastUI();

      const geoUrl = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(q)}&count=1&language=en&format=json`;
      const geoRes = await fetch(geoUrl);
      if (!geoRes.ok) throw new Error("Geocoding error");
      const geo = await geoRes.json();

      if (!geo.results || geo.results.length === 0) {
        elDesc.textContent = "Qyteti nuk u gjet.";
        return;
      }

      const place = geo.results[0];
      lastPlace = {
        name: place.name,
        country: place.country || "",
        lat: place.latitude,
        lon: place.longitude,
      };

      const weatherUrl =
        `https://api.open-meteo.com/v1/forecast?latitude=${lastPlace.lat}&longitude=${lastPlace.lon}` +
        `&current=temperature_2m,apparent_temperature,relative_humidity_2m,wind_speed_10m,is_day,weather_code&timezone=auto`;

      const wRes = await fetch(weatherUrl);
      if (!wRes.ok) throw new Error("Weather fetch error");
      const w = await wRes.json();

      const c = w.current;
      const temp = Math.round(c.temperature_2m);
      const feels = Math.round(c.apparent_temperature);
      const hum = Math.round(c.relative_humidity_2m);
      const wind = Math.round(c.wind_speed_10m);
      const isDay = c.is_day === 1;
      const code = c.weather_code;

      elCity.textContent = `${lastPlace.name}${lastPlace.country ? ", " + lastPlace.country : ""}`;
      elDesc.textContent = openMeteoDesc(code);
      elTemp.textContent = `${temp}°`;

      if (elWind) elWind.textContent = `Era: ${wind} km/h`;
      if (elHum) elHum.textContent = `Lagështia: ${hum}%`;
      if (elFeels) elFeels.textContent = `Ndjehet si: ${feels}°`;

      setThemeByOpenMeteo(code, isDay);
      setAnimByOpenMeteo(code, isDay);

      incDailySearch();
      pushHistory(elCity.textContent);
    } catch (err) {
      elDesc.textContent = "Problem me API. Provo përsëri.";
      console.error(err);
    }
  }

  elBtn.addEventListener("click", searchCityWeather);
  elInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") searchCityWeather();
  });

  if (elToggleForecast) {
    elToggleForecast.addEventListener("click", async () => {
      if (!lastPlace) {
        elDesc.textContent = "Së pari kërko një qytet, pastaj hap parashikimin!";
        return;
      }

      const isExpanded = elCard.classList.toggle("expanded");
      if (elForecastPanel) elForecastPanel.setAttribute("aria-hidden", String(!isExpanded));

      if (isExpanded && !forecastLoaded) {
        elToggleForecast.textContent = "Duke ngarkuar...";
        try {
          await loadForecast3();
          elToggleForecast.textContent = "Mbyll parashikimin";
        } catch (e) {
          if (elForecastGrid) elForecastGrid.innerHTML = "<div class='forecast-item'>S’u ngarkua parashikimi.</div>";
          elToggleForecast.textContent = "Mbyll parashikimin";
          console.error(e);
        }
      } else {
        elToggleForecast.textContent = isExpanded ? "Mbyll parashikimin" : "Shiko parashikimin 3-ditor";
      }
    });
  }

  if (elAddFav) {
    elAddFav.addEventListener("click", () => {
      if (!lastPlace) {
        elDesc.textContent = "Së pari kërko një qytet, pastaj shtoje te të preferuarat!";
        return;
      }
      const form = document.getElementById("favoriteAddForm");
      if (!form) {
        alert("favoriteAddForm mungon në HTML.");
        return;
      }

      const cityFull = `${lastPlace.name}${lastPlace.country ? ", " + lastPlace.country : ""}`;
      document.getElementById("favCityName").value = cityFull;

      document.getElementById("favLat").value = lastPlace.lat ?? "";
      document.getElementById("favLon").value = lastPlace.lon ?? "";

      form.submit(); // shkon te DB -> redirect te favorites.php
    });
  }
  elCity.textContent = "Kërko një qytet";
  elDesc.textContent = "Shkruaj emrin e qytetit në search dhe shtyp “Kërko” (ose Enter).";
  elTemp.textContent = "--°";
  if (elWind) elWind.textContent = "Era: -- km/h";
  if (elHum) elHum.textContent = "Lagështia: --%";
  if (elFeels) elFeels.textContent = "Ndjehet si: --°";
  document.body.classList.add("theme-cloudy");
});
