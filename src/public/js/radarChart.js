document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('radarChart');
    if(!canvas) {
        throw new Error('Canvasが見つかりませんでした。');
    }
    const ctx = canvas.getContext('2d');
    if (!ctx) {
        throw new Error('2Dコンテキストが取得できませんでした。');
    }
    const labels = Array.from({ length: 16 }, (_, i) => `${i + 1}`);
    const previousData = JSON.parse(canvas.dataset.previous);
    const currentData = JSON.parse(canvas.dataset.current);


    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '今回調査',
                    data: currentData,
                    fill: true,
                    backgroundColor: 'rgba(255, 99, 132, 0.4)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    pointRadius: 0,
                },
                {
                    label: '前回調査',
                    data: previousData,
                    backgroundColor: 'rgba(0, 255, 255, 0.2)',
                    borderColor: 'rgba(0, 255, 255, 1)',
                    borderWidth: 1,
                    pointRadius: 0,
                },
            ]
        },
        options: {
            responsive: true,
            layout: {
                padding: {
                    top: 30,
                    right: 0,
                    bottom: 30,
                    left: 0
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    min: 1,
                    max: 5,
                    ticks: {
                        stepSize: 1,
                        display: false,
                    },
                    pointLabels: {
                        display: false,
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 10,
                        },
                    }
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        title: function (tooltipItems) {
                            const index = tooltipItems[0].dataIndex + 1;
                            return `設問${index}`;
                        },
                        label: function (tooltipItem) {
                            const value = tooltipItem.raw;
                            if (tooltipItem.datasetIndex === 0) {
                                return `今回調査: ${value}`;
                            } else {
                                return `前回調査: ${value}`;
                            }
                        }
                    }
                }
            }
        },
        plugins: [
            {
                id: 'customLabels',
                beforeDraw: (chart) => {
                    const ctx = chart.ctx;
                    const chartArea = chart.chartArea;
                    const centerX = (chartArea.left + chartArea.right) / 2;
                    const centerY = (chartArea.top + chartArea.bottom) / 2;
                    const radius = chart.scales.r.drawingArea;
                    ctx.save();

                    chart.data.labels.forEach((label, index) => {
                        const angle = (index * (Math.PI * 2)) / chart.data.labels.length - Math.PI / 2;
                        const x = centerX + Math.cos(angle) * (radius + 18);
                        const y = centerY + Math.sin(angle) * (radius + 18);

                        ctx.beginPath();
                        ctx.arc(x, y, 12, 0, Math.PI * 2);
                        ctx.fillStyle = '#dadee5';
                        ctx.fill();
                        ctx.strokeStyle = '#dadee5';
                        ctx.lineWidth = 0;

                        ctx.stroke();
                        ctx.fillStyle = '#00214D';
                        ctx.font = 'bold 14px Arial';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(label, x, y);
                    });
                    ctx.restore();
                }
            },
            {
                id: 'customLegendPosition',
                afterLayout: (chart) => {
                    const legend = chart.legend;
                    legend.top = chart.chartArea.top - 60;
                }
            }
        ],
    });
});
