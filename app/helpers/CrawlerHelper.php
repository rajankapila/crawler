<?php

namespace Crawler;

/**
 * Class CrawlerHelper - class to crawl web pages
 */

class CrawlerHelper {

    private $crawls;
    private $linksCrawled;
    private $images;
    private $linksInternal;
    private $linksExternal;
    private $totalLinksCrawled;
    private $totalTime;
    private $totalWords;
    private $totalTitleLength;
    private $baseURL;

    function __construct() {
        $this->crawls            = array();
        $this->images            = array();
        $this->linksCrawled      = array();
        $this->linksInternal     = array();
        $this->linksExternal     = array();
        $this->totalLinksCrawled = 0;
        $this->totalTime         = 0;
        $this->totalWords        = 0;
        $this->totalTitleLength  = 0;
        $this->baseURL           = '';
    }

    /**
     * @param $baseUrl - base url of site to crawl
     * @param $link - start link
     * @param int $numberToCrawl - number of pages to crawl
     *
     * @return CrawlerResponse
     */

    public function crawl( $baseUrl, $link, $numberToCrawl = 5 ) {
        $this->baseURL = $baseUrl;

        //crawl first link
        $this->crawlURL( $link );

        //crawl other internal links found until number of links to crawl has been crawled
        $counter = 0;
        while ( $this->totalLinksCrawled < $numberToCrawl ) {
            if ( isset( $this->linksInternal[ $counter ] ) ) {
                //get next link
                $nextLink = $this->linksInternal[ $counter ];

                //check to make sure not already crawled
                if ( ! in_array( $nextLink, $this->linksCrawled ) ) {
                    $this->crawlURL( $nextLink );
                }

                $counter ++;
            } else {
                break;
            }
        }

        //create response
        $crawlerResponse = new CrawlerResponse( $this->crawls, $this->totalLinksCrawled );
        $crawlerResponse->setImageCount( $this->getNumberOfUniqueImages() );
        $crawlerResponse->setLinkExternalCount( $this->getNumberOfUniqueExternalURLs() );
        $crawlerResponse->setLinkInternalCount( $this->getNumberOfUniqueInternalURLs() );

        return $crawlerResponse;
    }

    /**
     * @param $content - content to check for unique images
     *
     * @return int
     */

    private function getNumberOfUniqueImages() {
        return count( $this->images );
    }

    /**
     * @return int - returns number internal links found
     */

    private function getNumberOfUniqueInternalURLs() {
        return count( $this->linksInternal );
    }

    /**
     * @return int - returns number of unique external links
     */

    private function getNumberOfUniqueExternalURLs() {
        return count( $this->linksExternal );
    }

    /**
     * @param $content - content to get title length from
     *
     * @return int
     */

    private function getTitleLength( $content ) {
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

    private function getImages( $content ) {
        //load cotent in DOMDocument object
        $dom = new DOMDocument( '1.0' );
        @$dom->loadHTML( $content );
        //get all img tags
        $images = $dom->getElementsByTagName( 'img' );

        //loop through and save unique image urls
        foreach ( $images as $element ) {
            $src = trim( $element->getAttribute( "src" ) );
            if ( $src && ! in_array( $src, $this->images ) ) {
                $this->images[] = $src;
            }
        }
    }

    /**
     * @param $content - content to find internal and external links
     */

    private function loadLinks( $content ) {
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
                if ( strpos( $href, $this->baseURL ) === 0 || strpos( $href, "/" ) === 0 ) {
                    if ( ! in_array( $href, $this->linksInternal ) ) {
                        $this->linksInternal[] = $href;
                    }
                } else {
                    if ( ! in_array( $href, $this->linksExternal ) ) {
                        $this->linksExternal[] = $href;
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

    private function createURL( $link ) {
        return $this->baseURL . $link;
    }

    /**
     * @param $content - content to check word count
     *
     *
     * @return mixed - returns word count
     */

    private function getWordCount( $content ) {
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

    private function crawlURL( $link ) {
        //create url from link
        $url = $this->createURL( $link );
        //get url content
        $crawl = $this->curlURL( $url );

        //add link to crawled list
        $this->linksCrawled[] = $link;
        //increase links crawled count
        $this->totalLinksCrawled ++;
        //get links in content
        $this->loadLinks( $crawl->getResponse() );
        //get images in content
        $this->getImages( $crawl->getResponse() );

        //set total words counted
        $crawl->setTotalWords( $this->getWordCount( $crawl->getResponse() ) );

        //set title length
        $crawl->setTitleLength( $this->getTitleLength( $crawl->getResponse() ) );

        $this->crawls[] = $crawl;
    }

    /**
     * @param $url - url to crawl
     *
     * @return Crawl
     */

    private function curlURL( $url ) {
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

