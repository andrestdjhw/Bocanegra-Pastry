    </main>

    <!-- Footer React mount point (el contenido interno es el fallback sin JS / para buscadores) -->
    <div id="render-footer-here">
      <?php get_template_part('template-parts/footer-fallback'); ?>
    </div>

    <!-- Aviso y panel de cookies (React: src/scripts/CookieConsent.js) -->
    <div id="render-cookie-consent-here"></div>

    <?php wp_footer(); ?>
  </body>
</html>
