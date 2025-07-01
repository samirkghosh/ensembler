function renderLanguageChart(str_language) {
    try {
        var chart = new CanvasJS.Chart("language_container", {
            animationEnabled: true,
            title: {
                text: "Languages",
                fontFamily: "arial black",
                fontColor: "#695A42",
                fontSize: 15,
            },
            axisX: {
                labelAngle: 150
            },
            axisY: {
                include_onceZero: true
            },
            data: [
                str_language
            ]
        });
        chart.render();
    } catch (err) {
        console.log(err);
    }
}


      
function renderComplaintChart(str_complaint_cat) {
    try {
        var chart = new CanvasJS.Chart("chartContainer_Complaint", {
            animationEnabled: true,
            title: {
                text: "Category",
                fontFamily: "arial black",
                fontColor: "#695A42",
                fontSize: 15,
            },
            axisX: {
                labelAngle: 150
            },
            axisY: {
                title: "No of cases",
                include_onceZero: true
            },
            data: [
                str_complaint_cat
            ]
        });
        chart.render();
    } catch (err) {
        console.log(err);
    }
}

function renderCategoryChart(str_category) {
    try {
        console.log('*********Case category***********');
        var chart = new CanvasJS.Chart("chartContainer_category", {
            title: {
                text: "Sub Category",
                fontFamily: "arial black",
                fontColor: "#695A42",
                fontSize: 15,
            },
            animationEnabled: true,
            axisY: {
                title: "Case Count"
            },
            legend: {
                verticalAlign: "bottom",
                horizontalAlign: "center"
            },
            data: [
                {
                    type: "pie",
                    bevelEnabled: true,
                    legendMarkerColor: "grey",
                    dataPoints: str_category
                }
            ]
        });
        chart.render();
    } catch (err) {
        console.log(err);
    }
}
   

   $(document).ready(function(){
   var rand_no = Math.floor((3-1)*Math.random()) + 1;
   $("#btnConvert").on('click', function () {
      html2canvas(document.getElementById("html-content")).then(function (canvas) {                   
        var anchorTag = document.createElement("a");
        document.body.appendChild(anchorTag);
        // document.getElementById("previewImg").appendChild(canvas);
        anchorTag.download = "dashboard_"+rand_no+".jpg";
        anchorTag.href = canvas.toDataURL();
        anchorTag.target = '_blank';
        anchorTag.click();
      });
   });
});
