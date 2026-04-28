import Chart from 'chart.js/auto';

function formatPln(value) {
    return `${Number(value).toFixed(2).replace('.', ',')} PLN`;
}

window.createGoldPricesChart = (canvas, labels = [], values = []) => {
    if (! canvas) {
        return null;
    }

    if (canvas.__goldPricesChart) {
        canvas.__goldPricesChart.destroy();
    }

    const darkMode = document.documentElement.classList.contains('dark');

    const chart = new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    data: values,
                    borderColor: darkMode ? '#f4f4f5' : '#18181b',
                    backgroundColor: darkMode ? 'rgba(244, 244, 245, 0.12)' : 'rgba(24, 24, 27, 0.08)',
                    pointBackgroundColor: darkMode ? '#f4f4f5' : '#18181b',
                    pointBorderWidth: 0,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    displayColors: false,
                    callbacks: {
                        label(context) {
                            return formatPln(context.parsed.y);
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: darkMode ? '#d4d4d8' : '#52525b',
                        maxRotation: 0,
                    },
                },
                y: {
                    grid: {
                        color: darkMode ? 'rgba(244, 244, 245, 0.10)' : 'rgba(24, 24, 27, 0.08)',
                    },
                    ticks: {
                        color: darkMode ? '#d4d4d8' : '#52525b',
                        callback(value) {
                            return formatPln(value);
                        },
                    },
                },
            },
        },
    });

    canvas.__goldPricesChart = chart;

    return chart;
};
