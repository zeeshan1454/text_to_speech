<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
       

       
        <a class="nav-link <?php if($page == 'texttospeech'){ echo ''; } else { echo 'collapsed'; } ?>"
            data-bs-target="#components-nav" href="index.php"> <i class="bi bi-headphones"
                style="color: rgb(18, 213, 243);"></i><span>Text To Speech</span><span class=""></span> </a>
                
        <a class="nav-link <?php if($page == 'speechtotext'){ echo ''; } else { echo 'collapsed'; } ?>"
            data-bs-target="#components-nav" href="speechtotext.php"> <i class="bi bi-headphones"
                style="color: rgb(73, 143, 135);"></i><span>Speech To Text</span><span class=""></span> </a>
      
        <li class="nav-item"> <a class="nav-link collapsed" href="logout.php"> <i class="bi bi-box-arrow-right"
                    style="color: rgb(255, 0, 0);"></i> <span>Sign Out</span></a></li>
    </ul>
</aside>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Get the navbar links
      var oneWayDashboardLink = document.getElementById("oneWayDashboardLink");
      var twoWayDashboardLink = document.getElementById("twoWayDashboardLink");
      // alert(oneWayDashboardLink);
      // Add click event listeners
      oneWayDashboardLink.addEventListener("click", function() {
        // Set session for One Way Dashboard
        sessionStorage.setItem("dashboardType", "oneWay");
        window.location.href = "index.php?dashboardType=oneWay";

      });

      twoWayDashboardLink.addEventListener("click", function() {
        // Set session for Two Way Dashboard
        sessionStorage.setItem("dashboardType", "twoWay");
        window.location.href = "index.php?dashboardType=twoWay";

      });
    });
  </script>

