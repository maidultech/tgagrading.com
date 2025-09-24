<script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/main.min.js') }}"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<!-- Responsive extension -->
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

<script>
    // Check if logoutLink-desktop exists before adding the event listener
    var logoutLinkDesktop = document.getElementById('logoutLink-desktop');
    if (logoutLinkDesktop) {
        logoutLinkDesktop.addEventListener('click', function (e) {
            e.preventDefault();
            this.classList.add('disabled');
            this.style.pointerEvents = 'none';
            document.getElementById('logout-form-desktop').submit();
        }, { once: true }); // Prevents multiple submissions
    }

    // Check if logoutLink-mobile exists before adding the event listener
    var logoutLinkMobile = document.getElementById('logoutLink-mobile');
    if (logoutLinkMobile) {
        logoutLinkMobile.addEventListener('click', function (e) {
            e.preventDefault();
            this.classList.add('disabled');
            this.style.pointerEvents = 'none';
            document.getElementById('logoutLink-mobile').submit();
        }, { once: true }); // Prevents multiple submissions
    }

    // Check if logoutLink exists before adding the event listener
    var logoutLink = document.getElementById('logoutLink');
    if (logoutLink) {
        logoutLink.addEventListener('click', function (e) {
            e.preventDefault();
            this.classList.add('disabled');
            this.style.pointerEvents = 'none';
            document.getElementById('logout-form').submit();
        }, { once: true }); // Prevents multiple submissions
    }
</script>
