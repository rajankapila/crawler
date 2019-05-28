import React from "react";

import CrawlerResultStat from "./CrawlerResultStat";
import CrawlerResultTable from "./CrawlerResultTable";

class CrawlerResult extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      data: this.props.data
    };
  }

  componentWillReceiveProps = nextProps => {
    this.setState({ data: nextProps.data });
  };

  render() {
    const stats = [
      { label: "Number of Pages", value: this.state.data.page_count },
      { label: "Number of Unique Images", value: this.state.data.image_count },
      {
        label: "Number of Unique Internal Links",
        value: this.state.data.links_internal_count
      },
      {
        label: "Number of Unique External Links",
        value: this.state.data.links_external_count
      },
      {
        label: "Average Page Load(seconds)",
        value: this.state.data.average_page_load.toFixed(3)
      },
      {
        label: "Average Word Count",
        value: Math.round(this.state.data.average_word_count)
      },
      {
        label: "Average Title Length(chars)",
        value: Math.round(this.state.data.average_title_length)
      }
    ];

    return (
      <div className="crawler__result">
        <div className="crawler__result-stats">
          {stats.map(stat => {
            return <CrawlerResultStat stat={stat} />;
          })}
        </div>
        <CrawlerResultTable pages_crawled={this.state.data.pages_crawled} />
      </div>
    );
  }
}

export default CrawlerResult;
