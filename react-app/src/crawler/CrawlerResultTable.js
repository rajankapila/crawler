import React from "react";

class CrawlerResultTable extends React.Component {
  render() {
    return (
      <div className="crawler__result-table">
        <div className="crawler__result-table-title">Pages Crawled</div>
        <table>
          <thead>
            <tr>
              <th>URL</th>
              <th>Code</th>
            </tr>
          </thead>
          <tbody>
            {this.props.pages_crawled.map((page, index) => {
              return (
                <tr key={index}>
                  <td className="crawler__result-table-url">{page.url}</td>
                  <td>{page.code}</td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    );
  }
}

export default CrawlerResultTable;
