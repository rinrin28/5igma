document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('bubbleChart');
    if (!(canvas instanceof HTMLCanvasElement)) {
        throw new Error('bubbleChartのCanvas要素が見つかりませんでした。');
    }
    const ctx = canvas.getContext('2d');
    if (!ctx) {
        throw new Error('2Dコンテキストが取得できませんでした。');
    }

    const matrixData = JSON.parse(canvas.dataset.matrix);

    const chart = new Chart(ctx, {
        type: 'bubble',
        data: {
            datasets: matrixData.map(point => ({
                label: point.label,
                data: [{ x: point.x, y: point.y, r: point.r }],
                backgroundColor: point.color,
                borderColor: point.color,
                borderWidth: 1,
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 20,
                    right: 30,
                    bottom: 0,
                    left: 90
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    position: 'bottom',
                    min: -4,
                    max: 4,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    },
                },
                y: {
                    min: 0,
                    max: 6,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return value <= 5 ? value : '';
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    },

                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const point = context.raw;
                            return [
                                `${context.dataset.label}`,
                                `期待度：${point.y.toFixed(2)}`,
                                `ギャップ：${point.x.toFixed(2)}`
                            ];
                        }
                    }
                }
            }
        },
        plugins: [{
            id: 'customBackground',
            beforeDraw: (chart) => {
                const ctx = chart.ctx;
                const chartArea = chart.chartArea;

                const gradientBlue = ctx.createLinearGradient(chartArea.left, chartArea.top, chartArea.left + chartArea.width / 2, chartArea.bottom);
                gradientBlue.addColorStop(0, 'rgba(0, 255, 255, 0.3)');
                gradientBlue.addColorStop(1, 'rgba(255, 255, 255, 0.3)');
                ctx.fillStyle = gradientBlue;
                ctx.fillRect(chartArea.left, chartArea.top, chartArea.width / 2, chartArea.height);

                const gradientRed = ctx.createLinearGradient(chartArea.right, chartArea.top, chartArea.left + chartArea.width / 2, chartArea.bottom);
                gradientRed.addColorStop(0, 'rgba(255, 99, 132, 0.3)');
                gradientRed.addColorStop(1, 'rgba(255, 255, 255, 0.3)');
                ctx.fillStyle = gradientRed;
                ctx.fillRect(chartArea.left + chartArea.width / 2, chartArea.top, chartArea.width / 2, chartArea.height);

                ctx.beginPath();
                ctx.moveTo(chartArea.left + chartArea.width / 2, chartArea.top);
                ctx.lineTo(chartArea.left + chartArea.width / 2, chartArea.bottom);
                ctx.strokeStyle = 'red';
                ctx.lineWidth = 2;
                ctx.stroke();

                ctx.beginPath();
                ctx.moveTo(chartArea.left, chartArea.bottom);
                ctx.lineTo(chartArea.right, chartArea.bottom);
                ctx.strokeStyle = 'red';
                ctx.lineWidth = 2;
                ctx.stroke();
            }
        }, {
            id: 'customLabel',
            afterDraw: (chart) => {
                const ctx = chart.ctx;
                const chartArea = chart.chartArea;

                ctx.save();
                ctx.fillStyle = '#000';
                ctx.font = 'bold 8px Arial';
                ctx.textAlign = 'light';
                ctx.fillText('期待度・', chartArea.left - 90 , chartArea.bottom);
                ctx.fillText('満足度ギャップ', chartArea.left - 90, chartArea.bottom + 10);
                ctx.restore();

                ctx.save();
                ctx.fillStyle = '#000';
                ctx.font = 'bold 8px Arial';
                ctx.textAlign = 'center';
                ctx.translate(chartArea.left + chartArea.width / 2 , chartArea.top -5);
                ctx.fillText('期待度', 0, 0);
                ctx.restore();

            }
        }]
    });
});
