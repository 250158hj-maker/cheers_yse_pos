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
    
    <footer class="main-footer" style="text-align: center; padding: 20px 0; color: #777; font-size: 0.8rem;">
        <div class="footer-container">
            <p class="copyright"><?php echo htmlspecialchars($copyRight); ?></p>
        </div>
    </footer>

    <!-- 共通JavaScriptがあればここに記述 -->
</body>
</html>
