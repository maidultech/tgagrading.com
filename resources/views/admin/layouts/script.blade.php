<script src="{{ asset('assets/js/jquery.min.js') }}"></script>

{{-- dataTables --}}
<script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.html5.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
{{-- <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script> --}}
<script src="{{ asset('assets/plugins/datatables-buttons/js/jszip.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/pdfmake.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/vfs_fonts.js')}}"></script>

<!-- Bootstrap 4 -->
<script src="{{ asset('assets/adminlte/bootstrap4/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="https://adminlte.io/themes/v3/plugins/summernote/summernote-bs4.min.js"></script>
<script src="{{ asset('assets/adminlte/js/adminlte.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.getElementById('logoutLink').addEventListener('click', function (e) {
        e.preventDefault();
        this.classList.add('disabled');
        this.style.pointerEvents = 'none';
        document.getElementById('logout-form').submit();
    }, { once: true }); // Prevents multiple submissions
</script>


