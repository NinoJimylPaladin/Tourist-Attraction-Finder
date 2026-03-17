<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Tourist Attraction Finder</title>
        <link rel="stylesheet" href="../assets/css/landing-page.css">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
         
    </head>
<body>
    <div class="background">
       <div data-aos="fade-down" data-aos-duration="1500" data-aos-delay="300" class="container">
            <div class="logo">
                <img src="../assets/img/logo.png">
            </div>
            <div class="search-home-container">
                <ul>
                    <a href="index.php">Home</a>
                    <a href="packages.php">Packages</a>
                    <a href="community.php">Community</a>
                    <a href="about.php">About</a>
                </ul>
            </div>
            <div class="signin-button">
                <a href="login.php">Sign In</a>
            </div>
       </div>
       <div class="container2">
            <div data-aos="fade-right" data-aos-duration="1500" data-aos-delay="300" class="discover-explore">
                <h1>Discover your <br>
                    next adventure
                </h1>
                <p>From local treasures to world wonders, find<br>
                    your path to unforgettable places
                </p>
                <div class="explore-button">
                    <a href="#">Explore Destinations</a>
                </div>
            </div>
            <div data-aos="fade-left" data-aos-duration="1500" data-aos-delay="300" class="two-cards">
                <img class="yellow-car" src="../assets/img/yellow-car.png">
                <img class="falls" src="../assets/img/falls.png">
            </div>
       </div>
    </div>
    <div class="top-destinations-container">
        <div class="background-top-destinations">
            <h1>Top Destinations</h1>
            <p>specific reasons why this should be your main goal.</p>
        </div>
        <div class="button-cities-municipalities">
            <div>
                <a class="cities">Cities</a>
                <a class="municipalities">Municipalities</a>
            </div>
        </div>
    </div>

    <div id="top-destinations-cards" class="top-destinations-cards-container"></div>
   
    <div class="click-to-see-more-container">
        <div>
            <h1>How can i help your journey</h1>
        </div>
    </div>

    <div id="three-cards" class="three-cards-container"></div>

    <div class="click-container">
        <div>
            <a class="click">Click to see more</a>
        </div>
    </div>

    <h1 class="explorer">Explorer’s Stories & Social Proof</h1>

    <div class="collage-container">
        <div class="first-row-container">
            <div class="first-row-div1">
                <img src="../assets/img/bike.png">
            </div>
            <div class="first-row-div2">
                <img src="../assets/img/cave.png">
            </div>
        </div>
        
        <div class="second-row-container">
            <div class="second-row-div1">
                <img src="../assets/img/diver.png">
            </div>
        </div>

        <div class="third-row-container">
            <div class="third-row-div1">
                <img src="../assets/img/family.png">
            </div>
            <div class="third-row-div2">
                <img src="../assets/img/selfie.png">
            </div>
        </div>
    </div>
    <div class="boy">
        <div>
            <img src="../assets/img/boy.png">
            <p>With the help of this website I’m happy<br>
                as a traveller to travel Zamboanga del<br>
                Norte</p>
        </div>
    </div>
    <div class="logo-bottom">
        <div>
            <img src="../assets/img/logo.png">
        </div>
    </div>

    <div class="quick-contacts-container">
        <div class>
            <h1 style="color:white">Quick Links</h1>
            <p>About Us</p>
            <p>Blog</p>
            <p>FAQs</p>
        </div>
        <div>
            <h1 style="color:white">Contacts</h1>
            <p>Email:info@tafgmail.com</p>
            <p>Phone:+63 12345678910</p>
            <p>Address:123 TrailBlazor Rd</p>
        </div>
    </div>

    <div class="social-icons">
        <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
        <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a>
        <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
        <a href="https://youtube.com" target="_blank"><i class="fab fa-youtube"></i></a>
    </div>
    
    <div class="all-rights-reserved">
        <div>
            <p style="color:#F2C94C;
            font-weight:bold;
            margin-top:20px">@Tourist Attraction Finder</p>
            <p style="color:white;
            margin-top:40px">2026 TAF. All rights reserved. l Terms & Conditions I Privacy Policy</p>
        </div>
    </div>


 <script src="../assets/js/app.js"></script>
 <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
    AOS.init();
    document.querySelectorAll('.smooth-link').forEach(link => {
    link.addEventListener('click', function(e) {
    e.preventDefault(); 
    document.body.classList.add('fade-out');
    setTimeout(() => {
    window.location.href = this.href; 
    }, 500); 
  });
});

    </script>
    

</body>    
</html>
