import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

// Stop double submits: lock a form's buttons after the first submit.
document.addEventListener("submit", (event) => {
    if (event.defaultPrevented) return;

    const form = event.target;

    if (form.dataset.submitted) {
        event.preventDefault();
        return;
    }

    form.dataset.submitted = "1";

    // Wait a tick so the clicked button's value is still sent.
    setTimeout(() => {
        form.querySelectorAll(
            "button[type=submit], button:not([type])",
        ).forEach((b) => {
            b.disabled = true;
            b.classList.add("opacity-60", "cursor-wait");
        });
    }, 0);
});

// Unlock again if the browser restores the page with the Back button.
window.addEventListener("pageshow", () => {
    document.querySelectorAll("form[data-submitted]").forEach((form) => {
        delete form.dataset.submitted;
        form.querySelectorAll("button").forEach((b) => {
            b.disabled = false;
            b.classList.remove("opacity-60", "cursor-wait");
        });
    });
});
