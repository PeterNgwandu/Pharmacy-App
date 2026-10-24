<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loader = document.getElementById('pageLoader');

        if (loader) {
            loader.classList.add('hidden');
        }
    });

    window.addEventListener('beforeunload', function () {
        const loader = document.getElementById('pageLoader');

        if (loader) {
            loader.classList.remove('hidden');
        }
    });
</script>