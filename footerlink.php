<!-- add Live Detale open form start  -->

<?php
   include('live_popup.php');

   
?>
<!-- include javascript code for live show popup value  -->
<script>
  $(document).on("click", ".contact_live_enquery", function() {
    var live_client_number = $(this).data("live_client_number");
    var live_agent_number = $(this).data("live_agent_number");
    
    $("#live_cilent_number_fill").val(live_client_number);
    $("#live_agent_number_fill").val(live_agent_number);
  });
</script>
<!-- include javascript code for live show popup value  -->
  <script>
  $(document).on("click", ".contact_des_set_alerm", function() {
    var cnumber = $(this).data("client_number");
    var cid = $(this).data("client_id");

    $("#contact_alarm_des_c").val(cnumber);
    $("#call_alarm_id_c").val(cid);
  });
</script>
<!-- add Live Detale open form End  -->
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.min.js"></script>

    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/js/main.js"></script>
    <script async="" src='../../../gtag/js?id=G-P7JSYB1CSP'></script>
    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
  <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" ></script>
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js" ></script> -->
  <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

           <!-- <script src="http://code.jquery.com/jquery-1.8.3.js"></script> -->
  <!-- <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
    
   
         <script>
  $(document).ready( function () {
		$('.table').DataTable();
  });

  $('#example').dataTable( {
  "pageLength": 100
} );
  </script>
         <script>
          // $("#basic_details").click();
          
                setInterval(loadPage,100);
      function loadPage() {
        var tststus = "<?= $tstatus ?>";
        var live = "live";
        $.ajax({
          url:"ajaxfile.php",
            data:{live:live},
            type:"POST",
            dataType:"json",
            success:function (res) {
              // console.log('res');
              var htmlcount = '';
              var html = '';
              if(res.length>0){
                for (var i = 0; i < res.length; i++) {
                  htmlcount++; 
                  // html+="<tr><td>"+res[i].tfn+"</td><td>"+res[i].forward+"</td><td>"+res[i].cc+" / "+res[i].count+"</td><td>"+res[i].cap2+" / "+res[i].cap1+"</td></tr>"
                  html+="<tr><td>"+res[i].number+"</td><td>"+res[i].starttime+"</td><td>"+res[i].campaign_name+"</td><td>"+res[i].repeate+"</td><td>"+res[i].call_status+"</td></tr>";
                }
                $('#c-live-count').css("color", "green");
                if (tststus !== '1') {
    var live_model = $("#exampleModallive")[0]; 
    if (live_model.style.display !== 'block') {
        // $("#basic_details").click();
        $("#cli_live_number").val(res[0].number);
    }
               }
              }else{
                html+="<tr><td colspan='6' align='center'>0</td></tr>";
                htmlcount+="0";
              }
              $('#c-live-count').html(htmlcount);
              $('.showlive').html(html);
            }
        })
      }
         </script>
           

         <!-- get Current amount -->
         <!-- <script>
                setInterval(loadamount,500);
      function loadamount() {
        var live = "live";
        $.ajax({
          url:"get_current_amount.php",
            data:{live:live},
            type:"POST",
            dataType:"json",
            success:function (result_amount) {
              $('#live-amount').html(result_amount);
            }
        })
      }
         </script> -->
         
         <script>
    setInterval(loadPagechannel, 100);
    function loadPagechannel() {
        var live_data = "live_data";
        $.ajax({
            url: "ajaxfiledata.php",
            data: {live_data: live_data},
            type: "POST",
            dataType: "json",
            success: function (resp) {
                // console.log(resp);
                var htmlcount = '';
                var html = '';
                if (resp.length > 0) {
                    for (var i = 0; i < resp.length; i++) {
                        htmlcount++;
                        html += "<tr><td>" + resp[i].campaign_id + "</td><td>" + resp[i].phnumber + "</td><td>" + resp[i].cli + "</td></tr>";
                    }
                } else {
                    html += "<tr><td colspan='3' align='center'>0</td></tr>";
                    htmlcount += "0";
                }
                $('#channel-live-count').html(htmlcount);
                $('.show_channel').html(html);
            }
        });
    }
</script>
