<script src="../js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const menuBtn = document.getElementById("navbarMenuBtn");
    const sidebar = document.querySelector(".sidebar");

    if (!menuBtn || !sidebar) return;

    /* Create overlay */
    const overlay = document.createElement("div");
    overlay.className = "sidebar-overlay";
    document.body.appendChild(overlay);

    /* Open / close sidebar */
    menuBtn.addEventListener("click", function () {

        sidebar.classList.toggle("show");
        overlay.classList.toggle("show");

        const icon = menuBtn.querySelector("i");

        if (sidebar.classList.contains("show")) {
            icon.classList.remove("fa-bars");
            icon.classList.add("fa-times");
        } else {
            icon.classList.remove("fa-times");
            icon.classList.add("fa-bars");
        }
    });

    /* Close when overlay is clicked */
    overlay.addEventListener("click", function () {

        sidebar.classList.remove("show");
        overlay.classList.remove("show");

        const icon = menuBtn.querySelector("i");

        icon.classList.remove("fa-times");
        icon.classList.add("fa-bars");
    });

    /* Close sidebar after clicking a menu link */
    const sidebarLinks = sidebar.querySelectorAll("a");

    sidebarLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            if (window.innerWidth <= 768) {

                sidebar.classList.remove("show");
                overlay.classList.remove("show");

                const icon = menuBtn.querySelector("i");

                icon.classList.remove("fa-times");
                icon.classList.add("fa-bars");
            }

        });

    });

    /* Reset sidebar when returning to desktop */
    window.addEventListener("resize", function () {

        if (window.innerWidth > 768) {

            sidebar.classList.remove("show");
            overlay.classList.remove("show");

            const icon = menuBtn.querySelector("i");

            icon.classList.remove("fa-times");
            icon.classList.add("fa-bars");
        }

    });

});
</script>
</body>

</html>