import {Page} from "../common/Page.ts"
import DataDashboardChart from "./DataDashboardChart.ts"

export class Barriers extends Page {

  constructor() {
    super()

    new DataDashboardChart(
      window.pageData.overviewKPILabels,
      window.pageData.overviewJson,
      "#chart"
    )

    new DataDashboardChart(
      window.pageData.breakdownKPILabels,
      window.pageData.breakdownJson,
      "#breakdown-chart",
      true
    )

  }

}
