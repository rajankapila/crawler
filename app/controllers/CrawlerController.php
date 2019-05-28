<?php

/**
 * Class CrawlerController - ajax controller which sends back crawled page data
 */
class CrawlerController extends ControllerBase {

    private $_baseURL = "https://agencyanalytics.com";

    /**
     * @number_of_pages {Int} Number of pages to crawl
     * @return mixed - returns CrawlerResponse on success
     */
    public function indexAction() {

        //check if post
        if ( $this->request->isPost() ) {

            //check if config environment dev for local testing
            if ($this->config->development == true) {
                $this->response->setHeader( 'Access-Control-Allow-Origin', '*' );
            }

            //get json data
            $data = $this->request->getJsonRawBody();
            //get number of pages variable from data
            $numberOfPages = $data->number_of_pages;

            //check if numeric
            if ( is_numeric( $numberOfPages ) && $numberOfPages > 0 && $numberOfPages < 6 ) {


                //set base link to start crawl
                $link            = "/";
                $crawler         = new CrawlerHelper();
                $crawlerResponse = $crawler->crawl( $this->_baseURL, $link, $numberOfPages );

                //return json data
                $this->response->setContent( json_encode( $crawlerResponse->getArray() ) );
            } else {
                //number of pages was not numeric
                $this->response->setStatusCode( 400, "Bad request" );
            }
        } else {
            //request was not a post
            $this->response->setStatusCode( 404, "Not Found" );
        }

        return $this->response;
    }
}

