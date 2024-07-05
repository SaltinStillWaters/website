<head>
    <link rel="stylesheet" href="css/base/base.css">
    <link rel="stylesheet" href="css/layout/header.css">
    <link rel="stylesheet" href="css/features/slideshow.css">
    
</head>

<body>
    <header>
        <a href="#" class="logo">ml companion</a>
        <ul>
            <li><a href="#">Strategy Guides</a></li>
            <li><a href="#">Hero Rankings</a></li>
            <li><a href="#">Counter Picking</a></li>
            <div class="logout">                
                <li><a href="logout.php">Log out</a></li>
            </div>
        </ul>
    </header>

    <div class="slider-frame">
        <div class="slide-images">
            <img src="resources/welcome/news/proQ.jpg" alt="Slide 1">
            <img src="resources/welcome/news/zhuxin.jpg" alt="Slide 2">
            <img src="resources/welcome/news/starlight.jpg" alt="Slide 3">
            <img src="resources/welcome/news/recharge.jpg" alt="Slide 4">
            <img src="resources/welcome/news/proQ.jpg" alt="Slide 1">
        </div>
    </div>

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
