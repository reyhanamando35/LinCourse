@if (Session::has('success'))
    <script>
        Swal.fire({
            title: "Success!",
            text: @json(Session::get('success')),
            icon: "success"
        });
    </script>
@endif
@if (Session::has('error'))
    <script>
        Swal.fire({
            title: "Ooops!",
            text: @json(Session::get('error')),
            icon: "error"
        });
    </script>
@endif
@if ($errors->any())
    <script>
        Swal.fire({
            title: "Ooops!",
            text: @json($errors->first()),
            icon: "error"
        });
    </script>
@endif
