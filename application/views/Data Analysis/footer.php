<section class="p-3 mt-5" style="background: aliceblue;">
      <hr>
      <footer>
        <div class="container-fluid text-center">
          <div class="row">
            <div class="col-md-12">
              <div class="footer-card">
                <span class="footer-title">Copyright © 2025-<?= date("Y")?>. All Rights Reserved. Developed By Stem Learning Pvt Ltd</span>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </section>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
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