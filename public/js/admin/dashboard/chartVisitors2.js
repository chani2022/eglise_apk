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
    console.log("xxxxxxx",data)
    // Récupérez la référence à l'élément tbody avec l'identifiant "resultats"
    const tbodyResultats = document.getElementById("resultats");

    // Parcourez les clés et valeurs de l'objet data
    for (const [key, value] of Object.entries(data)) {
        // Créez une nouvelle ligne <tr>
        const ligne = document.createElement("tr");

        // Créez les cellules <td> pour la clé et la valeur
        const celluleCle = document.createElement("td");
        celluleCle.textContent = key;
        const celluleValeur = document.createElement("td");
        celluleValeur.textContent = value;

        // Ajoutez les cellules à la ligne
        ligne.appendChild(celluleCle);
        ligne.appendChild(celluleValeur);

        // Ajoutez la ligne au tbody
        tbodyResultats.appendChild(ligne);
    }
    
    for (let date in data) {
        labels.push(date)
        datasets.push(data[date])
    }
    Chart.defaults.global.defaultFontFamily = "rubik";
    Chart.defaults.global.defaultFontColor = '#999';
    Chart.defaults.global.defaultFontSize = '12';
    

    $(function () {
        // Votre code existant...
    
        function generateTotal(data) {
            let total = 0;
            for (let date in data) {
                total += data[date];
            }
            return total;
        }
        
    
        Chart.defaults.global.defaultFontFamily = "rubik";
        Chart.defaults.global.defaultFontColor = '#999';
        Chart.defaults.global.defaultFontSize = '12';
    
        var ctx = document.querySelector('#chart').getContext('2d');
    
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: "Effectif",
                    backgroundColor: 'rgba(245, 66, 66, 0.3)',
                    borderColor: '#f54242',
                    borderWidth: "3",
                    data: datasets,
                    pointRadius: 4,
                    pointHoverRadius: 4,
                    pointHitRadius: 10,
                    pointBackgroundColor: "#f54242", // Couleur des points
                    pointHoverBackgroundColor: "#f54242", // Couleur des points au survol
                    pointBorderWidth: "3",
                    pointStyle: 'circle', // Style des points (cercle)
                    radius: 6, // Taille des points
                }]
            },
            options: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        fontColor: '#333',
                        fontSize: 14,
                        generateLabels: function(chart) {
                            const data = chart.data.datasets[0].data;
                            const total = generateTotal(data);
                            document.getElementById("totalEffectif").innerText = + total;
                            const lastValue = data[data.length - 1];
                            const derniereValeurGrapheElement = document.getElementById("derniereValeurGraphe");
                            derniereValeurGrapheElement.innerText =  + lastValue;
                            console.log("Dernier valeur",lastValue)
                            return [{
                                text: "Effectif",
                                fillStyle: 'red',
                                strokeStyle: '#f54242',
                                lineWidth: 3,
                                index: 0
                            }, {
                                text: "Total: " + total,
                                fillStyle: '#ffffff',
                                lineWidth: 0,
                                index: 1
                            }];
                        }
                    }
                },
                layout: {
                    padding: 0,
                },
                elements: {
                    point: {
                        radius: 6, // Taille des points
                        borderWidth: 3, // Épaisseur de la bordure des points
                        backgroundColor: '#f54242', // Couleur de fond des points
                        borderColor: '#f54242', // Couleur de la bordure des points
                    }
                },
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
                // Le reste de vos options...
            },
        });
    });

})