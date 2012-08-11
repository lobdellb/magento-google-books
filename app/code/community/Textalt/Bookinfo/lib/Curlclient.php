<?php 

class Textalt_Bookinfo_lib_Curlclient
{

    private $ch;
    private $cookie = "";
    private $html;
    private $location;

    private function _curlInit()
    {
        $this->ch = curl_init();

       // curl_setopt($this->ch, CURLOPT_HEADER, TRUE);
        curl_setopt($this->ch, CURLOPT_TIMEOUT_MS, 2000);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT_MS, 1000); 
        //curl_setopt($this->ch, CURLOPT_AUTOREFERER, TRUE);
        //curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, TRUE);
        // curl_setopt($ch, CURLOPT_USERAGENT, TRUE);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);

    }


    public function __construct() 
    {
        $this->_curlInit();
    }

    public function __destruct()
    {
        curl_close( $this->ch );
    }

    public function loadHtml($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url );

/*        if ( strlen( $this->cookie ) > 0 ) {
            curl_setopt($this->ch, CURLOPT_COOKIE, $this->cookie );
        } */

        $data = curl_exec($this->ch); 

        return $data;

    }


}
