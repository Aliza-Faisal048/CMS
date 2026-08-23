<script src="../js/bootstrap.bundle.min.js"></script>
<script>

document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebarOverlay = document.getElementById("sidebarOverlay");


    function openSidebar() {

        sidebar.classList.add("open");
        sidebarOverlay.classList.add("show");

        sidebarToggle.innerHTML =
            '<i class="fas fa-times"></i>';
    }


    function closeSidebar() {

        sidebar.classList.remove("open");
        sidebarOverlay.classList.remove("show");

        sidebarToggle.innerHTML =
            '<i class="fas fa-bars"></i>';
    }


    sidebarToggle.addEventListener("click", function () {

        if (sidebar.classList.contains("open")) {
            closeSidebar();
        } else {
            openSidebar();
        }

    });


    sidebarOverlay.addEventListener("click", function () {
        closeSidebar();
    });


    /* Close sidebar after clicking a menu item on mobile */

    sidebar.querySelectorAll("a").forEach(function (link) {

        link.addEventListener("click", function () {

            if (window.innerWidth <= 768) {
                closeSidebar();
            }

        });

    });


    /* Reset sidebar when returning to desktop */

    window.addEventListener("resize", function () {

        if (window.innerWidth > 768) {

            sidebar.classList.remove("open");
            sidebarOverlay.classList.remove("show");

            sidebarToggle.innerHTML =
                '<i class="fas fa-bars"></i>';

        }

    });

});

</script>
</body>

</html>
