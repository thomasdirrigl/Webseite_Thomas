// TODO: eigene Google Analytics Measurement ID eintragen (Format G-XXXXXXXXXX)
const GA_MEASUREMENT_ID = "G-XXXXXXXXXX";

document.addEventListener("DOMContentLoaded", () => {
  setupNavToggle();
  setupCookieBanner();
  setupContactForm();
});

function setupNavToggle() {
  const toggle = document.getElementById("navToggle");
  const links = document.getElementById("navLinks");
  if (!toggle || !links) return;

  toggle.addEventListener("click", () => {
    const isOpen = links.classList.toggle("open");
    toggle.setAttribute("aria-expanded", String(isOpen));
  });

  links.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      links.classList.remove("open");
      toggle.setAttribute("aria-expanded", "false");
    });
  });
}

function setupCookieBanner() {
  const banner = document.getElementById("cookieBanner");
  if (!banner) return;

  const consent = localStorage.getItem("cookie_consent");

  if (consent === "accepted") {
    loadGoogleAnalytics();
  } else if (consent !== "declined") {
    banner.classList.add("visible");
  }

  const acceptBtn = document.getElementById("cookieAccept");
  const declineBtn = document.getElementById("cookieDecline");

  acceptBtn?.addEventListener("click", () => {
    localStorage.setItem("cookie_consent", "accepted");
    banner.classList.remove("visible");
    loadGoogleAnalytics();
  });

  declineBtn?.addEventListener("click", () => {
    localStorage.setItem("cookie_consent", "declined");
    banner.classList.remove("visible");
  });
}

function loadGoogleAnalytics() {
  if (!GA_MEASUREMENT_ID || GA_MEASUREMENT_ID.includes("XXXX")) return;
  if (window.gtagLoaded) return;
  window.gtagLoaded = true;

  const script = document.createElement("script");
  script.async = true;
  script.src = `https://www.googletagmanager.com/gtag/js?id=${GA_MEASUREMENT_ID}`;
  document.head.appendChild(script);

  window.dataLayer = window.dataLayer || [];
  function gtag() {
    window.dataLayer.push(arguments);
  }
  window.gtag = gtag;
  gtag("js", new Date());
  gtag("config", GA_MEASUREMENT_ID, { anonymize_ip: true });
}

function setupContactForm() {
  const form = document.getElementById("contactForm");
  const status = document.getElementById("formStatus");
  if (!form || !status) return;

  const loadedAt = Date.now();

  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    status.className = "form-status";
    status.textContent = "";

    const formData = new FormData(form);
    // Fuer die Zeitsperre in contact.php
    formData.append("elapsed", String(Math.floor((Date.now() - loadedAt) / 1000)));

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: formData,
        headers: { Accept: "application/json" },
      });

      if (response.ok) {
        status.textContent = "Danke für deine Nachricht! Ich melde mich zeitnah bei dir.";
        status.className = "form-status success";
        form.reset();
      } else {
        throw new Error("Serverfehler");
      }
    } catch (err) {
      status.textContent = "Beim Senden ist ein Fehler aufgetreten. Bitte versuche es später erneut oder schreib mir direkt eine E-Mail.";
      status.className = "form-status error";
    }
  });
}
