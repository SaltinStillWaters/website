<head>
    <link rel="stylesheet" href="css/layout/header.css">
    <link rel="stylesheet" href="css/base/base.css">
</head>
<body>
    <header>
        <a href="#" class="logo">ml companion</a>
        <ul>
            <li><a href="#">Strategy Guides</a></li>
            <li><a href="#">Hero Rankings</a></li>
            <li><a href="#">Counter Picking</a></li>
            <li><a href="logout.php">Log out</a></li>
        </ul>
    </header>
        
    <script type="text/javascript">
    window.addEventListener("scroll", function() {
        var header = document.querySelector("header");
        header.classList.toggle("sticky", window.scrollY > 0);
    });
</script>
</body>




<?php
exit();

session_start();
//$_SESSION = [];

header('Location: frontend/login.php');
exit();
