<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Meetings Title</title>
    <style>
           h2,h4,.chart-title{
                color: #8e0083;
        }
         .scrollme {
        overflow-x: auto;
      }
      .card-bg-1 { background-color: #FFD700!important; } /* Gold */
      .card-bg-2 { background-color: #C0C0C0!important; } /* Silver */
      .card-bg-3 { background-color: #CD7F32!important; } /* Bronze */
      .card-bg-4 { background-color: #4682B4!important; } /* Steel Blue */
      .card-bg-5 { background-color: #556B2F!important; } /* Dark Olive Green */
      .card-bg-6 { background-color: #8B0000!important; } /* Dark Red */
      .card-bg-7 { background-color: #4B0082!important; } /* Indigo */
      .card-bg-8 { background-color: #2E8B57!important; } /* Sea Green */
      .card-bg-9 { background-color: #FF6347!important; } /* Tomato */
      .card-bg-10 { background-color: #9932CC!important; } /* Dark Orchid */
      .card-bg-11 { background-color: #8B4513!important; } /* Saddle Brown */
      .card-bg-12 { background-color: #20B2AA!important; } /* Light Sea Green */
      .card-bg-13 { background-color: #FFDAB9!important; } /* Peach Puff */
      .card-bg-14 { background-color: #6A5ACD!important; } /* Slate Blue */
      .card-bg-15 { background-color: #FF69B4!important; } /* Hot Pink */
      .card-bg-16 { background-color: #00BFFF!important; } /* Deep Sky Blue */
      .text-light {
        color: white !important;
      }
      .text-dark {
        color: black !important;
      }
      .form-container {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .form-container input, .form-container button {
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ccc;
      }
      .form-container button {
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
      }
      .col-sm-6.text-right-data {
        align-items: right;
        justify-content: right;
        display: flex;
      }
      .row-equal-height {
        display: flex;
        flex-wrap: wrap;
      }
      .row-equal-height > [class*='col-'] {
        display: flex;
        flex-direction: column;
      }
      .card {
        margin-bottom: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
      }
      .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center; /* Center content vertically */
      }
      .card-bg-1 { border: 2px solid #D4AF37;  } /* Gold border */
      .card-bg-2 { border: 2px solid #A9A9A9; } /* Silver border */
      .card-bg-3 { border: 2px solid #8B4513; } /* Bronze border */
      .card-bg-4 { border: 2px solid #1E90FF; } /* Steel Blue border */
      .card-bg-5 { border: 2px solid #556B2F; } /* Dark Olive Green border */
      .card-bg-6 { border: 2px solid #800000; } /* Dark Red border */
      .card-bg-7 { border: 2px solid #4B0082; } /* Indigo border */
      .card-bg-8 { border: 2px solid #2E8B57; } /* Sea Green border */
      .card-bg-9 { border: 2px solid #FF6347; } /* Tomato border */
      .card-bg-10 { border: 2px solid #9932CC; } /* Dark Orchid border */
      .card-bg-11 { border: 2px solid #8B4513; } /* Saddle Brown border */
      .card-bg-12 { border: 2px solid #20B2AA; } /* Light Sea Green border */
      .card-bg-13 { border: 2px solid #FFDAB9; } /* Peach Puff border */
      .card-bg-14 { border: 2px solid #6A5ACD; } /* Slate Blue border */
      .card-bg-15 { border: 2px solid #FF69B4; } /* Hot Pink border */
      .card-bg-16 { border: 2px solid #00BFFF; } /* Deep Sky Blue border */

      /* Multiple layer frame styles */
      .frame-layer-1 {
        padding: 5px;
        background: linear-gradient(145deg, #f0f0f0, #d9d9d9);
        border-radius: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
        margin-bottom: 2px;
      }
      .frame-layer-2 {
        padding: 10px;
        background: linear-gradient(145deg, #e6e6e6, #cccccc);
        border-radius: 10px;
        flex: 1;
        display: flex;
        flex-direction: column;
        margin-bottom: 2px;
      }
      .frame-layer-3 {
        padding: 15px;
        background: linear-gradient(145deg, #f5f5f5, #e0e0e0);
        border-radius: 5px;
        flex: 1;
        display: flex;
        flex-direction: column;
        margin-bottom: 2px;
      }

      .card.text-center{
        align-items: center;
        justify-content: center;
        display: flex;
      }
      .card-group {
        margin-bottom: 20px;
      }
      .card-group-title {
        width: 100%;
        text-align: center;
        margin-bottom: 10px;
        font-weight: bold;
        font-size: 1.2em;
      }

      @media (min-width: 576px) {
      .card-group {
        display: -ms-flexbox;
        display: unset;
        -ms-flex-flow: row wrap;
        flex-flow: row wrap;
    }
}
.row-color-1 { background-color: #ffdddd; }
    .row-color-2 { background-color: #ddffdd; }
    .row-color-3 { background-color: #ddddff; }
    .row-color-4 { background-color: #ffffdd; }
    .row-color-5 { background-color: #ffddff; }
    .row-color-6 { background-color: #d1ffd1; }
    .row-color-7 { background-color: #ffd1d1; }
    .row-color-8 { background-color: #d1d1ff; }
    .row-color-9 { background-color: #ffefd1; }
    .row-color-10 { background-color: #d1ffe7; }
    .row-color-11 { background-color: #ffd1f9; }
    .row-color-12 { background-color: #d1f9ff; }
    .row-color-13 { background-color: #f9d1ff; }
    .row-color-14 { background-color: #d1ffd9; }
    .row-color-15 { background-color: #ffd9d1; }
    .row-color-16 { background-color: #d9ffd1; }
    .row-color-17 { background-color: #d1d9ff; }
    .row-color-18 { background-color: #ffd1d9; }
    .row-color-19 { background-color: #d9d1ff; }
    .row-color-20 { background-color: #ffd1e7; }
    .row-color-21 { background-color: #d1ffe7; }
    .row-color-22 { background-color: #e7d1ff; }
    .row-color-23 { background-color: #d1ffd1; }
    .row-color-24 { background-color: #ffd1ff; }
    .row-color-25 { background-color: #d1e7ff; }
    .row-color-26 { background-color: #ffd1d1; }
    .row-color-27 { background-color: #d1ffd9; }
    .row-color-28 { background-color: #d9d1ff; }
    .row-color-29 { background-color: #ffd9d1; }
    .row-color-30 { background-color: #d1d9ff; }
    .row-color-31 { background-color: #ffd1e7; }
    .row-color-32 { background-color: #e7d1ff; }
    .row-color-33 { background-color: #d1ffd1; }
    .row-color-34 { background-color: #ffd1ff; }
    .row-color-35 { background-color: #d1e7ff; }
    .row-color-36 { background-color: #ffd1d1; }
    .row-color-37 { background-color: #d1ffd9; }
    .row-color-38 { background-color: #d9d1ff; }
    .row-color-39 { background-color: #ffd9d1; }
    .row-color-40 { background-color: #d1d9ff; }
    .row-color-41 { background-color: #ffd1e7; }
    .row-color-42 { background-color: #e7d1ff; }
    .row-color-43 { background-color: #d1ffd1; }
    .row-color-44 { background-color: #ffd1ff; }
    .row-color-45 { background-color: #d1e7ff; }
    .row-color-46 { background-color: #ffd1d1; }
    .row-color-47 { background-color: #d1ffd9; }
    .row-color-48 { background-color: #d9d1ff; }
    .row-color-49 { background-color: #ffd9d1; }
    .row-color-50 { background-color: #d1d9ff; }
    tr{
      font-weight: bold;
    }
    a.card-count-heading{
      color: #ff0022;
    text-decoration: none;
    background-color: yellow;
    padding: 4px 10px;
    }
    </style>
  </head>
  <body>
   <?php include ("nav.php"); ?>

    <div class="container-fluid">

     <hr>
        <h2 class="text-center mb-4">Meeting Data Visualization</h2>
      <hr>

        <div class="card-group">
              <div class="card-group-title">Plan Meetings</div>
              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-1">
                          <div class="card-cotent-data">
                            <h4>Total Plan Meetings</h4>
                            <p>All planned meetings.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_plan_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_plan_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-2">
                          <div class="card-cotent-data">
                            <h4>Complete Meetings</h4>
                            <p>Meetings that are completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/complete_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $complete_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-3">
                          <div class="card-cotent-data">
                            <h4>Not Complete Meetings</h4>
                            <p>Meetings that are not completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $not_complete_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

               <!-- RP Meetings Group -->
               <div class="card-group">
              <div class="card-group-title">RP / NO RP / Only Got Details Meetings</div>
              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-12">
                          <div class="card-cotent-data">
                            <h4>Total RP Meetings</h4>
                            <p>Meetings with Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-13">
                          <div class="card-cotent-data">
                            <h4>Total NO RP Meetings</h4>
                            <p>Meetings without Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-14">
                          <div class="card-cotent-data">
                            <h4>Total Only Got Details Meetings</h4>
                            <p>Meetings with only details obtained.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Only_Got_details_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Only_Got_details_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

             <!-- RP Meetings Group -->
               <div class="card-group">
              <div class="card-group-title">Fresh / Re Meetings Details</div>
              <div class="row">
              <div class="col-md-6">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-13">
                          <div class="card-cotent-data">
                            <h4>Total Fresh Meetings</h4>
                            <p>New meetings conducted.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_fresh_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_fresh_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-14">
                          <div class="card-cotent-data">
                            <h4>Total Re Meetings</h4>
                            <p>Follow-up meetings conducted.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_re_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_re_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
               
              </div>
            </div>

                  <!-- Scheduled Meetings Group -->
            <div class="card-group">
              <div class="card-group-title">Total Scheduled Meetings</div>
              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4>Total Scheduled Meetings</h4>
                            <p>All scheduled meetings.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_scheduled_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_scheduled_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4>Total Complete Scheduled Meetings</h4>
                            <p>Scheduled meetings that are completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_scheduled_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_complete_scheduled_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-5">
                          <div class="card-cotent-data">
                            <h4>Not Complete Scheduled Meetings</h4>
                            <p>Scheduled meetings that are not completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_scheduled_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $not_complete_scheduled_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

                  <!-- Scheduled RP Meetings Group -->
              <div class="card-group">
              <div class="card-group-title"> RP / NO RP / Only Got Details (Scheduled Meetings) </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-15">
                          <div class="card-cotent-data">
                            <h4>Total Scheduled RP Meetings</h4>
                            <p>Scheduled meetings with Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Scheduled_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-16">
                          <div class="card-cotent-data">
                            <h4>Total Scheduled NO RP Meetings</h4>
                            <p>Scheduled meetings without Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_NO_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Scheduled_NO_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-1">
                          <div class="card-cotent-data">
                            <h4>Total Scheduled Only Got Details Meetings</h4>
                            <p>Scheduled meetings with only details obtained.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_Only_Got_details_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Scheduled_Only_Got_details_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Barge Meetings Group -->
            <div class="card-group">
              <div class="card-group-title">Total Barge Meetings</div>
              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-6">
                          <div class="card-cotent-data">
                            <h4>Total Barge Meetings</h4>
                            <p>All barge meetings.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_barg_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_barg_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-7">
                          <div class="card-cotent-data">
                            <h4>Total Complete Barge Meetings</h4>
                            <p>Barge meetings that are completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_barg_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_complete_barg_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-8">
                          <div class="card-cotent-data">
                            <h4>Not Complete Barge Meetings</h4>
                            <p>Barge meetings that are not completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_barg_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $not_complete_barg_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
    </div>
    <?php include ("footer.php"); ?>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>