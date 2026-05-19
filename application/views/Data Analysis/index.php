<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" >
    <title> Data Analysis</title>
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
  *{margin:0;padding:0;box-sizing:0}section.nav-main{background:#f0f8ff}span.btn.btn-primary{margin:4px;border:0;box-shadow:rgba(0,0,0,.15) 0 2px 8px}a{color:var(--bs-link-color);text-decoration:none;font-size:16px;font-weight:400;color:#f900dd}span.footer-title{color:#c0026c;font-weight:500}.colaps-title.text-center{background:beige;padding:4px;box-shadow:rgba(9,30,66,.25) 0 1px 1px,rgba(9,30,66,.13) 0 0 1px 1px}.nav-tabs .nav-link{width:300px;font-weight:500}.nav-tabs .nav-link.active{background:#ff0061;color:#fff}li.nav-item{align-items:center;justify-content:center;display:flex}.active-btn{background-color:#28a745!important;color:#fff!important}
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
button.accordion-button.collapsed {
    /* font-size: large; */
    font-weight: 500;
    color: #ff00ae;
    font-size: 16px;
}

/* .accordion-button:not(.collapsed) {
    color: #ffffff;
    background-color: #206bd4;
    box-shadow: inset 0 calc(var(--bs-accordion-border-width)* -1) 0 var(--bs-accordion-border-color);
} */
body::-webkit-scrollbar{width:15px}
body::-webkit-scrollbar-track{box-shadow:inset 0 0 6px rgba(0,0,0,.3)}
body::-webkit-scrollbar-thumb{border-radius:15px;border:none;outline:0;background:#a8f268;background:linear-gradient(90deg,#a8f268 0,#f7025c 100%);background:-moz-linear-gradient(90deg,#a8f268 0,#f7025c 100%);background:-webkit-linear-gradient(90deg,#a8f268 0,#f7025c 100%)}
    </style>
  </head>
  <body>


  <?php 
  
  $cutype_id  = $user['type_id'];
  ?>

    <section class="nav-main">
      <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <a class="navbar-brand" href="#">
          <img src="https://stemlearning.in/wp-content/uploads/2020/07/stem-new-logo-2-1.png" width="200" alt="" class="img-fluid">
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">
            <li class="nav-item active">
                <a class="nav-link" href="<?=base_url()?>Menu/Dashboard">Switch To Main Dashboard &nbsp;&nbsp; <span class="horizentallineclass">|</span></a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="javascript:void(0)">
                  <span class="usernameclass">{ <?=$user['name']?> }</span>
                </a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="<?=base_url()?>Menu/Logout" title="Logout">
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
    <section class="mt-3">
      <div class="text-center p-2" style="background: beige;">
        <hr>
        <h2 class="text-uppercase"> Data Analysis</h2>
        <hr>
      </div>
      <div class="container-fluid mt-5">
        <div class="row">
        <div class="col-md-1"></div>
          <div class="col-md-8">
            <ul class="nav nav-tabs d-none d-lg-flex" id="myTab" role="tablist">
              <li class="nav-item" role="presentation" title="Sales Data Analysis">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Sales</button>
              </li>

              <?php if($cutype_id == 2 || $cutype_id == 45 || $cutype_id == 18): ?>
              <li class="nav-item" role="presentation"  title="Operation Data Analysis">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Operation</button>
              </li>
              <li class="nav-item" role="presentation" title="Factory Data Analysis" >
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Factory</button>
              </li>
              <?php endif; ?>
            </ul>
            <div class="tab-content accordion mt-2" id="myTabContent">
              <div class="tab-pane fade show active accordion-item" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                <div class="accordion" id="accordionExample">
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                      Task Analysis
                      </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                      <div class="accordion-body">
                        <div class="card card-body">
                          <div class="colaps-title text-center">
                            <h5 class="text-capitalize"> Task Analysis </h5>
                          </div>
                          <hr>
                          <div class="row">
                            <div class="col-md-8">
                              <div class="card">
                                <div class="card-body">
                                  <ul class="list-group">
                                    
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/TaskwiseDetailsAnalysis" target="_blank">
                                      1). Todays Task Planned Analysis
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/GetAllUserCurrentDayActivityOnAPPGraph" target="_blank">
                                      2).  Todays Team Activity On APP Analysis
                                        </a>
                                      </li>
                                    <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/GetTodaysAllUserAllTimeActivityOnAPPGraph" target="_blank">
                                        3).  Todays Task Status (Total / Pending / Completed) Analysis
                                        </a>
                                      </li>
                                    <!-- <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/AllTimesTaskwiseDetailsAnalysis" target="_blank">
                                      Last 3 Year User Activity On APP Graph
                                      </a>
                                    </li> -->
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/TeamWiseTaskGraph" target="_blank">
                                      4).  Todays Team Task Analysis
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/TodaysStatusWiseTask" target="_blank">
                                      5).  Todays Status wise Task Analysis
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/TodaysActionWiseTask" target="_blank">
                                      6).  Todays Action wise task Analysis
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/TodaysTimeSlotWiseFunnelAnalysis" target="_blank">
                                      7). Todays Time slot wise Task Analysis
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/TodaysStatusConversionWiseTaskAnalysis" target="_blank">
                                      8).  Todays Status Conversion Wise Task Analysis
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/CheckTodaysAllTodaysPlannerLog" target="_blank">
                                      9).  Todays Planner Log Analysis
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/TodaysStatusConversionWithDateTaskAnalysis" target="_blank">
                                      10).  All Time Status Conversion Analysis
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/CheckTodaysPlannerCurrentYearFocusFunnelsLogByuser" target="_blank">
                                      11).  User Marked focus funnels and add them to the planner Analysis (Todays)
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/CheckAllPlannerCurrentYearFocusFunnelsLogByuser" target="_blank">
                                      12).  User Marked focus funnels and  add them to the planner Analysis (Current Year)
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/AllTimesTaskwiseDetailsAnalysis" target="_blank">
                                      13).  All Times Team Task Graph
                                      </a>
                                    </li>
                                    <!-- <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/CompanyWhichNoStatusChange" target="_blank">
                                        Pending Task  
                                      </a>
                                    </li> -->
                                  </ul>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="card">
                                <img src="https://img.freepik.com/premium-vector/sales-performance-concept-with-people-scene-woman-man-analyzes-financial-data-makes-presentation-with-business-statistics-profit-vector-illustration-with-characters-flat-design-web_9209-9831.jpg" alt="" class="img-fluid">
                              </div>
                              <div class="card mt-1">
                              <img src="https://kit8.net/wp-content/uploads/2020/12/Data_analysis@2x-1.png" alt="" class="img-fluid">
                              </div>
                              <div class="card mt-1">
                                <!-- <img src="https://static.vecteezy.com/system/resources/previews/005/360/632/non_2x/online-data-analysis-flat-illustration-of-data-analytics-vector.jpg" alt="" class="img-fluid"> -->
                                 <img src="https://img.freepik.com/free-vector/marketing-professional-presenting-financial-chart-boss_74855-10928.jpg" alt="" class="img-fluid">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>


                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                      Task Status Analysis
                      </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                      <div class="accordion-body">
                      <div class="accordion-body">
                        <div class="card card-body">
                          <div class="colaps-title text-center">
                            <h5 class="text-capitalize"> Task Analysis </h5>
                          </div>
                          <hr>
                          <div class="row">
                            <div class="col-md-8">
                              <div class="card">
                                <div class="card-body">
                                  <ul class="list-group">
                                    
                                     
                                      <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/TaskVsCompanyStatusAnalysis" target="_blank">
                                        1). Todays Task vs Company Status Analysis
                                        </a>
                                      </li>
                                      <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/CurrentYearTaskVsCompanyStatusAnalysis" target="_blank">
                                        2). Current Year Task Wise Company Status Conversion Analysis
                                        </a>
                                      </li>
                                      <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/GetTodaysCompanyStatusConversionAnalysis" target="_blank">
                                        3). Todays Company Status Conversion Analysis
                                        </a>
                                      </li>
                                      <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/CheckCurrentYearsCompanyStatusConversionRate" target="_blank">
                                        4). Date Wise Status Conversion Data Visualization (Current Year)
                                        </a>
                                      </li>

                                      <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/CheckTodaysCompanyStatusConversionRate" target="_blank">
                                        5). Today Status Conversion Data Visualization
                                        </a>
                                      </li>
                                      <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/CheckDateWiseTaskCompanyStatusConversionRate" target="_blank">
                                        6). Date Wise Task Status Analysis
                                        </a>
                                      </li>
                                      <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/CompleteMeetingsReportAnalysis" target="_blank">
                                        6). Complete Meetings Report Analysis
                                        </a>
                                      </li>

                                      

                                      <!-- <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/CompanyWhichNoStatusChange" target="_blank">
                                        Task vs Company Status Changed By User Analysis
                                        </a>
                                      </li>
                                      <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/CompanyWhichNoStatusChange" target="_blank">
                                            Todays Compulsive Task & Need Your Attention Analysis
                                        </a>
                                      </li> -->
                                      
                                     
                                    
                                  </ul>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="card">
                              <img src="https://previews.123rf.com/images/surfupvector/surfupvector1903/surfupvector190301347/119219856-data-analysis-illustration-people-analyze-big-data-business-result-concept-vector-illustration.jpg" alt="" class="img-fluid">
                              </div>
                              <div class="card mt-1">
                              <img src="https://img.freepik.com/free-vector/company-employees-planning-task-brainstorming_74855-6316.jpg" alt="" class="img-fluid">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      </div>
                    </div>
                  </div>



                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingfour">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsefour" aria-expanded="false" aria-controls="collapsefour">
                        Day Management
                      </button>
                    </h2>
                    <div id="collapsefour" class="accordion-collapse collapse show" aria-labelledby="headingfour" data-bs-parent="#accordionExample">
                      <div class="accordion-body">
                       
                                <div class="row">
                                  <div class="col-md-8">
                                  <ul class="list-group">
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/TodaysUserDayAnalysis" target="_blank">
                                      1). Todays User Day Analysis
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/CurrerntYearDayAnalysis" target="_blank">
                                      2). User Day Analysis (Current Year)
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/TodaysUserDayMapAnalysis" target="_blank">
                                      3). Todays User Day Map Analysis (Google Map)
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/CurrerntYearDayMapAnalysis" target="_blank">
                                      4). User Day Map Analysis (Current Year - Google Map)
                                      </a>
                                    </li>
                                </ul>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="card">
                                    <img src="https://img.freepik.com/free-vector/set-up-daily-schedule-abstract-concept-illustration-quarantine-daily-routine-schedule-your-day-staying-home-self-organization-set-up-study-calendar_335657-592.jpg" alt="" class="img-fluid">
                                    </div>
                                  </div>
                                </div>

                      </div>
                    </div>
                  </div>

                  
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThreePlannerManagement">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThreePlannerManagement" aria-expanded="false" aria-controls="collapseThreePlannerManagement">
                        Planner Management
                      </button>
                    </h2>
                    <div id="collapseThreePlannerManagement" class="accordion-collapse collapse show" aria-labelledby="headingThreePlannerManagement" data-bs-parent="#accordionExample">
                      <div class="accordion-body">
                      <div class="row">
                                  <div class="col-md-8">
                                  <ul class="list-group">
                                    <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/TodaysTaskPlannerManagementAnalysis" target="_blank">
                                            1). Todays Planner Analysis
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/MeetingsReportAnalysis" target="_blank">
                                            1). Meeting Data Analysis By User Between Date
                                        </a>
                                    </li>
                                    <!-- <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/TodaysUserPlannerManagementAnalysis" target="_blank">
                                            1). Todays Planner Analysis
                                        </a>
                                    </li> -->
                                    <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/TodaysStatusWisePlannerAnalysis" target="_blank">
                                            2). Todays Status Wise Planner Analysis
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/TodaysCategoryWisePlannerAnalysis" target="_blank">
                                            3). Todays Category Wise Planner Analysis
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/TodaysCategoryDevideWisePlannerAnalysis" target="_blank">
                                            4). Todays Category Devide Wise Planner Analysis
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="<?=base_url()?>SalesGraph/TodaysCompanyWisePlannerAnalysis" target="_blank">
                                            5). Todays Company Name Wise Planner Analysis
                                        </a>
                                    </li>
                                </ul>
                                  </div>
                                  <div class="col-md-4">
                                    <img src="https://static.vecteezy.com/system/resources/previews/004/586/115/non_2x/startup-business-project-launch-idea-through-planning-and-strategy-time-management-realization-teamwork-in-the-startup-start-up-concept-with-spaceship-flat-illustration-with-characters-vector.jpg" alt="" class="img-fluid">
                                  </div>
                                </div>
                      </div>
                    </div>
                  </div>

                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThreeCompanyManagementAnalysis">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThreeCompanyManagementAnalysis" aria-expanded="false" aria-controls="collapseThreeCompanyManagementAnalysis">
                        Company Management Analysis
                      </button>
                    </h2>
                    <div id="collapseThreeCompanyManagementAnalysis" class="accordion-collapse collapse show" aria-labelledby="headingThreeCompanyManagementAnalysis" data-bs-parent="#accordionExample">
                      <div class="accordion-body">
                      
                      <div class="row">
                        <div class="col-md-8">
                        <ul class="list-group">
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/CompaniesListThatDoNotHavePrimaryContactAnalysis" target="_blank">
                                      1). Company That Do Not Have Primary Contact Analysis
                                      </a>
                                    </li>
                                    <li class="list-group-item">
                                      <a href="<?=base_url()?>SalesGraph/CompanyThatHasPrimaryContactAnalysis" target="_blank">
                                      2). Company That Has Primary Contact Analysis
                                      </a>
                                    </li>
                                   
                                </ul>
                        </div>
                        <div class="col-md-4">
                        <img src="https://img.freepik.com/free-vector/people-analyzing-growth-charts_23-2148866843.jpg" alt="" class="img-fluid">
                        </div>
                      </div>

                      </div>
                    </div>
                  </div>

                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                         Funnel analysis
                      </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                      <div class="accordion-body">
                        <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                      </div>
                    </div>
                  </div>

                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        User management Analysis
                      </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                      <div class="accordion-body">
                        <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                      </div>
                    </div>
                  </div>


                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                      Review Analysis
                      </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                      <div class="accordion-body">
                        <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                      </div>
                    </div>
                  </div>

                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                          Meeting Analysis
                      </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                      <div class="accordion-body">
                        <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                      </div>
                    </div>
                  </div>




                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <img src="https://img.freepik.com/premium-vector/data-analytics_773186-791.jpg" alt="" class="img-fluid">
            <!-- <img src="https://miro.medium.com/v2/resize:fit:869/0*Lu_e0xFezC6gTtvd.jpg" alt="" class="img-fluid">
              <img src="https://kit8.net/wp-content/uploads/2020/12/Data_analysis@2x-1.png" alt="" class="img-fluid">
              <img src="https://previews.123rf.com/images/surfupvector/surfupvector1903/surfupvector190301347/119219856-data-analysis-illustration-people-analyze-big-data-business-result-concept-vector-illustration.jpg" alt="" class="img-fluid">
              <img src="https://static.vecteezy.com/system/resources/previews/005/360/632/non_2x/online-data-analysis-flat-illustration-of-data-analytics-vector.jpg" alt="" class="img-fluid">
              <img src="https://img.freepik.com/free-vector/data-inform-illustration-concept_114360-864.jpg" alt="" class="img-fluid">
              <img src="https://img.freepik.com/premium-vector/data-analytics-information-web-development-website-statistic_257312-767.jpg" alt="" class="img-fluid">
              <img src="https://img.freepik.com/premium-vector/concept-flat-design-product-data-analysis-seo-optimization_18660-456.jpg" alt="" class="img-fluid">
              <img src="https://img.freepik.com/free-vector/communication-flat-icon_1262-18771.jpg" alt="" class="img-fluid">
              <img src="https://img.freepik.com/free-vector/company-employees-planning-task-brainstorming_74855-6316.jpg" alt="" class="img-fluid">
              <img src="https://img.freepik.com/free-vector/marketing-professional-presenting-financial-chart-boss_74855-10928.jpg" alt="" class="img-fluid"> -->
          </div>
          <div class="col-md-1"></div>
        </div>
      </div>
    </section>
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
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      $(document).ready(function () {
      $(".btn").click(function () {
        $(".btn").removeClass("active-btn"); // Remove active class from all buttons
        if ($(this).attr("aria-expanded") === "false") {
            $(this).addClass("active-btn"); // Add active class to the clicked button
        }
      });
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
      
      });
      
    </script>
  </body>
</html>