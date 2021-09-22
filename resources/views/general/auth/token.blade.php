<script>
    (function(){
        var token = "{{ $token }}";
        var role = "{{ $role }}"
        window.opener.postMessage({token: token, role: role}, "{{ env('APP_DASHBOARD_DOMAIN') }}");
    })()

</script>