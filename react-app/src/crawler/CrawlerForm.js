import React from "react";

class CrawlerForm extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      maxPages: 5,
      numberOfPages: 1,
      isCrawling: this.props.isCrawling
    };
  }

  componentWillReceiveProps = nextProps => {
    this.setState({ isCrawling: nextProps.isCrawling });
  };

  /**
   *  check if app has sent a crawling request
   */

  crawling() {
    if (this.state.isCrawling) {
      return (
        <div className="crawler__form-is-crawling">
          <i className="fas fa-spinner fa-pulse" />
          <div className="crawler__form-is-crawling-text">Crawling...</div>
        </div>
      );
    }
  }

  handleSelectChange = e => {
    this.setState({ numberOfPages: e.target.value });
  };

  handleSubmit = () => {
    this.props.handleSubmit(this.state.numberOfPages);
  };

  render() {
    return (
      <div className="crawler__form ">
        <div className="crawler__form-inner ">
          <div className="crawler__form-select ">
            <select
              className="custom-select"
              onChange={this.handleSelectChange}
            >
              {[...Array(this.state.maxPages)].map((x, i) => (
                <option key={i} value={i + 1}>
                  {i + 1} {i + 1 > 1 ? "pages" : "page"}
                </option>
              ))}
            </select>
          </div>
          <div className="crawler__form-submit">
            <input
              className="btn btn-primary btn-md"
              type="button"
              value="crawl"
              disabled={this.state.isCrawling ? true : false}
              onClick={() => {
                this.handleSubmit();
              }}
            />
          </div>
        </div>
        {this.crawling()}
      </div>
    );
  }
}

export default CrawlerForm;
