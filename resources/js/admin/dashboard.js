Chart.defaults.font.family = 'Poppins, sans-serif';

new Chart(document.getElementById('overviewChart'), {

    type: 'line',

    data: {

        labels: [
            'May 1',
            'May 6',
            'May 11',
            'May 16',
            'May 21',
            'May 26',
            'May 31'
        ],

        datasets: [

            {
                label: 'Registrations',
                data: [80, 120, 90, 150, 110, 140, 300],

                borderColor: '#5c1414',
                backgroundColor: 'rgba(92,20,20,0.15)',

                fill: true,
                tension: 0.4,

                borderWidth: 2,

                pointRadius: 3,
                pointHoverRadius: 7,

                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#5c1414',
                pointBorderWidth: 2,

                pointHoverBorderWidth: 3
            },

            {
                label: 'Active Users',
                data: [60, 90, 70, 180, 130, 160, 380],

                borderColor: '#e63946',
                backgroundColor: 'rgba(230,57,70,0.10)',

                fill: true,
                tension: 0.4,

                borderWidth: 2,

                pointRadius: 3,
                pointHoverRadius: 7,

                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#e63946',
                pointBorderWidth: 2,

                pointHoverBorderWidth: 3
            },

            {
                label: 'Active Sellers',
                data: [100, 150, 120, 200, 160, 190, 350],

                borderColor: '#f3a341',
                backgroundColor: 'rgba(243,163,65,0.15)',

                fill: true,
                tension: 0.4,

                borderWidth: 2,

                pointRadius: 3,
                pointHoverRadius: 7,

                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#f3a341',
                pointBorderWidth: 2,

                pointHoverBorderWidth: 3
            }

        ]

    },

    options: {

        responsive: true,
        maintainAspectRatio: false,

        interaction: {
            mode: 'index',
            intersect: false
        },

        animation: {
            duration: 1600,
            easing: 'easeOutQuart',

            onComplete: function () {
                this.canvas.style.transition = 'transform 0.3s ease';
            }
        },

        plugins: {

            legend: {

                position: 'top',
                align: 'start',

                labels: {

                    usePointStyle: true,
                    pointStyle: 'circle',

                    boxWidth: 8,
                    boxHeight: 8,

                    padding: 16,

                    font: {
                        size: 14
                    }
                }
            },

            tooltip: {

                enabled: true,

                backgroundColor: '#5c1414',

                titleColor: '#ffffff',
                bodyColor: '#ffffff',

                titleFont: {
                    size: 13,
                    weight: 'bold'
                },

                bodyFont: {
                    size: 13
                },

                padding: 12,

                cornerRadius: 10,

                displayColors: true,

                boxPadding: 4,

                callbacks: {

                    title: function (context) {
                        return context[0].label;
                    },

                    label: function (context) {
                        return ` ${context.dataset.label}: ${context.parsed.y}`;
                    }

                }
            }
        },

        scales: {

            y: {

                beginAtZero: true,

                grid: {
                    color: 'rgba(0,0,0,0.08)',
                    drawBorder: false
                },

                ticks: {
                    font: {
                        size: 13
                    },

                    color: '#6b7280'
                }
            },

            x: {

                grid: {
                    color: 'rgba(0,0,0,0.06)',
                    drawBorder: false
                },

                ticks: {

                    font: {
                        size: 13
                    },

                    color: '#6b7280'
                }
            }
        }
    }

});


new Chart(document.getElementById('complaintsChart'), {

    type: 'doughnut',

    data: {

        labels: [
            'Open',
            'In Progress',
            'Resolved'
        ],

        datasets: [{

            data: [20, 10, 45],

            backgroundColor: [
                '#5c1414',
                '#e63946',
                '#f3a98c'
            ],

            borderWidth: 0,

            hoverOffset: 8
        }]

    },

    options: {

        responsive: false,

        cutout: '70%',

        animation: {
            animateRotate: true,
            duration: 900,
            easing: 'easeOutQuart'
        },

        plugins: {

            legend: {
                display: false
            },

            tooltip: {

                enabled: true,

                backgroundColor: '#5c1414',

                titleColor: '#ffffff',
                bodyColor: '#ffffff',

                padding: 10,

                cornerRadius: 8,

                callbacks: {

                    label: function (context) {

                        const value = context.parsed;

                        return ` ${context.label}: ${value}`;
                    }

                }
            }
        }
    }

});


document.addEventListener('DOMContentLoaded', () => {

    setTimeout(() => {

        const overview =
            document.getElementById('overview-chart-container');

        if (overview) {
            overview.classList.remove(
                'opacity-0',
                'translate-y-3'
            );
        }

    }, 150);

});