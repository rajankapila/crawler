import React from "react";

class CrawlerResultStat extends React.Component {
  render() {
    return (
      <div className="crawler__result-stat">
        <div>
          <label>{this.props.stat.label}:</label>
        </div>
        <div className="crawler__result-stat-value">
          {this.props.stat.value}
        </div>
      </div>
    );
  }
}

export default CrawlerResultStat;
