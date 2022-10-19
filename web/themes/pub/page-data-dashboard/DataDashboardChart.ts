export default class DataDashboardChart {

  constructor(labels, json, selector, horizontal = false) {
    let config = {
      type: horizontal ? "horizontalBar" : "bar",
      data: {
        labels: labels,
        datasets: []
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          [horizontal ? "yAxes" : "xAxes"]: [
            {
              gridLines: {
                display: false
              },
            }
          ],
          [horizontal ? "xAxes" : "yAxes"]: [
            {
              gridLines: {
                drawBorder: false,
                tickMarkLength: 0
              },
              ticks: {
                min: 0,
                max: 100,
                callback: function(value, index, values) {
                  return value + "%"
                },
                padding: 10,
              },
              position: "right"
            }
          ],
        },
        legend: false,
        animation: {
          duration: 350
        },
        tooltips: {
          displayColors: false,
          borderWidth: 1,
          borderColor: '#e5e5e5',
          backgroundColor: 'rgba(255,255,255,0.9)',
          cornerRadius: 8,
          xPadding: 20,
          yPadding: 25,
          titleFontColor: '#000',
          titleMarginBottom: 20,
          bodyFontColor: 'rgba(0,0,0,0.75)',
          bodyFontStyle: 'bold',
          callbacks: {
            title: function(tooltipItem, data) {
              let labelComponents = horizontal ? tooltipItem[0].yLabel : tooltipItem[0].xLabel as Array<string>
              return labelComponents.join(" ")
            },
            label: function(tooltipItem, data) {
              let value = horizontal ? tooltipItem.xLabel.toString() : tooltipItem.yLabel.toString()
              if(window.pageData.language == "fr") {
                value = value.replace('.', ',')
              }
              let label = data.datasets[tooltipItem.datasetIndex].label
              label += '   '
              label += value
              label += '%'
              return label
            }
          }
        }
      },
    };

    for(const i in json) {
      let row = json[i]
      let item = {
        label: row.label,
        data: row.values,
        backgroundColor: row.color,
        hoverBackgroundColor: row.color,
        trail: row.trail,
        hidden: true
      }
      config.data.datasets.push(item)
    }

    let ctx = document.querySelector(selector);
    let chart = new Chart(ctx, config);
    chart.data.datasets[0].hidden = false
    chart.update()

    const wrapper = ctx.closest(".chart")

    wrapper.querySelectorAll('input[type=checkbox]').forEach((el: HTMLInputElement) => {
      el.addEventListener("change", (e) => {
        let value = JSON.parse(el.getAttribute('value'))
        let result = chart.data.datasets.filter((a) => {
          for(const i in value) {
            if(a.trail[i] != value[i]) {
              return false
            }
          }
          return true
        })
        let dataset = result[0]
        dataset.hidden = false
        if(el.checked) {
          dataset.hidden = false
        }
        else {
          dataset.hidden = true
        }
        chart.update()
      })
    })

    wrapper.querySelectorAll('a.toggle-details').forEach((el: HTMLAnchorElement) => {
      el.addEventListener('click', (e) => {
        let factor = el.closest(".chart-factor")
        factor.classList.toggle('open')
      })
    })

  }

}
