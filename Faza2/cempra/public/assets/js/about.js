document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("contactForm");
  if (!form) return;

  const nameEl = document.getElementById("contactName");
  const emailEl = document.getElementById("contactEmail");
  const msgEl = document.getElementById("contactMsg");

  const errName = document.getElementById("errName");
  const errEmail = document.getElementById("errEmail");
  const errMsg = document.getElementById("errMsg");
  const success = document.getElementById("successMsg");

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i;

  function setError(el, errEl, message) {
    errEl.textContent = message || "";
    el.classList.toggle("input-error", Boolean(message));
  }

  function clearAll() {
    setError(nameEl, errName, "");
    setError(emailEl, errEmail, "");
    setError(msgEl, errMsg, "");
    if (success) success.textContent = "";
  }

  function validate() {
    clearAll();
    let ok = true;

    const name = (nameEl?.value || "").trim();
    const email = (emailEl?.value || "").trim();
    const msg = (msgEl?.value || "").trim();

    if (name.length < 2) {
      setError(nameEl, errName, "Shkruaj emrin (min 2 karaktere).");
      ok = false;
    }

    if (!emailRegex.test(email)) {
      setError(emailEl, errEmail, "Shkruaj një email të saktë (p.sh. test@gmail.com).");
      ok = false;
    }

    if (msg.length < 10) {
      setError(msgEl, errMsg, "Mesazhi duhet të ketë të paktën 10 karaktere.");
      ok = false;
    }

    return ok;
  }

  [nameEl, emailEl, msgEl].forEach((el) => {
    if (!el) return;
    el.addEventListener("blur", () => validate());
    el.addEventListener("input", () => {
      if (el.classList.contains("input-error")) validate();
    });
  });

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    if (!validate()) return;

    form.submit();
  });
});
