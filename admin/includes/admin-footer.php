            </div>
            <!-- End Main Content -->
        </div>
        <!-- End Content Wrapper -->
    </div>
    <!-- End Wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('collapsed');
                $('#content-wrapper').toggleClass('expanded');
            });
            
            // Auto close alerts after 5 seconds
            setTimeout(function() {
                $('.alert-dismissible').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Delete confirmation
            $('.delete-confirm').on('click', function(e) {
                if(!confirm('Bạn có chắc chắn muốn xóa mục này? Hành động này không thể hoàn tác.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
