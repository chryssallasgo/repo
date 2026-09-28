const menuToggle = document.querySelector(".menu-toggle");
const siteNav = document.querySelector(".site-nav");
const signupForm = document.querySelector(".signup-form");
const formMessage = document.querySelector(".form-message");

menuToggle?.addEventListener("click", () => {
  const isOpen = siteNav.classList.toggle("is-open");
  menuToggle.setAttribute("aria-expanded", String(isOpen));
  menuToggle.setAttribute("aria-label", isOpen ? "Close menu" : "Open menu");
});

siteNav?.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => {
    siteNav.classList.remove("is-open");
    menuToggle?.setAttribute("aria-expanded", "false");
    menuToggle?.setAttribute("aria-label", "Open menu");
  });
});

signupForm?.addEventListener("submit", (event) => {
  event.preventDefault();
  const email = new FormData(signupForm).get("email");
  if (typeof email !== "string" || !email.includes("@")) return;
  formMessage.textContent = "You're on the list — thank you.";
  signupForm.reset();
});

document.querySelector("#year").textContent = new Date().getFullYear();
