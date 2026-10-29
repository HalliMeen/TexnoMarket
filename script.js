document.addEventListener("DOMContentLoaded", () => {
    // Анімація відповідає за плавний вхід усіх блоків сторінки
    document.body.classList.add("page-ready");

    // Анімація відповідає за появу карточок товарів (article)
    const cards = document.querySelectorAll("article");

    cards.forEach((card, index) => {
        card.animate(
            [
                { opacity: 0, transform: "translateY(20px)" },
                { opacity: 1, transform: "translateY(0)" }
            ],
            {
                duration: 700,
                delay: index * 60,
                easing: "ease-out",
                fill: "forwards"
            }
        );
    });

    // Анімація відповідає за кнопку "На головну" (.home-button)
    const homeButton = document.querySelector(".home-button");

    if (homeButton) {
        const floatAnimation = homeButton.animate(
            [
                { transform: "translateY(0)" },
                { transform: "translateY(-10px)" },
                { transform: "translateY(0)" }
            ],
            {
                duration: 1800,
                easing: "ease-in-out",
                iterations: Infinity
            }
        );

        homeButton.addEventListener("mouseenter", () => {
            floatAnimation.pause();
            homeButton.style.transform = "scale(1.08)";
        });

        homeButton.addEventListener("mouseleave", () => {
            homeButton.style.transform = "";
            floatAnimation.play();
        });

        homeButton.addEventListener("mousedown", () => {
            homeButton.style.transform = "scale(0.95)";
        });

        homeButton.addEventListener("mouseup", () => {
            homeButton.style.transform = "scale(1.08)";
        });

        // Анімація відповідає за плавний перехід при натисканні на кнопку "На головну"
        homeButton.addEventListener("click", (event) => {
            const href = homeButton.getAttribute("href");
            if (!href || href.startsWith("#")) return;

            event.preventDefault();
            document.body.classList.add("page-exit");
            setTimeout(() => {
                window.location.href = href;
            }, 220);
        });
    }

    // Анімація відповідає за плавний перехід між усіма внутрішніми сторінками сайту
    document.querySelectorAll("a[href]").forEach((link) => {
        const href = link.getAttribute("href");
        if (!href || href.startsWith("#") || href.startsWith("mailto:") || href.startsWith("tel:") || /^https?:\/\//i.test(href)) {
            return;
        }

        link.addEventListener("click", (event) => {
            if (link.classList.contains("home-button")) return;

            event.preventDefault();
            document.body.classList.add("page-exit");
            setTimeout(() => {
                window.location.href = href;
            }, 220);
        });
    });
});