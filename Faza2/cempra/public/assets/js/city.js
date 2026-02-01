console.log("city.js loaded ✅");

document.addEventListener("DOMContentLoaded", async () => {
  const elWxAnim = document.getElementById("wxAnim");
  const elCity = document.getElementById("wCity");
  const elDesc = document.getElementById("wDesc");
  const elTemp = document.getElementById("wTemp");
  const elWind = document.getElementById("wWind");
  const elHum = document.getElementById("wHum");
  const elFeels = document.getElementById("wFeels");
  const elForecastGrid = document.getElementById("forecastGrid");

  const city = (window.CEMPRA_CITY || "").trim();
  console.log("CEMPRA_CITY =", city);

  if (!city) {
    if (elDesc) elDesc.textContent = "Nuk u dha qyteti në URL.";
    return;
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

  function setThemeByOpenMeteo(code, isDay) {
    document.body.classList.remove(
      "theme-clear-day","theme-clear-night","theme-cloudy","theme-rain","theme-storm","theme-snow","theme-fog"
    );
    if (code === 0) return document.body.classList.add(isDay ? "theme-clear-day" : "theme-clear-night");
    if ([45,48].includes(code)) return document.body.classList.add("theme-fog");
    if ([51,53,55,56,57,61,63,65,66,67,80,81,82].includes(code)) return document.body.classList.add("theme-rain");
    if ([71,73,75,77,85,86].includes(code)) return document.body.classList.add("theme-snow");
    if ([95,96,99].includes(code)) return document.body.classList.add("theme-storm");
    document.body.classList.add("theme-cloudy");
  }

  function setAnimByOpenMeteo(code, isDay) {
    if (!elWxAnim) return;
    elWxAnim.classList.remove("sunny","cloudy","rainy","snowy");
    const rain = [51,53,55,56,57,61,63,65,66,67,80,81,82];
    const snow = [71,73,75,77,85,86];
    const cloudy = [1,2,3,45,48];
    if (rain.includes(code)) return elWxAnim.classList.add("rainy");
    if (snow.includes(code)) return elWxAnim.classList.add("snowy");
    if (cloudy.includes(code)) return elWxAnim.classList.add("cloudy");
    if (code === 0) return elWxAnim.classList.add("sunny");
    elWxAnim.classList.add(isDay ? "sunny" : "cloudy");
  }

  function formatDayLabel(dateStr) {
    const d = new Date(dateStr + "T00:00:00");
    return d.toLocaleDateString(undefined, { weekday: "short", day: "2-digit", month: "short" });
  }

  function renderForecast3(times, codes, tmax, tmin) {
    if (!elForecastGrid) return;
    elForecastGrid.innerHTML = "";
    for (let i = 0; i < 3 && i < times.length; i++) {
      const card = document.createElement("div");
      card.className = "card";
      card.innerHTML = `
        <div class="card-title">${formatDayLabel(times[i])}</div>
        <div class="card-temp">${Math.round(tmax[i])}° / ${Math.round(tmin[i])}°</div>
        <div class="card-sub">${openMeteoDesc(codes[i])}</div>
      `;
      elForecastGrid.appendChild(card);
    }
  }

  try {
    const geoUrl = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(city)}&count=1&language=en&format=json`;
    const geoRes = await fetch(geoUrl);
    const geo = await geoRes.json();
    if (!geo.results || geo.results.length === 0) {
      if (elDesc) elDesc.textContent = "Qyteti nuk u gjet.";
      return;
    }

    const place = geo.results[0];

    const url =
      `https://api.open-meteo.com/v1/forecast?latitude=${place.latitude}&longitude=${place.longitude}` +
      `&current=temperature_2m,apparent_temperature,relative_humidity_2m,wind_speed_10m,is_day,weather_code` +
      `&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto`;

    const res = await fetch(url);
    const data = await res.json();

    const c = data.current;
    const temp = Math.round(c.temperature_2m);
    const feels = Math.round(c.apparent_temperature);
    const hum = Math.round(c.relative_humidity_2m);
    const wind = Math.round(c.wind_speed_10m);
    const isDay = c.is_day === 1;
    const code = c.weather_code;

    if (elCity) elCity.textContent = `${place.name}${place.country ? ", " + place.country : ""}`;
    if (elDesc) elDesc.textContent = `Sot · ${openMeteoDesc(code)}`;
    if (elTemp) elTemp.textContent = `${temp}°`;
    if (elFeels) elFeels.textContent = `Ndjehet si: ${feels}°`;
    if (elWind) elWind.textContent = `Era: ${wind} km/h`;
    if (elHum) elHum.textContent = `Lagështia: ${hum}%`;

    setThemeByOpenMeteo(code, isDay);
    setAnimByOpenMeteo(code, isDay);

    renderForecast3(
      data.daily.time || [],
      data.daily.weather_code || [],
      data.daily.temperature_2m_max || [],
      data.daily.temperature_2m_min || []
    );
  } catch (e) {
    console.error(e);
    if (elDesc) elDesc.textContent = "Problem me API. Provo përsëri.";
  }
});
