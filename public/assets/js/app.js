document.querySelectorAll("[data-print]").forEach((button) => {
    button.addEventListener("click", () => window.print());
});

document.querySelectorAll("form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", (event) => {
        const message = form.getAttribute("data-confirm") || "Are you sure?";
        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });
});

const lightbox = document.querySelector("[data-lightbox]");
if (lightbox) {
    const image = lightbox.querySelector("img");
    const caption = lightbox.querySelector("p");
    document.querySelectorAll("[data-full]").forEach((link) => {
        link.addEventListener("click", (event) => {
            event.preventDefault();
            image.src = link.getAttribute("data-full");
            image.alt = link.getAttribute("data-alt") || "Gallery image";
            caption.textContent = link.getAttribute("data-caption") || "";
            lightbox.classList.add("open");
        });
    });
    lightbox.addEventListener("click", (event) => {
        if (event.target === lightbox || event.target.hasAttribute("data-close")) {
            lightbox.classList.remove("open");
            image.src = "";
        }
    });
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            lightbox.classList.remove("open");
        }
    });
}
