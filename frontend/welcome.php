<head>
    <link rel="stylesheet" href="../css/base/base.css">
    <link rel="stylesheet" href="../css/layout/header.css">
    <link rel="stylesheet" href="../css/features/slideshow.css">
    <link rel="stylesheet" href="../css/pages/welcome.css">
    <link rel="stylesheet" href="../css/layout/footer.css">
    
</head>

<body>
    <div class="blk_bg">

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
        
        <div class="slideshow">
            <div class="title">Latest News</div>

            <div class="slider-frame">
                <div class="slide-images">
                    <img src="../resources/welcome/news/proQ.jpg" alt="Slide 1">
                    <img src="../resources/welcome/news/zhuxin.jpg" alt="Slide 2">
                    <img src="../resources/welcome/news/starlight.jpg" alt="Slide 3">
                    <img src="../resources/welcome/news/recharge.jpg" alt="Slide 4">
                    <img src="../resources/welcome/news/proQ.jpg" alt="Slide 1">
                </div>
            </div>
        </div>

    </div>

    <div class="story_text">
        Experience the Thrill of Battle
    </div>
    <div class="aulus">
    </div>

    <div class="story_text">
        Protect your Allies
    </div>
    <div class="stun">
    </div>

    <div class="story_text">
        Exterminate Demons
    </div>
    <div class="monster">
    </div>

    <div class="story_text">
        Purify the World
    </div>
    <div class="kadita">
    </div>

    <div class="story_text">
        Your Epic Journey Awaits...
    </div>
    
    <footer>
        <img src="../resources/app_store.png" alt="">
        <img src="../resources/google_play.png" alt="">
        <img src="../resources/ml_logo.png" alt="" class="logo">
    </footer>

    <script type="text/javascript">
        window.addEventListener("scroll", function() {
            var header = document.querySelector("header");
            header.classList.toggle("sticky", window.scrollY > 0);
        });
    </script>
</body>