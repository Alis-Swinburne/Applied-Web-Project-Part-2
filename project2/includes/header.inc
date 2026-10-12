<?php

// Set these variables below in each page depending on the content, e.g:
// $page_title = title of the page
// $page_description = related description
// $page_keywords = any keywords related to the page


// Default site wide settings/variables
$site_name = "UPDATE COMPANY NAME";
$site_author = "Juliette Grove, Ada Yong, Andrea McPadden, Alisla Foley";
$default_description = "Secure and user-friendly financial technology solutions for digital payments, budgeting, account management, and financial education.";
$default_keywords = "UPDATE COMPANY NAME, FinTech, financial technology, digital payments, online payments, personal finance, budgeting tools, money management, account management, financial education, financial security";


$title = $page_title ?? $site_name;
$description = $page_description ?? $default_description;
$keywords = $page_keywords ?? $default_keywords;
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="<?php echo htmlspecialchars($site_author); ?>">
        <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
        <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
        <link rel="stylesheet" href="styles/styles.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&family=Unica+One&display=swap">
        <script src="https://kit.fontawesome.com/8b43896124.js" crossorigin="anonymous"></script>
        <title><?php echo htmlspecialchars($title); ?></title>
    </head>