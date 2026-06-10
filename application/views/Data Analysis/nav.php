<section class="nav-main">
      <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <a class="navbar-brand" href="<?=base_url()?>SalesGraph/Dashboard">
          <img src="https://stemlearning.in/wp-content/uploads/2020/07/stem-new-logo-2-1.png" width="200" alt="" class="img-fluid">
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ml-auto">
              <li class="nav-item active">
                <a class="nav-link" href="<?=base_url()?>SalesGraph/Dashboard">Graph Home &nbsp;&nbsp; <span class="horizentallineclass">|</span></a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="<?=base_url()?>Menu/Dashboard">Switch To Main Dashboard &nbsp;&nbsp; <span class="horizentallineclass">|</span></a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="javascript:void(0)">
                  <span class="usernameclass">{ <?=$user['name']?> }</span>
                </a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="<?=base_url()?>Menu/Logout" title="logout">
                <img src="https://i.ibb.co/hR6KZhM1/logout-153871-640.png" width="40" height="40" alt="">
                </a>
              </li>
              <!-- <li class="nav-item">
                <a class="nav-link" href="#">Features</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#">Pricing</a>
                </li> -->
            </ul>
          </div>
        </nav>
      </div>
    </section>

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
    </script>
    <style>
      span.usernameclass {
    color: #ce08b0;
    font-weight: 500;
    /* text-decoration: underline; */
}
.horizentallineclass {
    color: red;
}
a.nav-link {
    color: #ff00ae !important;
}
body::-webkit-scrollbar{width:15px}
body::-webkit-scrollbar-track{box-shadow:inset 0 0 6px rgba(0,0,0,.3)}
body::-webkit-scrollbar-thumb{border-radius:15px;border:none;outline:0;background:#a8f268;background:linear-gradient(90deg,#a8f268 0,#f7025c 100%);background:-moz-linear-gradient(90deg,#a8f268 0,#f7025c 100%);background:-webkit-linear-gradient(90deg,#a8f268 0,#f7025c 100%)}
    </style>