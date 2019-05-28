import React from "react";

import CrawlerForm from "./CrawlerForm";
import CrawlerResult from "./CrawlerResult";

class Crawler extends React.Component {
  constructor(props) {
    super(props);
    this.state = { data: {}, isCrawling: false, crawlData: null };
  }

  /**
   * @numberOfPages {Integer} - number of pages to crawl
   * handle a crawl submit and retrieve the data
   */
  handleSubmit = numberOfPages => {
    //null the crawl data and set is crawling to true
    this.setState({ isCrawling: true, crawlData: null });

    //post to crawler and get data
    fetch("/crawler/", {
      method: "post",

      body: JSON.stringify({ number_of_pages: numberOfPages })
    })
      .then(res => res.json())
      .then(res => {
        //set the new state
        this.setState({ isCrawling: false, crawlData: res });
      })
      .catch(res => {
        alert("There was error with your request");
        console.log(res);
      });
  };

  /**
   * check if we have crawl data to display
   */
  renderCrawlResult = () => {
    if (this.state.crawlData) {
      return <CrawlerResult data={this.state.crawlData} />;
    }
  };

  render() {
    return (
      <div className="crawler ">
        <div className="card">
          <div className="crawler__title card-header">Crawler</div>
          <CrawlerForm
            handleSubmit={this.handleSubmit}
            isCrawling={this.state.isCrawling}
          />
          {this.renderCrawlResult()}
        </div>
      </div>
    );
  }
}

export default Crawler;
