<?php


class Crawl {

    private $url;
    private $code;
    private $timeTaken;
    private $errorMessage;
    private $response;
    private $totalWords;
    private $titleLength;

    function __construct() {

    }

    /**
     * @return mixed
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl( $url ) {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode( $code ) {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getTimeTaken() {
        return $this->timeTaken;
    }

    /**
     * @param mixed $timeTaken
     */
    public function setTimeTaken( $timeTaken ) {
        $this->timeTaken = $timeTaken;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * @param mixed $errorMessage
     */
    public function setErrorMessage( $errorMessage ) {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return mixed
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse( $response ) {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getTotalWords() {
        return $this->totalWords;
    }

    /**
     * @param mixed $totalWords
     */
    public function setTotalWords( $totalWords ) {
        $this->totalWords = $totalWords;
    }

    /**
     * @return mixed
     */
    public function getTitleLength() {
        return $this->titleLength;
    }

    /**
     * @param mixed $titleLength
     */
    public function setTitleLength( $titleLength ) {
        $this->titleLength = $titleLength;
    }


}