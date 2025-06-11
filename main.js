// Smooth scrolling and offset for fixed header
document.addEventListener("DOMContentLoaded", function () {
  const links = document.querySelectorAll(".nav__links a");

  links.forEach(link => {
    link.addEventListener("click", function (e) {
      e.preventDefault(); // Stop default anchor jump

      const targetId = this.getAttribute("href").substring(1); // Remove #
      const targetSection = document.getElementById(targetId);

      if (targetSection) {
        const header = document.querySelector("header");
        const headerOffset = header.offsetHeight; // Dynamic header height
        const elementPosition = targetSection.offsetTop;
        const offsetPosition = elementPosition - headerOffset;

        window.scrollTo({
          top: offsetPosition,
          behavior: "smooth"
        });
      }
    });
  });

  // Book Now button logic
  const bookButtons = document.querySelectorAll(".room__btn");
  const output = document.getElementById("booking-details");

  bookButtons.forEach(button => {
    button.addEventListener("click", function () {
      const roomCard = this.closest(".room__card");
      const title = roomCard.dataset.title;
      const price = roomCard.dataset.price;
      const desc = roomCard.dataset.desc;

      output.innerHTML = `
        <div class="booking__card">
          <h2>${title}</h2>
          <p><strong>Price:</strong> ${price}</p>
          <p><strong>Details:</strong> ${desc}</p>
        </div>
      `;

      output.scrollIntoView({ behavior: "smooth" });
    });
  });
});

const headerOffset = document.querySelector("header").offsetHeight;

// Menu toggle logic
const menuBtn = document.getElementById("menu-btn");
const navLinks = document.getElementById("nav-links");
const menuBtnIcon = menuBtn.querySelector("i");

menuBtn.addEventListener("click", () => {
  navLinks.classList.toggle("open");

  const isOpen = navLinks.classList.contains("open");
  menuBtnIcon.setAttribute("class", isOpen ? "ri-close-line" : "ri-menu-line");
});

navLinks.addEventListener("click", () => {
  navLinks.classList.remove("open");
  menuBtnIcon.setAttribute("class", "ri-menu-line");
});

// ScrollReveal configuration
const scrollRevealOption = {
  distance: "50px",
  origin: "bottom",
  duration: 1000,
};

// Header container animation
ScrollReveal().reveal(".header__container p", {
  ...scrollRevealOption,
});

ScrollReveal().reveal(".header__container h1", {
  ...scrollRevealOption,
  delay: 500,
});

// About container animations
ScrollReveal().reveal(".about__image img", {
  ...scrollRevealOption,
  origin: "left",
});

ScrollReveal().reveal(".about__content .section__subheader", {
  ...scrollRevealOption,
  delay: 500,
});

ScrollReveal().reveal(".about__content .section__header", {
  ...scrollRevealOption,
  delay: 1000,
});

ScrollReveal().reveal(".about__content .section__description", {
  ...scrollRevealOption,
  delay: 1500,
});

ScrollReveal().reveal(".about__btn", {
  ...scrollRevealOption,
  delay: 2000,
});

// Room container animation
ScrollReveal().reveal(".room__card", {
  ...scrollRevealOption,
  interval: 500,
});

// Service container animation
ScrollReveal().reveal(".service__list li", {
  ...scrollRevealOption,
  interval: 500,
  origin: "right",
});
