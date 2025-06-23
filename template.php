<?php
   if (!function_exists('base_url')) {
    function base_url($path = '') {
        return "http://" . $_SERVER['HTTP_HOST'] . "/tugas_online/" . ltrim($path, '/');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css'); ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');

:root{
    --primary-color: #6a5acd; 
      --text-color: #ffffff; 
      --secondary: #e0e0e0; 
      --dark-purple-hover: #403385; 
      --light-blue: #ADD8E6; 
      --dark-blue: #4682B4; 
    --black-text: #3d3b41;
}

*{
    font-family: 'Ubuntu', Arial, Helvetica, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    
}
.body{
    background-color: var(--text-color);
}
</style>
</head>
<body class="body">
    
<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ;?>"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>