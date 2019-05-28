<?php

/**
 * Class CrawlerResponse - Response object to be send back via ajax to client with crawl data
 */

class CrawlerResponse {
    private $_pageCount;
    private $_pages;
    private $_imageCount;
    private $_linkInternalCount;
    private $_linkExternalCount;
    private $_averagePageLoad;
    private $_averageWordCount;
    private $_averageTitleLength;
    private $_crawls;

    function __construct( $crawls, $pageCount ) {
        $this->_pages = array();
        $this->setCrawls( $crawls );
        $this->setPageCount( $pageCount );

        $totalPageLoad    = 0;
        $totalWords       = 0;
        $totalTitleLength = 0;

        if ( $crawls ) {
            //loop through crawls to calculate crawl stats
            foreach ( $this->_crawls as $crawl ) {
                //sum total time taken for crawls
                $totalPageLoad += $crawl->getTimeTaken();
                //sum total words found
                $totalWords += $crawl->getTotalWords();
                //sum total title length
                $totalTitleLength += $crawl->getTitleLength();
                //add page url and response code
                $this->_pages[] = array( "url" => $crawl->getUrl(), "code" => $crawl->getCode() );
            }

            //calculate averages
            $this->setAveragePageLoad( $totalPageLoad / $this->_pageCount );
            $this->setAverageWordCount( $totalWords / $this->_pageCount );
            $this->setAverageTitleLength( $totalTitleLength / $this->_pageCount );
        }
    }

    /**
     * @return array - returns an array of response data to be sent back
     */

    public function getArray() {

        return array(
          "page_count"           => $this->getPageCount(),
          "image_count"          => $this->getImageCount(),
          "links_internal_count" => $this->getLinkInternalCount(),
          "links_external_count" => $this->getLinkExternalCount(),
          "average_page_load"    => $this->getAveragePageLoad(),
          "average_word_count"   => $this->getAverageWordCount(),
          "average_title_length" => $this->getAverageTitleLength(),
          "pages_crawled"        => $this->getPages()
        );
    }

    /**
     * @return mixed
     */
    public function getPageCount() {
        return $this->_pageCount;
    }

    /**
     * @param mixed $pageCount
     */
    public function setPageCount( $pageCount ) {
        $this->_pageCount = $pageCount;
    }

    /**
     * @return array
     */
    public function getPages() {
        return $this->_pages;
    }

    /**
     * @param array $pages
     */
    public function setPages( $pages ) {
        $this->_pages = $pages;
    }

    /**
     * @return mixed
     */
    public function getImageCount() {
        return $this->_imageCount;
    }

    /**
     * @param mixed $imageCount
     */
    public function setImageCount( $imageCount ) {
        $this->_imageCount = $imageCount;
    }

    /**
     * @return mixed
     */
    public function getLinkInternalCount() {
        return $this->_linkInternalCount;
    }

    /**
     * @param mixed $linkInternalCount
     */
    public function setLinkInternalCount( $linkInternalCount ) {
        $this->_linkInternalCount = $linkInternalCount;
    }

    /**
     * @return mixed
     */
    public function getLinkExternalCount() {
        return $this->_linkExternalCount;
    }

    /**
     * @param mixed $linkExternalCount
     */
    public function setLinkExternalCount( $linkExternalCount ) {
        $this->_linkExternalCount = $linkExternalCount;
    }

    /**
     * @return mixed
     */
    public function getAveragePageLoad() {
        return $this->_averagePageLoad;
    }

    /**
     * @param mixed $averagePageLoad
     */
    public function setAveragePageLoad( $averagePageLoad ) {
        $this->_averagePageLoad = $averagePageLoad;
    }

    /**
     * @return mixed
     */
    public function getAverageWordCount() {
        return $this->_averageWordCount;
    }

    /**
     * @param mixed $averageWordCount
     */
    public function setAverageWordCount( $averageWordCount ) {
        $this->_averageWordCount = $averageWordCount;
    }

    /**
     * @return mixed
     */
    public function getAverageTitleLength() {
        return $this->_averageTitleLength;
    }

    /**
     * @param mixed $averageTitleLength
     */
    public function setAverageTitleLength( $averageTitleLength ) {
        $this->_averageTitleLength = $averageTitleLength;
    }

    /**
     * @return mixed
     */
    public function getCrawls() {
        return $this->_crawls;
    }

    /**
     * @param mixed $crawls
     */
    public function setCrawls( $crawls ) {
        $this->_crawls = $crawls;
    }


}