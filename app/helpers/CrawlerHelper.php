<?php

/**
 * Class CrawlerHelper - class to crawl web pages
 */
class CrawlerHelper {

    private $_crawls;
    private $_linksCrawled;
    private $_images;
    private $_linksInternal;
    private $_linksExternal;
    private $_totalLinksCrawled;
    private $_totalTime;
    private $_totalWords;
    private $_totalTitleLength;
    private $_baseURL;

    function __construct() {
        $this->_crawls            = array();
        $this->_images            = array();
        $this->_linksCrawled      = array();
        $this->_linksInternal     = array();
        $this->_linksExternal     = array();
        $this->_totalLinksCrawled = 0;
        $this->_totalTime         = 0;
        $this->_totalWords        = 0;
        $this->_totalTitleLength  = 0;
        $this->_baseURL           = '';
    }

    /**
     * @param $baseUrl - base url of site to crawl
     * @param $link - start link
     * @param int $numberToCrawl - number of pages to crawl
     *
     * @return CrawlerResponse
     */

    public function crawl( $baseUrl, $link, $numberToCrawl = 5 ) {
        $this->_baseURL = $baseUrl;

        //crawl first link
        $this->_crawlURL( $link );

        //crawl other internal links found until number of links to crawl has been crawled
        $counter = 0;
        while ( $this->_totalLinksCrawled < $numberToCrawl ) {
            if ( isset( $this->_linksInternal[ $counter ] ) ) {
                //get next link
                $nextLink = $this->_linksInternal[ $counter ];

                //check to make sure not already crawled
                if ( ! in_array( $nextLink, $this->_linksCrawled ) ) {
                    $this->_crawlURL( $nextLink );
                }

                $counter ++;
            } else {
                break;
            }
        }

        //create response
        $crawlerResponse = new CrawlerResponse( $this->_crawls, $this->_totalLinksCrawled );
        $crawlerResponse->setImageCount( $this->_getNumberOfUniqueImages() );
        $crawlerResponse->setLinkExternalCount( $this->_getNumberOfUniqueExternalURLs() );
        $crawlerResponse->setLinkInternalCount( $this->_getNumberOfUniqueInternalURLs() );

        return $crawlerResponse;
    }

    /**
     * @param $content - content to check for unique images
     *
     * @return int
     */

    private function _getNumberOfUniqueImages() {
        return count( $this->_images );
    }

    /**
     * @return int - returns number internal links found
     */

    private function _getNumberOfUniqueInternalURLs() {
        return count( $this->_linksInternal );
    }

    /**
     * @return int - returns number of unique external links
     */

    private function _getNumberOfUniqueExternalURLs() {
        return count( $this->_linksExternal );
    }

    /**
     * @param $content - content to get title length from
     *
     * @return int
     */

    private function _getTitleLength( $content ) {
        //load cotent in DOMDocument object
        $dom = new DOMDocument( '1.0' );
        @$dom->loadHTML( $content );

        //get title tag
        $tags    = $dom->getElementsByTagName( 'title' );
        $element = $tags->item( 0 );

        //return length of title
        return strlen( $element->textContent );
    }

    /**
     * @param $content - content to check for unique images
     */

    private function _getImages( $content ) {
        //load cotent in DOMDocument object
        $dom = new DOMDocument( '1.0' );
        @$dom->loadHTML( $content );
        //get all img tags
        $images = $dom->getElementsByTagName( 'img' );

        //loop through and save unique image urls
        foreach ( $images as $element ) {
            $src = trim( $element->getAttribute( "src" ) );
            if ( $src && ! in_array( $src, $this->_images ) ) {
                $this->_images[] = $src;
            }
        }
    }

    /**
     * @param $content - content to find internal and external links
     */

    private function _loadLinks( $content ) {
        //load cotent in DOMDocument object
        $dom = new DOMDocument( '1.0' );
        @$dom->loadHTML( $content );
        //get all link tags
        $anchors = $dom->getElementsByTagName( 'a' );

        //loop through all tags found
        foreach ( $anchors as $element ) {
            $href = trim( $element->getAttribute( "href" ) );

            //check to make href is not empty and not a anchor link
            if ( $href && strpos( $href, "#" ) !== 0 ) {
                //check to see if link internal
                if ( strpos( $href, $this->_baseURL ) === 0 || strpos( $href, "/" ) === 0 ) {
                    if ( ! in_array( $href, $this->_linksInternal ) ) {
                        $this->_linksInternal[] = $href;
                    }
                } else {
                    if ( ! in_array( $href, $this->_linksExternal ) ) {
                        $this->_linksExternal[] = $href;
                    }
                }
            }
        }
    }

    /**
     * @param $link - create url from link and base url
     *
     * @return string
     */

    private function _createURL( $link ) {
        return $this->_baseURL . $link;
    }

    /**
     * @param $content - content to check word count
     *
     *
     * @return mixed - returns word count
     */

    private function _getWordCount( $content ) {
        //hack to remove script tags, was having trouble removing by tag in DOMDocument object
        $content = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $content );;

        //load cotent in DOMDocument object
        $dom = new DOMDocument( '1.0' );
        @$dom->loadHTML( $content );

        //get body DOM object
        $bodyArray = $dom->getElementsByTagName( 'body' );
        $body      = $bodyArray->item( 0 );

        //get body text
        $text = $plainText = $body->textContent;

        //return word cound
        return str_word_count( $text );
    }

    /**
     * @param $link - link to crawl
     */

    private function _crawlURL( $link ) {
        //create url from link
        $url = $this->_createURL( $link );
        //get url content
        $crawl = $this->_curlURL( $url );

        //add link to crawled list
        $this->_linksCrawled[] = $link;
        //increase links crawled count
        $this->_totalLinksCrawled ++;
        //get links in content
        $this->_loadLinks( $crawl->getResponse() );
        //get images in content
        $this->_getImages( $crawl->getResponse() );

        //set total words counted
        $crawl->setTotalWords( $this->_getWordCount( $crawl->getResponse() ) );

        //set title length
        $crawl->setTitleLength( $this->_getTitleLength( $crawl->getResponse() ) );

        $this->_crawls[] = $crawl;
    }

    /**
     * @param $url - url to crawl
     *
     * @return Crawl
     */

    private function _curlURL( $url ) {
        $errorMessage = "";

        //set url to get
        $handle = curl_init( $url );

        //set curl options
        curl_setopt( $handle, CURLOPT_URL, $url );
        curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $handle, CURLOPT_FAILONERROR, true );

        //get url content
        $response = curl_exec( $handle );

        //catch error
        if ( curl_error( $handle ) ) {
            $errorMessage = curl_error( $handle );
        }

        //record time taken
        $timeTaken = curl_getinfo( $handle, CURLINFO_TOTAL_TIME );

        //record code returned
        $code = curl_getinfo( $handle, CURLINFO_HTTP_CODE );

        curl_close( $handle );

        //create return object
        $crawl = new Crawl();
        $crawl->setCode( $code );
        $crawl->setErrorMessage( $errorMessage );
        $crawl->setResponse( $response );
        $crawl->setTimeTaken( $timeTaken );
        $crawl->setUrl( $url );

        return $crawl;
    }

}

