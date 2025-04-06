document.addEventListener('DOMContentLoaded', () => {
    // 期待度データの取得
    const expectationGraphCanvas = document.getElementById('expectationGraph');
  if (!expectationGraphCanvas) {
    console.error('expectationGraph canvas not found');
    return;
  }

  // data-history-data 属性から JSON 文字列を取得し、パースする
  let historyData;
  try {
    historyData = JSON.parse(expectationGraphCanvas.dataset.historyData);
    console.log('Parsed historyData:', historyData);
  } catch (error) {
    console.error('Error parsing historyData:', error);
    return;
  }

  // historyData から期待度に関するデータを抽出する
  const rawData = historyData.map(item => item.avg_expectation);
  const labels = historyData.map(item => item.start_date);

  // Chart.js 用の設定
  const ctx = expectationGraphCanvas.getContext('2d');

  // データ調整用の関数
  function getAdjustedData(data, labels) {
      if (data.length === 1) {
          return {
              labels: ['x=0'],
              values: [data[0]]
          };
      } else {
          const newLabels = [];
          const newValues = [];
          newLabels.push('x=0');
          newValues.push(data[0]);
          if (data.length > 2) {
              const step = (data.length - 1) / (data.length - 2);
              for (let i = 1; i < data.length - 1; i++) {
                  newLabels.push(`x=${Math.round(i * step)}`);
                  newValues.push(data[i]);
              }
          }
          newLabels.push(`x=${data.length - 1}`);
          newValues.push(data[data.length - 1]);
          return { labels: newLabels, values: newValues };
      }
  }

  const adjustedData = getAdjustedData(rawData, labels);

  // グラデーションの設定
  const gradient = ctx.createLinearGradient(0, 0, 0, 250);
  gradient.addColorStop(0, 'rgba(255, 99, 132, 0.9)');
  gradient.addColorStop(1, 'rgba(255, 255, 255, 0.7)');

  let verticalLinePosition = null;

    const mChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'スコア',
                data: rawData,
                fill: true,
                borderColor: 'rgba(255, 99, 132, 0.5)',
                borderWidth: 2,
                backgroundColor: gradient,
                pointRadius: 1,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 30,
                    right: 30,
                    bottom: 10,
                    left: 30
                }
            },
            scales: {
                x: {
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 6
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    },
                },
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 25,
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return value;
                        }
                    },
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true
                }
            },
            onClick: (event) => {
                const points = mChart.getElementsAtEventForMode(event, 'nearest', { intersect: false }, true);
                if (points.length) {
                    const index = points[0].index;
                    verticalLinePosition = mChart.getDatasetMeta(0).data[index].x;
                    mChart.update();
                }
            }
        },
        plugins: [{
            id: 'drawVerticalLine',
            afterDraw: (chart) => {
                if (verticalLinePosition !== null) {
                    const chartArea = chart.chartArea;
                    const ctx = chart.ctx;
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(verticalLinePosition, chartArea.top);
                    ctx.lineTo(verticalLinePosition, chartArea.bottom);
                    ctx.strokeStyle = 'rgba(0, 255, 255, 0.3)';
                    ctx.lineWidth = 3;
                    ctx.stroke();
                    ctx.restore();
                }
            }
        },{
            id: 'customBackground',
            beforeDraw: (chart) => {
                const ctx = chart.ctx;
                const chartArea = chart.chartArea;
                ctx.beginPath();
                ctx.moveTo(chartArea.left, chartArea.bottom);
                ctx.lineTo(chartArea.right, chartArea.bottom);
                ctx.strokeStyle = 'red';
                ctx.lineWidth = 2;
                ctx.stroke();
            }
        }]
    });
});
