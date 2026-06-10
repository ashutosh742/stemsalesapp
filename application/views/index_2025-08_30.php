<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stem CRM | Webapp</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://stemapp.in/assets/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="https://stemapp.in/assets/css/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://stemapp.in/assets/css/adminlte.min.css">
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(ellipse at bottom, #1B2735 0%, #090A0F 100%);
            overflow: hidden;
            font-family: 'Arial', sans-serif;
        }

        .stars {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .star {
            position: absolute;
            background-color: white;
            border-radius: 50%;
            animation: twinkle var(--duration) infinite;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.2; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        .container {
            position: relative;
            text-align: center;
            z-index: 2;
        }

        .card, .small-box>.inner, button.btn.btn-primary.btn-block, input.form-control, table {
            box-shadow: inset 4px 4px 7px rgba(55,84,170,.15), inset -4px -4px 10px #fff, 0 0 2px rgba(255,255,255,.2);
            transition: box-shadow .2s ease-in-out;
        }

        button.btn.btn-primary.btn-block, input.form-control {
            border-radius: 23px;
            outline: 0;
            border: none;
        }

        .card, .small-box>.inner, table {
            background: #fff8dc;
            padding: 10px;
        }

        .input-group-text, .input-group>.custom-file, .input-group>.custom-select, .input-group>.form-control, .input-group>.form-control-plaintext {
            box-shadow: inset 4px 4px 7px rgb(55 117 170 / 39%), inset -4px -4px 10px #fff, 0 0 2px rgb(255 255 255 / 0%);
            transition: box-shadow .2s ease-in-out;
        }

        .frame-container {
            position: relative;
            width: 100%;
            padding: 20px;
            background-color: rgba(255,255,255,.8);
            border-radius: 10px;
        }

        .frame-layer {
            position: absolute;
            border-radius: 15px;
            pointer-events: none;
            opacity: .5;
            border: 1px solid transparent;
        }

        .frame-layer-1 {
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: linear-gradient(135deg, #f5f7fa 0, #c3cfe2 100%);
            box-shadow: 0 0 10px rgba(0,0,0,.5);
            border-image: linear-gradient(135deg, #3498db, #9b59b6) 1;
            animation: 3s linear infinite borderAnimation;
        }

        .frame-layer-2 {
            top: -20px;
            left: -20px;
            right: -20px;
            bottom: -20px;
            background: linear-gradient(135deg, #e0e0e0 0, #a0a0a0 100%);
            box-shadow: 0 0 15px rgba(0,0,0,.5);
            border-image: linear-gradient(135deg, #e74c3c, #f39c12) 1;
            animation: 3s linear .5s infinite borderAnimation;
        }

        .frame-layer-3 {
            top: -30px;
            left: -30px;
            right: -30px;
            bottom: -30px;
            background: linear-gradient(135deg, #d1d1d1 0, #8a8a8a 100%);
            box-shadow: 0 0 20px rgba(0,0,0,.5);
            border-image: linear-gradient(135deg, #2ecc71, #1abc9c) 1;
            animation: 3s linear 1s infinite borderAnimation;
        }

        .gradient-text, .gradient-text1 {
            color: transparent;
            animation: 5s infinite gradientAnimation;
        }

        @keyframes borderAnimation {
            0%, 100% { border: 1px solid transparent; border-top-color: #3498db; }
            25% { border: 1px solid transparent; border-right-color: #3498db; }
            50% { border: 1px solid transparent; border-bottom-color: #3498db; }
            75% { border: 1px solid transparent; border-left-color: #3498db; }
        }

        .card.card-outline.card-primary {
            background: rgba(255,255,255,.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.18);
        }

        .gradient-text {
            background: linear-gradient(90deg, #ff8a00, #e52e71, #1e90ff);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            background-clip: text;
        }

        .gradient-text1 {
            background: linear-gradient(90deg, #e113aa, #1500ff, #1e90ff);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            background-clip: text;
        }

        @keyframes gradientAnimation {
            0%, 100% { background-position: 0 50%; }
            50% { background-position: 100% 50%; }
        }

        .card-header img {
            animation: 3s ease-in-out infinite floatAnimation;
        }

        @keyframes floatAnimation {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        #mainlogo {
            filter: drop-shadow(0 0 1rem blue);
            animation: 5s linear infinite spinGradient_image;
            transition: .2s;
        }

        @keyframes spinGradient_image {
            0% { filter: drop-shadow(0 0 1rem #ff0f7b); }
            25% { filter: drop-shadow(0 0 1rem #fff95b); }
            50% { filter: drop-shadow(0 0 1rem #e60b09); }
            75% { filter: drop-shadow(0 0 1rem #59d102); }
            100% { filter: drop-shadow(0 0 1rem #6bdfdb); }
        }

        @keyframes spinGradient {
            0% { background: conic-gradient(from 90deg at 50% 50%, #220c0c, #220c0c, #0f0c22, #0c2215, #140c22, #220c13); }
            25% { background: radial-gradient(circle, #220c0c, #0f0c22, #0c2215, #140c22, #220c13); }
            50% { background: conic-gradient(from 180deg at 50% 50%, #220c0c, #0f0c22, #0c2215, #140c22, #220c13, #220c0c); }
            75% { background: conic-gradient(from 270deg at 50% 50%, #220c0c, #0f0c22, #0c2215, #140c22, #220c13, #220c0c); }
        }

        @keyframes glowing {
            0%, 100% { box-shadow: 0 0 5px #3498db; text-shadow: 0 0 5px #3498db; }
            50% { box-shadow: 0 0 20px #3498db, 0 0 20px #3498db; text-shadow: 0 0 20px #3498db, 0 0 20px #3498db; }
        }

        button.btn-11 {
            animation: 2s infinite glowing;
            width: 150px;
        }

        .custom-btn {
            width: 130px;
            height: 40px;
            color: #fff;
            border-radius: 5px;
            padding: 10px 25px;
            font-family: Lato, sans-serif;
            font-weight: 500;
            background: 0 0;
            cursor: pointer;
            transition: .3s;
            position: relative;
            display: inline-block;
            box-shadow: inset 2px 2px 2px 0 rgba(255,255,255,.5), 7px 7px 20px 0 rgba(0,0,0,.1), 4px 4px 5px 0 rgba(0,0,0,.1);
            outline: 0;
        }

        .btn-11 {
            border: none;
            background: #212ffb;
            background: linear-gradient(0deg, #3e21fb 0, #4c5cea 100%);
            color: #fff;
            overflow: hidden;
        }

        .btn-11:hover {
            text-decoration: none;
            color: #fff;
            opacity: .7;
        }

        .btn-11:before {
            position: absolute;
            content: '';
            display: inline-block;
            top: -180px;
            left: 0;
            width: 30px;
            height: 100%;
            background-color: #fff;
            animation: 3s ease-in-out infinite shiny-btn1;
        }

        .btn-11:active {
            box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2);
        }

        @keyframes shiny-btn1 {
            0% { transform: scale(0) rotate(45deg); opacity: 0; }
            80% { transform: scale(0) rotate(45deg); opacity: .5; }
            81% { transform: scale(4) rotate(45deg); opacity: 1; }
            100% { transform: scale(50) rotate(45deg); opacity: 0; }
        }

        .celestial-body {
            position: absolute;
            border-radius: 50%;
            z-index: 1;
            animation: float 20s infinite alternate;
        }

        .sun {
            width: 150px;
            height: 150px;
            top: 10%;
            left: 10%;
            background: radial-gradient(circle at 30% 30%, #ffde00, #ff8c00, #e85d04);
            animation-delay: 0s;
        }

        .earth {
            width: 100px;
            height: 100px;
            top: 70%;
            left: 20%;
            background: radial-gradient(circle at 30% 30%, #0066cc, #0099ff, #00cc99);
            animation-delay: 3s;
        }

        .moon {
            width: 60px;
            height: 60px;
            top: 40%;
            left: 80%;
            background: radial-gradient(circle at 30% 30%, #ddd, #bbb, #999);
            animation-delay: 6s;
        }

        .mars {
            width: 80px;
            height: 80px;
            top: 30%;
            left: 70%;
            background: radial-gradient(circle at 30% 30%, #ff6600, #ff9933, #cc6600);
            animation-delay: 9s;
        }

        .jupiter {
            width: 200px;
            height: 200px;
            top: 60%;
            left: 60%;
            background: radial-gradient(circle at 30% 30%, #ffcc99, #ff9966, #cc6633);
            animation-delay: 12s;
        }

        .saturn {
            width: 170px;
            height: 170px;
            top: 20%;
            left: 50%;
            background: radial-gradient(circle at 30% 30%, #ffcc66, #ffcc99, #ff9966);
            animation-delay: 15s;
        }

        .saturn::before {
            content: "";
            position: absolute;
            width: 200px;
            height: 20px;
            background: radial-gradient(ellipse at center, rgba(204, 153, 102, 0.8), transparent 70%);
            border-radius: 50%;
            top: 50%;
            left: -15%;
            transform: rotate(20deg);
        }

        @keyframes float {
            0% { transform: translate(0, 0); }
            33% { transform: translate(50px, -50px); }
            66% { transform: translate(-50px, 50px); }
            100% { transform: translate(0, 0); }
        }

        .meteor {
            position: absolute;
            width: 4px;
            height: 4px;
            background-color: white;
            border-radius: 50%;
            z-index: 1;
            animation: meteorShoot 3s linear infinite;
        }

        .meteor::before {
            content: "";
            position: absolute;
            width: 50px;
            height: 2px;
            background: linear-gradient(to right, transparent, white);
            filter: blur(1px);
        }

        @keyframes meteorShoot {
            0% { transform: translate(0, 0); opacity: 1; }
            100% { transform: translate(100vw, 100vh); opacity: 0; }
        }

        .shuttle {
            position: absolute;
            font-size: 50px;
            z-index: 1;
            transition: transform 5s linear;
            background: linear-gradient(45deg, #4facfe, #00f2fe);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .main-login-box {
            z-index: 99999999999999999;
            width: 90%;
            max-width: 400px;
        }

        .moving-star {
            position: absolute;
            background-color: white;
            border-radius: 50%;
            animation: moveStar 5s linear infinite;
        }

        @keyframes moveStar {
            0% { transform: translate(0, 0); opacity: 1; }
            100% { transform: translate(100vw, 100vh); opacity: 0; }
        }

        @media (max-width: 768px) {
            .celestial-body {
                display: none;
            }

            .main-login-box {
                width: 95%;
            }

            .frame-container {
                padding: 10px;
            }
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="stars" id="stars"></div>
    <div class="celestial-body sun" title="Sun"></div>
    <div class="celestial-body earth" title="Earth"></div>
    <div class="celestial-body moon" title="Moon"></div>
    <div class="celestial-body mars" title="Mars"></div>
    <div class="celestial-body jupiter" title="Jupiter"></div>
    <div class="celestial-body saturn" title="Saturn"></div>
    <i class="fa-solid fa-shuttle-space shuttle"></i>

    <div class="login-box main-login-box">
        <div class="frame-container">
            <div class="frame-layer frame-layer-1"></div>
            <div class="frame-layer frame-layer-2"></div>
            <div class="frame-layer frame-layer-3"></div>
            <!-- /.login-logo -->
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <img id="mainlogo1" src="https://stemlearning.in/wp-content/uploads/2020/07/stem-new-logo-2-1.png" width="70%" />
                    <a href="javascript:void(0)" class="h1"><b class="gradient-text">CRM</b>App</a>
                </div>
                <div class="card-body">
                    <p class="login-box-msg gradient-text1">Sign in to start your session</p>
                   <?=form_open('Menu/login', array('id' => 'loginForm'))?>
                        <div class="input-group mb-3">
                            <input type="text" name="user" id="user" class="form-control" placeholder="User ID">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fa-solid fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock" id="togglePassword"></span>
                                </div>
                            </div>
                        </div>
                        <p id="error-message" class="text-danger" style="display: none;">
                            * Please enter both username and password.
                        </p>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <center>
                                    <button type="submit" class="custom-btn btn-11"><div class="dot"></div>Sign In</button>
                                </center>
                            </div>
                            <br>
                            <br>
                            <hr>
                            <div class="col-12">
                          <?php if ($this->session->flashdata('error_message')) { ?>
                          <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          <strong><?php echo $this->session->flashdata('error_message'); ?></strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <?php } ?>
                          <?php if ($this->session->flashdata('success_message')) { ?>
                          <div class="alert alert-success alert-dismissible fade show" role="alert">
                          <strong><?php echo $this->session->flashdata('success_message'); ?></strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <?php } ?>
                          </div>
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.login-box -->
    </div>
    <!-- jQuery -->
    <script src="https://stemapp.in/assets/js/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://stemapp.in/assets/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://stemapp.in/assets/js/adminlte.min.js"></script>
    <script>
        function setFavicon(url) {
            let link = document.querySelector("link[rel~='icon']");
            if (!link) {
                link = document.createElement('link');
                link.rel = 'icon';
                document.head.appendChild(link);
            }
            link.href = url;
        }
        setFavicon('https://stemapp.in/uploads/favicon/favicon.ico');

        document.addEventListener('DOMContentLoaded', function() {
            const starsContainer = document.getElementById('stars');
            const numberOfStars = 200;

            for (let i = 0; i < numberOfStars; i++) {
                const star = document.createElement('div');
                star.classList.add('star');

                const size = Math.random() * 3;
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;

                star.style.left = `${Math.random() * 100}%`;
                star.style.top = `${Math.random() * 100}%`;

                const duration = Math.random() * 3 + 2;
                star.style.setProperty('--duration', `${duration}s`);

                star.style.opacity = Math.random();

                starsContainer.appendChild(star);
            }

            const shuttle = document.querySelector('.shuttle');

            function moveShuttle() {
                const startX = Math.random() < 0.5 ? -50 : window.innerWidth;
                const startY = Math.random() * window.innerHeight;
                const endX = Math.random() < 0.5 ? window.innerWidth + 50 : -50;
                const endY = Math.random() * window.innerHeight;

                shuttle.style.left = `${startX}px`;
                shuttle.style.top = `${startY}px`;

                setTimeout(() => {
                    shuttle.style.transform = `translate(${endX - startX}px, ${endY - startY}px)`;
                }, 10);

                setTimeout(moveShuttle, 5000);
            }

            moveShuttle();

            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function (e) {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            document.getElementById('loginForm').addEventListener('submit', function(event) {
                var user = document.getElementById('user').value;
                var password = document.getElementById('password').value;
                var errorMessage = document.getElementById('error-message');

                if (user === '') {
                    errorMessage.style.display = 'block';
                    errorMessage.innerHTML = "* Please enter username.";
                    event.preventDefault();
                } else if (password === '') {
                    errorMessage.style.display = 'block';
                    errorMessage.innerHTML = "* Please enter password.";
                    event.preventDefault();
                } else {
                    errorMessage.style.display = 'none';
                }
            });

            // Create 10 moving stars
            for (let i = 0; i < 10; i++) {
                createMovingStar();
            }

            function createMovingStar() {
                const star = document.createElement('div');
                star.classList.add('moving-star');

                const size = Math.random() * 3 + 1;
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;

                star.style.left = `${Math.random() * 100}vw`;
                star.style.top = `${Math.random() * 100}vh`;

                document.body.appendChild(star);

                // Randomly reposition the star after animation completes
                star.addEventListener('animationend', function() {
                    star.style.left = `${Math.random() * 100}vw`;
                    star.style.top = `${Math.random() * 100}vh`;
                });
            }
        });
    </script>
    
<script>
 window.oncontextmenu = function () {
          return false;
          }
          $(document).keydown(function (event) {
          if (event.keyCode == 123) {
          return false;
          }
          else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) {
          return false;
          }
          else if (event.ctrlKey && event.keyCode == 85) {
          return false;
          }
          })
          function onKeyDown() {
          var pressedKey = String.fromCharCode(event.keyCode).toLowerCase();
          if (event.ctrlKey && (pressedKey == "c" || pressedKey == "v" || pressedKey=="j" )) {
          event.returnValue = false;
          }
          }

</script> 
</body>
</html>
