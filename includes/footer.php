<!-- Bootstrap JavaScript -->

<script
    src="../js/bootstrap.bundle.min.js">
</script>


<!-- Our JavaScript -->

<script src="js/script.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const menuBtn = document.getElementById("mobileMenuBtn");
    const sidebar = document.querySelector(".sidebar");

    if (menuBtn && sidebar) {

        menuBtn.addEventListener("click", function () {

            sidebar.classList.toggle("mobile-open");

        });

    }

});
</script>

</body>

</html>

