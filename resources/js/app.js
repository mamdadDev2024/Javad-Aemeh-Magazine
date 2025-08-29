import "./bootstrap";
import Swiper from "swiper/bundle";
import "swiper/css/bundle";
import AOS from 'aos';
import 'aos/dist/aos.css';
AOS.init();
const initMainSwiper = () => {
    const newsSwiperContainer = document.querySelector(".news-swiper-container");
    if (!newsSwiperContainer) return;

    new Swiper(".news-swiper-container", {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        scrollbar: {
            el: ".swiper-scrollbar",
        },
        autoplay: {
            delay: 2500
        },
        breakpoints: {
            1200: {
                slidesPerView: 4
            },
            1024: {
                slidesPerView: 3,
            },
            800: {
                slidesPerView: 2,
            },
            100: {
                slidesPerView: 1,
            },
        },
    });
};


const initEventSwiper = () => {
    const eventSwiperContainer = document.querySelector(".event-swiper-container");
    if (!eventSwiperContainer) return;

    new Swiper(".event-swiper-container", {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        scrollbar: {
            el: ".swiper-scrollbar",
        },
        autoplay:{
            delay:2500
        },
        breakpoints: {
            1200: {
                slidesPerView: 4
            },
            1024: {
                slidesPerView: 3,
            },
            800: {
                slidesPerView: 2,
            },
            100: {
                slidesPerView: 1,
            },
        },
    });
};

const initMagazineSwiper = () => {
    const magazineSwiperContainer = document.querySelector(".magazine-swiper-container");
    if (!magazineSwiperContainer) return;

    new Swiper(".magazine-swiper-container", {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        scrollbar: {
            el: ".swiper-scrollbar",
        },
        autoplay: {
            delay: 2500
        },
        breakpoints: {
            1024: {
                slidesPerView: 3,
            },
            800: {
                slidesPerView: 2,
            },
            100: {
                slidesPerView: 1,
            },
        },
    });
};

if (location.pathname === "/" || location.pathname === "") {
    initMainSwiper();
    initEventSwiper();
    initMagazineSwiper();
}

document.addEventListener("DOMContentLoaded", () => {
    const htmlTag = document.documentElement;
    const menuButton = document.querySelector("#menu_button");
    const menuSection = document.querySelector("#menu_section");
    let hideMenuTimeout;
    function toggleDarkMode() {
        const isDark = htmlTag.classList.toggle("dark");
        localStorage.setItem("theme", isDark ? "dark" : "light");
        updateButtonText(isDark);
    }

    function updateButtonText(isDark) {
        const text = isDark ? "روشن" : "تاریک";
        document
            .querySelectorAll("#btn_toggle_drkmd, #btn_toggle_drkmd_2")
            .forEach((btn) => (btn.textContent = text));
    }

    function applySavedTheme() {
        const savedTheme = localStorage.getItem("theme");
        if (savedTheme === "dark") {
            htmlTag.classList.add("dark");
            updateButtonText(true);
        } else {
            htmlTag.classList.remove("dark");
            updateButtonText(false);
        }
    }
    document.querySelectorAll("#btn_toggle_drkmd").forEach((btn) => {
        btn.addEventListener("click", toggleDarkMode);
    });
    applySavedTheme();
    const loginBtn = document.getElementById("login_btn");
    if (loginBtn) {
        loginBtn.addEventListener(
            "click",
            () => (window.location.href = "/login")
        );
    }

    const likeBtn = document.querySelector("#like_btn");
    if (likeBtn) {
        likeBtn.addEventListener("click", () =>
            likeBtn.classList.toggle("bg-red-400")
        );
    }

    function showMenu() {
        clearTimeout(hideMenuTimeout);
        menuSection.classList.remove("opacity-0", "invisible");
        menuSection.classList.add("opacity-100");
    }

    function hideMenu() {
        menuSection.classList.remove("opacity-100");
        menuSection.classList.add("opacity-0", "invisible");
    }

    function hideMenuWithDelay() {
        hideMenuTimeout = setTimeout(hideMenu, 1000);
    }

    function toggleMenu() {
        if (menuSection.classList.contains("opacity-0")) {
            showMenu();
        } else {
            hideMenu();
        }
    }

    const isTouchDevice = "ontouchstart" in window || navigator.maxTouchPoints > 0;

    if (menuButton && menuSection) {
        if (isTouchDevice) {
            menuButton.addEventListener("touchstart", (e) => {
                e.preventDefault();
                toggleMenu();
            });
        } else {
            menuButton.addEventListener("click", toggleMenu);
            menuButton.addEventListener("mouseenter", showMenu);
            menuSection.addEventListener("mouseenter", showMenu);
            menuButton.addEventListener("mouseleave", hideMenuWithDelay);
            menuSection.addEventListener("mouseleave", hideMenuWithDelay);
        }

        document.addEventListener("click", (event) => {
            if (
                !menuButton.contains(event.target) &&
                !menuSection.contains(event.target)
            ) {
                hideMenu();
            }
        });
    }
});
