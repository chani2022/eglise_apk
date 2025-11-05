$(function () {
    var element = document.querySelector('#chart-data')
    // console.log(element)
    if (!element) {
        return
    }

    let labels = []
    let datasets = []

    let data = element.dataset.statistique
    data = JSON.parse(data)

    for (let date in data) {
        labels.push(date)
        datasets.push(data[date])
    }
    Chart.defaults.global.defaultFontFamily = "rubik";
    Chart.defaults.global.defaultFontColor = '#999';
    Chart.defaults.global.defaultFontSize = '12';

    var ctx = document.querySelector('#chart').getContext('2d');

    var chart = new Chart(ctx, {
        type: 'line',
        // The data for our dataset
        data: {
            labels: labels,
            // Information about the dataset
            datasets: [{
                label: "Effectif",
                backgroundColor: 'rgba(0,0,0,0.05)',
                borderColor: '#4c1864',
                borderWidth: "3",
                data: datasets,
                pointRadius: 4,
                pointHoverRadius: 4,
                pointHitRadius: 10,
                pointBackgroundColor: "#fff",
                pointHoverBackgroundColor: "#fff",
                pointBorderWidth: "3",
            }]
        },

        // Configuration options
        options: {

            layout: {
                padding: 0,
            },

            legend: { display: false },
            title: { display: false },

            scales: {
                yAxes: [{
                    scaleLabel: {
                        display: false
                    },
                    gridLines: {
                        borderDash: [6, 6],
                        color: "#ebebeb",
                        lineWidth: 1,
                    },
                }],
                xAxes: [{
                    scaleLabel: { display: false },
                    gridLines: { display: false },
                }],
            },

            tooltips: {
                backgroundColor: '#333',
                titleFontSize: 12,
                titleFontColor: '#fff',
                bodyFontColor: '#fff',
                bodyFontSize: 12,
                displayColors: false,
                xPadding: 10,
                yPadding: 10,
                intersect: false
            }
        },
    });

})