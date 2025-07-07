<?php
// Meta Tags SEO
?>
<meta name="description" content="<?php echo isset($page_description) ? $page_description : 'GymForge - Plataforma completa para gerenciamento de academias e acompanhamento de treinos. Transforme sua jornada fitness.'; ?>">
<meta name="keywords" content="academia, treinos, exercícios, fitness, saúde, GymForge, musculação, personal trainer">
<meta name="author" content="GymForge">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>assets/img/gymforge-badge.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>assets/img/gymforge-badge.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_URL; ?>assets/img/gymforge-badge.png">
<link rel="manifest" href="<?php echo BASE_URL; ?>site.webmanifest">
<meta name="theme-color" content="#1A1A1A">

<!-- Open Graph -->
<meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>GymForge">
<meta property="og:description" content="<?php echo isset($page_description) ? $page_description : 'Transforme sua jornada fitness com o GymForge.'; ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI']; ?>">
<meta property="og:image" content="<?php echo BASE_URL; ?>assets/img/gymforge-logo.jpeg">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>GymForge">
<meta name="twitter:description" content="<?php echo isset($page_description) ? $page_description : 'Transforme sua jornada fitness com o GymForge.'; ?>">
<meta name="twitter:image" content="<?php echo BASE_URL; ?>assets/img/gymforge-logo.jpeg">

<!-- Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "GymForge",
    "description": "Plataforma completa para gerenciamento de academias e acompanhamento de treinos",
    "url": "<?php echo BASE_URL; ?>",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo BASE_URL; ?>search?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>

<!-- Mobile Meta -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="GymForge">

<!-- PWA Meta -->
<meta name="application-name" content="GymForge">
<meta name="msapplication-TileColor" content="#1A1A1A">
<meta name="msapplication-config" content="<?php echo BASE_URL; ?>browserconfig.xml"> 