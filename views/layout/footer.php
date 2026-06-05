<?php
/**
 * footer.php
 * 
 * --- 処理部 (Processing) ---
 */
$currentYear = date('Y');
$copyRight = "© {$currentYear} Cheers YSE POS All Rights Reserved.";
?>

<!-- 
  --- 描画部 (Rendering) ---
-->
    </main> <!-- .container end -->
    
    <footer class="mt-5 py-4 border-top text-center text-muted small">
        <div class="container">
            <p class="mb-0"><?= h($copyRight) ?></p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
