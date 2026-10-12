<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-full focus:bg-chocolate focus:px-5 focus:py-3 focus:text-sm focus:font-bold focus:text-cream">Skip to content</a>

    <!-- Topbar React mount point: contacto + redes (no es sticky; el navbar sí) -->
    <div id="render-topbar-here">
      <?php get_template_part('template-parts/topbar-fallback'); ?>
    </div>

    <!-- Navbar React mount point (el contenido interno es el fallback sin JS / para buscadores) -->
    <div id="render-navbar-here" class="bn-sticky sticky top-0 z-50">
      <?php get_template_part('template-parts/navbar-fallback'); ?>
    </div>

    <main id="main-content">
