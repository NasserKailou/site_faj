    </div><!-- .admin-content -->
</main><!-- .admin-main -->

<script>
function toggleSidebar() {
    document.getElementById('adminSidebar').classList.toggle('collapsed');
    document.getElementById('adminMain').classList.toggle('expanded');
}

// Auto-close alerts
setTimeout(() => {
    document.querySelectorAll('.auto-dismiss').forEach(el => el.remove());
}, 5000);
</script>
</body>
</html>
